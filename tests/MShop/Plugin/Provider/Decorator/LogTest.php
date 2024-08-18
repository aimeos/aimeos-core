<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Plugin\Provider\Decorator;


class LogTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;


	protected function setUp() : void
	{
		$context = \TestHelper::context();

		$item = \Aimeos\MShop::create( $context, 'plugin' )->create();
		$provider = new \Aimeos\MShop\Plugin\Provider\Order\Example( $context, $item );

		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off();
		$this->object = new \Aimeos\MShop\Plugin\Provider\Decorator\Log( $context, $item, $provider );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->order );
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdate()
	{
		$value = 'value';
		$this->assertEquals( $value, $this->object->update( $this->order, 'test', $value ) );
	}


	public function testUpdateNull()
	{
		$this->assertEquals( null, $this->object->update( $this->order, 'test' ) );
	}


	public function testUpdateArray()
	{
		$value = [];
		$this->assertEquals( $value, $this->object->update( $this->order, 'test', $value ) );
	}


	public function testUpdateObject()
	{
		$value = new \stdClass();
		$this->assertEquals( $value, $this->object->update( $this->order, 'test', $value ) );
	}
}
