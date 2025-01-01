<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2025
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


class NotTest extends \PHPUnit\Framework\TestCase
{
	private $provider;
	private $object;
	private $order;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$item = \Aimeos\MShop::create( $context, 'coupon' )->create();

		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off();

		$this->provider = $this->getMockBuilder( \Aimeos\MShop\Coupon\Provider\None::class )
			->setConstructorArgs( [$context, $item, 'abcd'] )
			->onlyMethods( ['isAvailable'] )
			->getMock();

		$this->object = new \Aimeos\MShop\Coupon\Provider\Decorator\Not( $this->provider, $context, $item, 'abcd' );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->provider, $this->order );
	}


	public function testIsAvailableFalse()
	{
		$this->provider->expects( $this->once() )->method( 'isAvailable' )->willReturn( true );
		$this->assertFalse( $this->object->isAvailable( $this->order ) );
	}


	public function testIsAvailableTrue()
	{
		$this->provider->expects( $this->once() )->method( 'isAvailable' )->willReturn( false );
		$this->assertTrue( $this->object->isAvailable( $this->order ) );
	}
}
