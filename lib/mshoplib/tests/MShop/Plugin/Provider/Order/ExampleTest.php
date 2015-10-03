<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Plugin_Provider_Order_Example and MShop_Plugin_Provider_Base because abstract classes can not be tested directly
 */
class MShop_Plugin_Provider_Order_ExampleTest extends PHPUnit_Framework_TestCase
{
	private $order;
	private $plugin;


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
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setTypeId( 2 );
		$this->plugin->setProvider( 'Example' );
		$this->plugin->setConfig( array( 'key'=>1 ) );
		$this->plugin->setStatus( '1' );

		$priceItem = MShop_Price_Manager_Factory::createManager( $context )->createItem();
		$this->order = new MShop_Order_Item_Base_Default( $priceItem, $context->getLocale() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->order, $this->plugin );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_Example( TestHelper::getContext(), $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdate()
	{
		$object = new MShop_Plugin_Provider_Order_Example( TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'test' ) );
	}
}
