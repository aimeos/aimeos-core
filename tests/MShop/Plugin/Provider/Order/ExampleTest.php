<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ExampleTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$plugin = \Aimeos\MShop::create( $context, 'plugin' )->create();
		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\Example( $context, $plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->order, $this->object );
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdate()
	{
		$this->assertEquals( null, $this->object->update( $this->order, 'test' ) );
	}
}
