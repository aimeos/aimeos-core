<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MAdmin_Cache_Manager_NoneTest extends PHPUnit_Framework_TestCase
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
		$this->_object = new MAdmin_Cache_Manager_None( $this->_context );
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
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'cache.id', 'unittest' ) );
		$results = $this->_object->searchItems( $search );

		$this->assertEquals( array(), $results );
	}


	public function testGetItem()
	{
		$this->setExpectedException( 'MAdmin_Cache_Exception' );
		$this->_object->getItem( 'unittest' );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->_object->createItem();
		$this->_object->saveItem( $item );
		$this->_object->deleteItem( $item->getId() );
	}
}
