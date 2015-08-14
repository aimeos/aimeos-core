<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MAdmin_Cache_Manager_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_object = new MAdmin_Cache_Manager_Default( $this->_context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MAdmin_Cache_Item_Interface', $this->_object->createItem() );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attr ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attr );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( 'MAdmin_Exception' );
		$this->_object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$siteid = $this->_context->getLocale()->getSiteId();

		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'cache.id', 'unittest' ) );
		$results = $this->_object->searchItems( $search );

		$this->assertEquals( 1, count( $results ) );

		foreach( $results as $id => $item )
		{
			$this->assertEquals( 'unittest', $id );
			$this->assertEquals( 'unittest', $item->getId() );
			$this->assertEquals( $siteid, $item->getSiteId() );
			$this->assertEquals( 'unit test value', $item->getValue() );
		}
	}


	public function testGetItem()
	{
		$siteid = $this->_context->getLocale()->getSiteId();
		$item = $this->_object->getItem( 'unittest' );

		$this->assertEquals( 'unittest', $item->getId() );
		$this->assertEquals( $siteid, $item->getSiteId() );
		$this->assertEquals( 'unit test value', $item->getValue() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->_object->createItem();
		$item->setId( 'unittest2' );
		$item->setValue( 'test2' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setValue( 'test3' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $item->getId() );

		$this->_object->deleteItem( $item->getId() );

		$this->assertEquals( 'unittest2', $item->getId() );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTimeExpire(), $itemSaved->getTimeExpire() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );
		$this->assertEquals( $item->getTags(), $itemSaved->getTags() );

		$this->assertEquals( $itemExp->getId(), $itemSaved->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $itemExp->getTimeExpire(), $itemUpd->getTimeExpire() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );
		$this->assertEquals( $itemExp->getTags(), $itemUpd->getTags() );

		$this->setExpectedException( 'MAdmin_Cache_Exception' );
		$this->_object->getItem( $item->getId() );
	}
}
