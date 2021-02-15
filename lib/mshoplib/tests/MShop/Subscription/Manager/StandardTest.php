<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MShop\Subscription\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();

		$this->object = new \Aimeos\MShop\Subscription\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'subscription.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'subscription.status' )->toArray();

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


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'subscription', $result );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Subscription\Item\Iface::class, $this->object->create() );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'subscription.status', 1 ),
			$search->compare( '==', 'subscription.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No subscription found in mshop_subscription with status "%1$s"', 1 ) );
		}

		$actual = $this->object->get( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'subscription.status', 1 ),
			$search->compare( '==', 'subscription.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No subscription item found.' );
		}

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

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

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

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter() );
	}


	public function testCreateSearchDefault()
	{
		$result = $this->object->filter( true );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\Iface::class, $result->getConditions() );
	}


	public function testCreateSearchSite()
	{
		$result = $this->object->filter( false, true );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\Iface::class, $result->getConditions() );
	}


	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'subscription.id', null );
		$expr[] = $search->compare( '==', 'subscription.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'subscription.ordbaseid', null );
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
		$expr[] = $search->compare( '==', 'subscription.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.address.id', null );
		$expr[] = $search->compare( '==', 'order.base.address.siteid', $siteid );
		$expr[] = $search->compare( '==', 'order.base.address.type', 'payment' );


		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemsTotal()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'subscription.editor', $this->editor ) );
		$items = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 2, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsRef()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'subscription.dateend', '2010-01-01' ) );
		$item = $this->object->search( $search, ['order/base', 'order/base/product'], $total )->first();

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $item->getBaseItem() );
		$this->assertEquals( 4, count( $item->getBaseItem()->getProducts() ) );
	}


	public function testGetSubManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}
}
