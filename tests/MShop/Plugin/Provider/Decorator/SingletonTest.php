<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Plugin\Provider\Decorator;


class SingletonTest extends \PHPUnit\Framework\TestCase
{
	private $mock;
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$pluginManager = \Aimeos\MShop::create( $this->context, 'plugin' );
		$item = $pluginManager->create();


		$this->mock = $this->getMockBuilder( \Aimeos\MShop\Plugin\Provider\Decorator\Example::class )
			->disableOriginalConstructor()
			->onlyMethods( ['update', 'checkConfigBE', 'getConfigBE'] )
			->getMock();

		$this->object = new \Aimeos\MShop\Plugin\Provider\Decorator\Singleton( $this->context, $item, $this->mock );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->mock );
	}


	public function testCheckConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'checkConfigBE' )->willReturn( [] );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 0, count( $result ) );
	}


	public function testGetConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'getConfigBE' )->willReturn( [] );

		$list = $this->object->getConfigBE();

		$this->assertEquals( 0, count( $list ) );
	}


	public function testUpdate()
	{
		$value = 'value';
		$order = \Aimeos\MShop::create( $this->context, 'order' )->create()->off();

		$this->mock->expects( $this->once() )->method( 'update' )->willReturn( $value );

		$this->assertEquals( $value, $this->object->update( $order, 'test', $value ) );
	}
}
