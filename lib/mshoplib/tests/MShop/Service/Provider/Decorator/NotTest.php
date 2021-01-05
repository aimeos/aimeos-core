<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class NotTest extends \PHPUnit\Framework\TestCase
{
	private $provider;
	private $object;
	private $orderBase;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();
		$item = \Aimeos\MShop\Service\Manager\Factory::create( $context )->create();

		$this->orderBase = \Aimeos\MShop\Order\Manager\Factory::create( $context )
			->getSubmanager( 'base' )->create()->off();

		$this->provider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Delivery\Standard::class )
			->setConstructorArgs( [$context, $item] )
			->setMethods( ['isAvailable'] )
			->getMock();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Not( $this->provider, $context, $item );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->provider, $this->orderBase );
	}


	public function testIsAvailableFalse()
	{
		$this->provider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertFalse( $this->object->isAvailable( $this->orderBase ) );
	}


	public function testIsAvailableTrue()
	{
		$this->provider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( false ) );
		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}
}
