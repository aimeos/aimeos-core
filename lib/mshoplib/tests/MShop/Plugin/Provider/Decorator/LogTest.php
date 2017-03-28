<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Plugin\Provider\Decorator;


/**
 * Test class for \Aimeos\MShop\Plugin\Provider\Decorator\Log.
 */
class LogTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $order;


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
		$item = $pluginManager->createItem();

		$provider = new \Aimeos\MShop\Plugin\Provider\Order\Example( $context, $item );

		$priceItem = \Aimeos\MShop\Price\Manager\Factory::createManager( $context )->createItem();
		$this->order = new \Aimeos\MShop\Order\Item\Base\Standard( $priceItem, $context->getLocale() );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Decorator\Log( $context, $item, $provider );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
		unset( $this->order );
	}


	public function testRegister()
	{
		$this->object->register( $this->order );
	}


	public function testUpdate()
	{
		$this->assertTrue( $this->object->update( $this->order, 'test', 'value' ) );
	}


	public function testUpdateNull()
	{
		$this->assertTrue( $this->object->update( $this->order, 'test' ) );
	}


	public function testUpdateArray()
	{
		$this->assertTrue( $this->object->update( $this->order, 'test', [] ) );
	}


	public function testUpdateObject()
	{
		$this->assertTrue( $this->object->update( $this->order, 'test', new \stdClass() ) );
	}
}
