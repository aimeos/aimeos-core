<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


/**
 * Test class for MShop_Plugin_Provider_Decorator_Log.
 */
class MShop_Plugin_Provider_Decorator_LogTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_order;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$pluginManager = MShop_Plugin_Manager_Factory::createManager( $context );
		$item = $pluginManager->createItem();

		$provider = new MShop_Plugin_Provider_Order_Example( $context, $item );

		$priceItem = MShop_Price_Manager_Factory::createManager( $context )->createItem();
		$this->_order = new MShop_Order_Item_Base_Default( $priceItem, $context->getLocale() );

		$this->_object = new MShop_Plugin_Provider_Decorator_Log( $context, $item, $provider );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
		unset( $this->_order );
	}


	public function testRegister()
	{
		$this->_object->register( $this->_order );
	}


	public function testUpdate()
	{
		$this->assertTrue( $this->_object->update( $this->_order, 'test', 'value' ) );
	}


	public function testUpdateNull()
	{
		$this->assertTrue( $this->_object->update( $this->_order, 'test' ) );
	}


	public function testUpdateArray()
	{
		$this->assertTrue( $this->_object->update( $this->_order, 'test', array() ) );
	}


	public function testUpdateObject()
	{
		$this->assertTrue( $this->_object->update( $this->_order, 'test', new stdClass() ) );
	}
}
