<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MAdmin_Cache_Manager_NoneTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = TestHelper::getContext();
		$this->object = new MAdmin_Cache_Manager_None( $this->context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MAdmin_Cache_Item_Iface', $this->object->createItem() );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attr ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Iface', $attr );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( 'MAdmin_Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'cache.id', 'unittest' ) );
		$results = $this->object->searchItems( $search );

		$this->assertEquals( array(), $results );
	}


	public function testGetItem()
	{
		$this->setExpectedException( 'MAdmin_Cache_Exception' );
		$this->object->getItem( 'unittest' );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->createItem();
		$this->object->saveItem( $item );
		$this->object->deleteItem( $item->getId() );
	}
}
