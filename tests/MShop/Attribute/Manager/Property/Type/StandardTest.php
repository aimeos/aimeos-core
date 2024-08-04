<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2024
 */


namespace Aimeos\MShop\Attribute\Manager\Property\Type;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelper::context()->editor();
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'attribute' );
		$this->object = $manager->getSubManager( 'property' )->getSubManager( 'type' );
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


	public function testGetResourceType()
	{
		$this->assertContains( 'attribute/property/type', $this->object->getResourceType() );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testGet()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'attribute.property.type.code', 'size' ),
			$search->compare( '==', 'attribute.property.type.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );

		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false )
		{
			throw new \RuntimeException( 'No property type item found.' );
		}

		$actual = $this->object->get( $expected->getId() );

		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDelete()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'attribute.property.type.editor', $this->editor ) );
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


	public function testSearch()
	{
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'attribute.property.type.id', null );
		$expr[] = $search->compare( '!=', 'attribute.property.type.siteid', null );
		$expr[] = $search->compare( '==', 'attribute.property.type.domain', 'attribute' );
		$expr[] = $search->compare( '==', 'attribute.property.type.code', 'size' );
		$expr[] = $search->compare( '==', 'attribute.property.type.label', 'Size' );
		$expr[] = $search->compare( '>=', 'attribute.property.type.position', 0 );
		$expr[] = $search->compare( '==', 'attribute.property.type.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.property.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.property.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.property.type.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search )->toArray();
		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchAll()
	{
		$total = 0;
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '=~', 'attribute.property.type.code', '' ),
			$search->compare( '==', 'attribute.property.type.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->setSortations( [$search->sort( '-', 'attribute.property.type.position' )] );
		$search->slice( 0, 2 );
		$items = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 2, count( $items ) );
		$this->assertEquals( 3, $total );

		$pos = 2;
		foreach( $items as $itemId => $item )
		{
			$this->assertEquals( $itemId, $item->getId() );
			$this->assertEquals( $pos--, $item->getPosition() );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}
}
