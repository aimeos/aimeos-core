<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MShop\Subscription\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();

		$this->object = new \Aimeos\MShop\Subscription\Manager\Standard( $this->context );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'subscription.editor', 'core:unittest' ) );
		$result = $this->object->aggregate( $search, 'subscription.status' );

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( '0', $result );
		$this->assertArrayHasKey( '1', $result );
		$this->assertEquals( 1, $result['0'] );
		$this->assertEquals( 1, $result['1'] );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'subscription', $result );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Subscription\\Item\\Iface', $this->object->createItem() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'subscription.status', 1 ),
			$search->compare( '==', 'subscription.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No subscription found in mshop_subscription with status "%1$s"', 1 ) );
		}

		$actual = $this->object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MW\Common\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'subscription.status', 1 ),
			$search->compare( '==', 'subscription.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No subscription item found.' );
		}

		$item->setId( null );
		$resultSaved = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setInterval( 'P0Y0M1W0D' );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getOrderProductId(), $itemSaved->getOrderProductId() );
		$this->assertEquals( $item->getDateNext(), $itemSaved->getDateNext() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getInterval(), $itemSaved->getInterval() );
		$this->assertEquals( $item->getReason(), $itemSaved->getReason() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getOrderProductId(), $itemUpd->getOrderProductId() );
		$this->assertEquals( $itemExp->getDateNext(), $itemUpd->getDateNext() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getInterval(), $itemUpd->getInterval() );
		$this->assertEquals( $itemExp->getReason(), $itemUpd->getReason() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Iface', $resultSaved );
		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Iface', $resultUpd );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $this->object->createSearch() );
	}


	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'subscription.id', null );
		$expr[] = $search->compare( '==', 'subscription.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'subscription.ordbaseid', null );
		$expr[] = $search->compare( '!=', 'subscription.ordprodid', null );
		$expr[] = $search->compare( '==', 'subscription.datenext', '2000-01-01' );
		$expr[] = $search->compare( '==', 'subscription.dateend', '2010-01-01' );
		$expr[] = $search->compare( '==', 'subscription.interval', 'P0Y1M0W0D' );
		$expr[] = $search->compare( '==', 'subscription.reason', 1 );
		$expr[] = $search->compare( '==', 'subscription.status', 1 );
		$expr[] = $search->compare( '>=', 'subscription.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'subscription.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'subscription.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.address.id', null );
		$expr[] = $search->compare( '==', 'order.base.address.siteid', $siteid );
		$expr[] = $search->compare( '==', 'order.base.address.type', 'payment' );


		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'subscription.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );
		$total = 0;
		$items = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 2, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}
}
