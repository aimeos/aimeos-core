<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Product\Manager\Type;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelper::context()->editor();
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'product' );
		$this->object = $manager->getSubManager( 'type' );
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
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $this->object->create() );
	}


	public function testSaveUpdateDelete()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'product.type.editor', $this->editor ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No type item found' );
		}

		$item->setId( null );
		$item->setCode( 'unitTestSave' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unitTestSave2' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testGet()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'product.type.code', 'default' ),
			$search->compare( '==', 'product.type.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$result = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No type item found for code "%1$s"', 'product' ) );
		}

		$actual = $this->object->get( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'product/type', $result );
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
		$expr[] = $search->compare( '!=', 'product.type.id', null );
		$expr[] = $search->compare( '!=', 'product.type.siteid', null );
		$expr[] = $search->compare( '==', 'product.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'product.type.code', 'select' );
		$expr[] = $search->compare( '==', 'product.type.label', 'Selection' );
		$expr[] = $search->compare( '>=', 'product.type.position', 0 );
		$expr[] = $search->compare( '==', 'product.type.status', 1 );
		$expr[] = $search->compare( '>=', 'product.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.type.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );


		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '~=', 'product.type.code', '' ),
			$search->compare( '==', 'product.type.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->setSortations( [$search->sort( '-', 'product.type.position' )] );
		$search->slice( 0, 2 );
		$items = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 2, count( $items ) );
		$this->assertEquals( 4, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}

}
