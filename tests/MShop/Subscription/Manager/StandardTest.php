<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2024
 */


namespace Aimeos\MShop\Subscription\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Subscription\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testAggregate()
	{
		$search = $this->object->filter()->add( 'subscription.editor', '==', 'core' );
		$result = $this->object->aggregate( $search, 'subscription.status' );

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( '0', $result );
		$this->assertArrayHasKey( '1', $result );
		$this->assertEquals( 1, $result['0'] );
		$this->assertEquals( 1, $result['1'] );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDelete()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetResourceType()
	{
		$this->assertContains( 'subscription', $this->object->getResourceType() );
	}


	public function testCreate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Subscription\Item\Iface::class, $this->object->create() );
	}


	public function testGet()
	{
		$search = $this->object->filter()->add( [
			'subscription.status' => 1,
			'subscription.editor' => $this->context->editor()
		] )->slice( 0, 1 );

		$expected = $this->object->search( $search )
			->first( new \RuntimeException( 'No subscription found in mshop_subscription with status "1"' ) );

		$actual = $this->object->get( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDelete()
	{
		$search = $this->object->filter()->add( [
			'subscription.status' => 1,
			'subscription.editor' => $this->context->editor()
		] )->slice( 0, 1 );

		$item = $this->object->search( $search )
			->first( new \RuntimeException( 'No subscription item found' ) );

		$item->setId( null );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setInterval( 'P0Y0M1W0D' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getOrderProductId(), $itemSaved->getOrderProductId() );
		$this->assertEquals( $item->getProductId(), $itemSaved->getProductId() );
		$this->assertEquals( $item->getDateNext(), $itemSaved->getDateNext() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getInterval(), $itemSaved->getInterval() );
		$this->assertEquals( $item->getPeriod(), $itemSaved->getPeriod() );
		$this->assertEquals( $item->getReason(), $itemSaved->getReason() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->context->editor(), $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getOrderProductId(), $itemUpd->getOrderProductId() );
		$this->assertEquals( $itemExp->getProductId(), $itemUpd->getProductId() );
		$this->assertEquals( $itemExp->getDateNext(), $itemUpd->getDateNext() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getInterval(), $itemUpd->getInterval() );
		$this->assertEquals( $itemExp->getPeriod(), $itemUpd->getPeriod() );
		$this->assertEquals( $itemExp->getReason(), $itemUpd->getReason() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->context->editor(), $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testFilter()
	{
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter() );
	}


	public function testFilterDefault()
	{
		$result = $this->object->filter( true );
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Expression\Combine\Iface::class, $result->getConditions() );
	}


	public function testFilterSite()
	{
		$result = $this->object->filter( false, true );
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Expression\Combine\Iface::class, $result->getConditions() );
	}


	public function testSearch()
	{
		$search = $this->object->filter();
		$siteid = $this->context->locale()->getSiteId();

		$expr = [];
		$expr[] = $search->compare( '!=', 'subscription.id', null );
		$expr[] = $search->compare( '==', 'subscription.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'subscription.orderid', null );
		$expr[] = $search->compare( '!=', 'subscription.ordprodid', null );
		$expr[] = $search->compare( '==', 'subscription.datenext', '2000-01-01' );
		$expr[] = $search->compare( '==', 'subscription.dateend', '2010-01-01' );
		$expr[] = $search->compare( '==', 'subscription.interval', 'P0Y1M0W0D' );
		$expr[] = $search->compare( '>=', 'subscription.productid', '' );
		$expr[] = $search->compare( '==', 'subscription.period', 120 );
		$expr[] = $search->compare( '==', 'subscription.reason', 1 );
		$expr[] = $search->compare( '==', 'subscription.status', 1 );
		$expr[] = $search->compare( '>=', 'subscription.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'subscription.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'subscription.editor', $this->context->editor() );

		$expr[] = $search->compare( '!=', 'order.address.id', null );
		$expr[] = $search->compare( '==', 'order.address.siteid', $siteid );
		$expr[] = $search->compare( '==', 'order.address.type', 'payment' );

		$total = 0;
		$result = $this->object->search( $search->add( $search->and( $expr ) ), [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchTotal()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'subscription.editor', $this->context->editor() ) );
		$items = $this->object->search( $search, [], $total );

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 2, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchRef()
	{
		$total = 0;
		$search = $this->object->filter()->add( 'subscription.dateend', '==', '2010-01-01' )->slice( 0, 1 );
		$item = $this->object->search( $search, ['order', 'order/product'], $total )->first();

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $item->getOrderItem() );
		$this->assertEquals( 4, count( $item->getOrderItem()->getProducts() ) );
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}
}
