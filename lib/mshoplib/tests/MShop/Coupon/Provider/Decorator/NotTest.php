<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2020
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


class NotTest extends \PHPUnit\Framework\TestCase
{
	private $provider;
	private $object;
	private $orderBase;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();
		$item = \Aimeos\MShop\Coupon\Manager\Factory::create( $context )->createItem();

		$this->orderBase = \Aimeos\MShop\Order\Manager\Factory::create( $context )
			->getSubmanager( 'base' )->createItem()->off();

		$this->provider = $this->getMockBuilder( \Aimeos\MShop\Coupon\Provider\Example::class )
			->setConstructorArgs( [$context, $item, 'abcd'] )
			->setMethods( ['isAvailable'] )
			->getMock();

		$this->object = new \Aimeos\MShop\Coupon\Provider\Decorator\Not( $this->provider, $context, $item, 'abcd' );
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
