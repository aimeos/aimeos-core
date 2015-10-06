<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class MShop_Order_Manager_Base_Service_Attribute_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = TestHelper::getContext()->getEditor();
		$this->context = TestHelper::getContext();
		$this->object = new MShop_Order_Manager_Base_Service_Attribute_Default( $this->context );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch();
		$expr = array(
			$search->compare( '==', 'order.base.service.attribute.type', 'payment' ),
			$search->compare( '==', 'order.base.service.attribute.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->aggregate( $search, 'order.base.service.attribute.code' );

		$this->assertEquals( 9, count( $result ) );
		$this->assertArrayHasKey( 'ACOWNER', $result );
		$this->assertEquals( 1, $result['ACOWNER'] );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute )
		{
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testCreateItem()
	{
		$actual = $this->object->createItem();
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Attribute_Interface', $actual );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->object->createSearch() );
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->object->createSearch( true ) );
	}


	public function testSearchItem()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'order.base.service.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.service.attribute.attributeid', null );
		$expr[] = $search->compare( '!=', 'order.base.service.attribute.serviceid', null );
		$expr[] = $search->compare( '!=', 'order.base.service.attribute.type', '' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.code', 'NAME' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.value', '"CreditCard"' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.name', 'payment method' );
		$expr[] = $search->compare( '>=', 'order.base.service.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.service.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $result ) );

		$conditions = array(
			$search->compare( '==', 'order.base.service.attribute.code', array( 'REFID', 'NAME' ) ),
			$search->compare( '==', 'order.base.service.attribute.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );
		$total = 0;
		$results = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 2, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.base.service.attribute.code', 'REFID' ),
			$search->compare( '==', 'order.base.service.attribute.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( !( $item = reset( $results ) ) ) {
			throw new Exception( 'empty results' );
		}

		$actual = $this->object->getItem( $item->getId() );
		$this->assertEquals( $item->getId(), $actual->getId() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();

		$conditions = array(
			$search->compare( '==', 'order.base.service.attribute.code', 'REFID' ),
			$search->compare( '==', 'order.base.service.attribute.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$orderItems = $this->object->searchItems( $search );

		if( !( $item = reset( $orderItems ) ) ) {
			throw new Exception( 'empty search result' );
		}

		$item->setId( null );
		$item->setCode( 'unittest1' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unittest2' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getAttributeId(), $itemSaved->getAttributeId() );
		$this->assertEquals( $item->getServiceId(), $itemSaved->getServiceId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );
		$this->assertEquals( $item->getName(), $itemSaved->getName() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getAttributeId(), $itemUpd->getAttributeId() );
		$this->assertEquals( $itemExp->getServiceId(), $itemUpd->getServiceId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );
		$this->assertEquals( $itemExp->getName(), $itemUpd->getName() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'unknown' );
	}
}