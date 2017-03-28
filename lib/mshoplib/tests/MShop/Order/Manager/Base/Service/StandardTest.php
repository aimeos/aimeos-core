<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Order\Manager\Base\Service;


/**
 * Test class for \Aimeos\MShop\Order\Manager\Base\Service\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Order\Manager\Base\Service\Standard( $this->context );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.service.editor', 'core:unittest' ) );
		$result = $this->object->aggregate( $search, 'order.base.service.code' );

		$this->assertEquals( 4, count( $result ) );
		$this->assertArrayHasKey( 'OGONE', $result );
		$this->assertEquals( 2, $result['OGONE'] );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'order/base/service', $result );
		$this->assertContains( 'order/base/service/attribute', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute )
		{
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testCreateItem()
	{
		$actual = $this->object->createItem();
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $actual );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $this->object->createSearch() );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $this->object->createSearch( true ) );
	}


	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.base.service.id', null );
		$expr[] = $search->compare( '==', 'order.base.service.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.service.baseid', null );
		$expr[] = $search->compare( '!=', 'order.base.service.serviceid', null );
		$expr[] = $search->compare( '==', 'order.base.service.type', 'payment' );
		$expr[] = $search->compare( '==', 'order.base.service.code', 'OGONE' );
		$expr[] = $search->compare( '==', 'order.base.service.name', 'ogone' );
		$expr[] = $search->compare( '==', 'order.base.service.mediaurl', 'somewhere/thump1.jpg' );
		$expr[] = $search->compare( '==', 'order.base.service.price', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.rebate', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.taxrate', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.taxflag', 1 );
		$expr[] = $search->compare( '==', 'order.base.service.taxvalue', '0.00' );
		$expr[] = $search->compare( '>=', 'order.base.service.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.service.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.service.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.service.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.service.attribute.parentid', null );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.code', 'NAME' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.value', '"CreditCard"' );
		$expr[] = $search->compare( '>=', 'order.base.service.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.service.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.base.service.code', array( 'OGONE', 'not exists' ) ),
			$search->compare( '==', 'order.base.service.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 2, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'attribute' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'attribute', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'attribute', 'unknown' );
	}


	public function testGetSubManagerInvalidType()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( '$$$' );
	}


	public function testGetSubManagerInvalidDefaultName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'attribute', '$$$' );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.base.service.code', 'OGONE' ),
			$search->compare( '==', 'order.base.service.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( !( $item = reset( $results ) ) ) {
			throw new \RuntimeException( 'empty results' );
		}

		$actual = $this->object->getItem( $item->getId() );
		$this->assertEquals( $item, $actual );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Order\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.base.service.code', 'OGONE' ),
			$search->compare( '==', 'order.base.service.editor', $this->editor ),
			$search->compare( '==', 'order.base.service.attribute.code', 'NAME' ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$orderItems = $this->object->searchItems( $search );

		if( !( $item = reset( $orderItems ) ) ) {
			throw new \RuntimeException( 'empty search result' );
		}

		$item->setId( null );
		$item->setCode( 'unittest1' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );


		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unittest1' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertNotEquals( [], $item->getAttributes() );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getBaseId(), $itemSaved->getBaseId() );
		$this->assertEquals( $item->getServiceId(), $itemSaved->getServiceId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getName(), $itemSaved->getName() );
		$this->assertEquals( $item->getMediaUrl(), $itemSaved->getMediaUrl() );
		$this->assertEquals( $item->getPrice(), $itemSaved->getPrice() );


		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getBaseId(), $itemUpd->getBaseId() );
		$this->assertEquals( $itemExp->getServiceId(), $itemUpd->getServiceID() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getName(), $itemUpd->getName() );
		$this->assertEquals( $itemExp->getMediaUrl(), $itemUpd->getMediaUrl() );
		$this->assertEquals( $itemExp->getPrice(), $itemUpd->getPrice() );
		$this->assertEquals( [], $itemUpd->getAttributes() );


		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}
}
