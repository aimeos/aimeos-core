<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Plugin\Provider\Decorator;


class SingletonTest extends \PHPUnit\Framework\TestCase
{
	private $mock;
	private $order;
	private $object;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();

		$priceItem = \Aimeos\MShop\Price\Manager\Factory::create( $context )->create();
		$this->order = new \Aimeos\MShop\Order\Item\Base\Standard( $priceItem, $context->getLocale() );

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::create( $context );
		$item = $pluginManager->create();


		$this->mock = $this->getMockBuilder( \Aimeos\MShop\Plugin\Provider\Decorator\Example::class )
			->disableOriginalConstructor()
			->setMethods( ['update', 'checkConfigBE', 'getConfigBE'] )
			->getMock();

		$this->object = new \Aimeos\MShop\Plugin\Provider\Decorator\Singleton( $context, $item, $this->mock );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->order, $this->mock );
	}


	public function testCheckConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'checkConfigBE' )->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 0, count( $result ) );
	}


	public function testGetConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( [] ) );

		$list = $this->object->getConfigBE();

		$this->assertEquals( 0, count( $list ) );
	}


	public function testUpdate()
	{
		$value = 'value';
		$this->mock->expects( $this->once() )->method( 'update' )->will( $this->returnValue( $value ) );

		$this->assertEquals( $value, $this->object->update( $this->order, 'test', $value ) );
	}
}
