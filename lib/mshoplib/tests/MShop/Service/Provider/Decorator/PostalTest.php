<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Test class for \Aimeos\MShop\Service\Provider\Decorator\Postal.
 */
class PostalTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Factory::createManager( $this->context, 'service' );
		$this->servItem = $servManager->createItem();

		$this->mockProvider = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Decorator\\Postal' )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )
			->getSubManager( 'base' )->createItem();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Postal( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'getConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'postal.billing-include', $result );
		$this->assertArrayHasKey( 'postal.billing-exclude', $result );
		$this->assertArrayHasKey( 'postal.delivery-include', $result );
		$this->assertArrayHasKey( 'postal.delivery-exclude', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'postal.billing-include' => ' 025698, 573789, 452168 ',
			'postal.billing-exclude' => ' 025698 ,573789 ,452168 ',
			'postal.delivery-include' => '025698, 573789, 452168',
			'postal.delivery-exclude' => '025698 ,573789 ,452168',
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'null', $result['postal.billing-include'] );
		$this->assertInternalType( 'null', $result['postal.billing-exclude'] );
		$this->assertInternalType( 'null', $result['postal.delivery-include'] );
		$this->assertInternalType( 'null', $result['postal.delivery-exclude'] );
	}


	public function testCheckConfigBENoConfig()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'null', $result['postal.billing-include'] );
		$this->assertInternalType( 'null', $result['postal.billing-exclude'] );
		$this->assertInternalType( 'null', $result['postal.delivery-include'] );
		$this->assertInternalType( 'null', $result['postal.delivery-exclude'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'postal.billing-include' => [],
			'postal.billing-exclude' => 1.5,
			'postal.delivery-include' => [],
			'postal.delivery-exclude' => 1.5,
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'string', $result['postal.billing-include'] );
		$this->assertInternalType( 'string', $result['postal.billing-exclude'] );
		$this->assertInternalType( 'string', $result['postal.delivery-include'] );
		$this->assertInternalType( 'string', $result['postal.delivery-exclude'] );
	}


	public function testIsAvailableNoAddresses()
	{
		$config = array(
			'postal.billing-include' => '',
			'postal.delivery-include' => '',
		);
		$this->servItem->setConfig( $config );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoConfig()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( [] );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeBilling()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postal.billing-include' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeDelivery()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'postal.delivery-include' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeDeliveryFallback()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postal.delivery-include' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExcludeBilling()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postal.billing-exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExcludeDelivery()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'postal.delivery-exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExcludeDeliveryFallback()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postal.delivery-exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeBilling()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postal.billing-exclude' => '025698' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeDelivery()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'postal.delivery-exclude' => '025698' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeDeliveryFallback()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postal.delivery-exclude' => '025698' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeBilling()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postal.billing-include' => '025698' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDelivery()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'postal.delivery-include' => '025698' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFallback()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postal.delivery-include' => '025698' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeBillingFailure()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postal.billing-include' => '452168' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFailure()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'postal.delivery-include' => '452168' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFailureFallback()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postal.delivery-include' => '452168' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}
}