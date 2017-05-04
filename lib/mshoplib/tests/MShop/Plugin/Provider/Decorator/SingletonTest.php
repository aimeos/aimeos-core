<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Plugin\Provider\Decorator;


class SingletonTest extends \PHPUnit\Framework\TestCase
{
	private $mock;
	private $order;
	private $object;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$priceItem = \Aimeos\MShop\Price\Manager\Factory::createManager( $context )->createItem();
		$this->order = new \Aimeos\MShop\Order\Item\Base\Standard( $priceItem, $context->getLocale() );

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $context );
		$item = $pluginManager->createItem();


		$this->mock = $this->getMockBuilder( '\Aimeos\MShop\Plugin\Provider\Decorator\Example' )
			->disableOriginalConstructor()
			->setMethods( ['update'] )
			->getMock();

		$this->object = new \Aimeos\MShop\Plugin\Provider\Decorator\Singleton( $context, $item, $this->mock );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->order, $this->mock );
	}


	public function testUpdate()
	{
		$this->mock->expects( $this->once() )->method( 'update' )->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->update( $this->order, 'test', 'value' ) );
	}
}
