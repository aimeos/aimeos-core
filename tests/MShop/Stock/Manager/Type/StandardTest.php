<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\MShop\Stock\Manager\Type;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelper::context()->editor();
		$this->object = new \Aimeos\MShop\Stock\Manager\Type\Standard( \TestHelper::context() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testCreate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Type\Item\Iface::class, $this->object->create() );
	}


	public function testSaveUpdateDelete()
	{
		$search = $this->object->filter()->add( ['stock.type.code' => 'unitstock'] );
		$item = $this->object->search( $search )->first( new \RuntimeException( 'No item found' ) );

		$item->setId( null );
		$item->setCode( 'unit-test-type' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unit-test-type-2' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testFind()
	{
		$item = $this->object->find( 'unitstock', [], 'stock' );

		$this->assertEquals( 'unitstock', $item->getCode() );
	}


	public function testGet()
	{
		$search = $this->object->filter()->add( 'stock.type.editor', '==', $this->editor );
		$expected = $this->object->search( $search )->first( new \RuntimeException( 'No type item found' ) );

		$actual = $this->object->get( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testSearch()
	{
		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'stock.type.id', null );
		$expr[] = $search->compare( '!=', 'stock.type.siteid', null );
		$expr[] = $search->compare( '==', 'stock.type.domain', 'stock' );
		$expr[] = $search->compare( '==', 'stock.type.code', 'unitstock' );
		$expr[] = $search->compare( '>=', 'stock.type.position', 0 );
		$expr[] = $search->compare( '>=', 'stock.type.status', 0 );
		$expr[] = $search->compare( '>=', 'stock.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'stock.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'stock.type.editor', $this->editor );

		$search->add( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total );
		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchTotal()
	{
		$search = $this->object->filter()->add( 'stock.type.editor', '==', $this->editor );
		$search->setSortations( [$search->sort( '-', 'stock.type.position' )] );
		$search->slice( 0, 1 );

		$total = 0;
		$results = $this->object->search( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 2, $total );
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}

}
