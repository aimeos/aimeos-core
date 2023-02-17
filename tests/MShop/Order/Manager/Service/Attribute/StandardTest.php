<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Order\Manager\Service\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelper::context()->editor();
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Order\Manager\Service\Attribute\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter();
		$expr = array(
			$search->compare( '==', 'order.service.attribute.type', 'payment' ),
			$search->compare( '==', 'order.service.attribute.editor', 'core' ),
		);
		$search->setConditions( $search->and( $expr ) );

		$result = $this->object->aggregate( $search, 'order.service.attribute.code' )->toArray();

		$this->assertEquals( 9, count( $result ) );
		$this->assertArrayHasKey( 'ACOWNER', $result );
		$this->assertEquals( 1, $result['ACOWNER'] );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}



	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'order/service/attribute', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute )
		{
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testCreateItem()
	{
		$actual = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $actual );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter() );
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter( true ) );
	}


	public function testSearchItem()
	{
		$siteid = $this->context->locale()->getSiteId();

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.service.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.service.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.service.attribute.attributeid', null );
		$expr[] = $search->compare( '!=', 'order.service.attribute.parentid', null );
		$expr[] = $search->compare( '!=', 'order.service.attribute.type', '' );
		$expr[] = $search->compare( '==', 'order.service.attribute.code', 'NAME' );
		$expr[] = $search->compare( '==', 'order.service.attribute.price', '1.00' );
		$expr[] = $search->compare( '==', 'order.service.attribute.value', '"CreditCard"' );
		$expr[] = $search->compare( '==', 'order.service.attribute.name', 'payment method' );
		$expr[] = $search->compare( '>=', 'order.service.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.service.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.service.attribute.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $result ) );

		$conditions = array(
			$search->compare( '==', 'order.service.attribute.code', array( 'REFID', 'NAME' ) ),
			$search->compare( '==', 'order.service.attribute.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 1 );
		$total = 0;
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 2, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'order.service.attribute.code', 'REFID' ),
			$search->compare( '==', 'order.service.attribute.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( !( $item = reset( $results ) ) ) {
			throw new \RuntimeException( 'empty results' );
		}

		$actual = $this->object->get( $item->getId() );
		$this->assertEquals( $item->getId(), $actual->getId() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter()->add( ['order.service.attribute.code' => 'REFID'] );
		$item = $this->object->search( $search )->first( new \RuntimeException( 'empty search result' ) );

		$item->setId( null );
		$item->setCode( 'unittest1' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unittest1' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getAttributeId(), $itemSaved->getAttributeId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getName(), $itemSaved->getName() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );
		$this->assertEquals( $item->getPrice(), $itemSaved->getPrice() );
		$this->assertEquals( $item->getQuantity(), $itemSaved->getQuantity() );

		$this->assertEquals( $this->editor, $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getAttributeId(), $itemUpd->getAttributeId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getName(), $itemUpd->getName() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );
		$this->assertEquals( $itemExp->getPrice(), $itemUpd->getPrice() );
		$this->assertEquals( $itemExp->getQuantity(), $itemUpd->getQuantity() );

		$this->assertEquals( $this->editor, $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}
}
