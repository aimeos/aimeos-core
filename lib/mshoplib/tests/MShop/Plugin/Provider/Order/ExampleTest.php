<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Test class for \Aimeos\MShop\Plugin\Provider\Order\Example and \Aimeos\MShop\Plugin\Provider\Base because abstract classes can not be tested directly
 */
class ExampleTest extends \PHPUnit_Framework_TestCase
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
		$context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setTypeId( 2 );
		$this->plugin->setProvider( 'Example' );
		$this->plugin->setConfig( array( 'key'=>1 ) );
		$this->plugin->setStatus( '1' );

		$priceItem = \Aimeos\MShop\Price\Manager\Factory::createManager( $context )->createItem();
		$this->order = new \Aimeos\MShop\Order\Item\Base\Standard( $priceItem, $context->getLocale() );
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
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Example( \TestHelperMShop::getContext(), $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdate()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Example( \TestHelperMShop::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'test' ) );
	}
}
