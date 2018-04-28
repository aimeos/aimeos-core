<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2017
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

		$this->assertArrayHasKey( 'postsl.billing-include', $result );
		$this->assertArrayHasKey( 'postsl.billing-exclude', $result );
		$this->assertArrayHasKey( 'postsl.delivery-include', $result );
		$this->assertArrayHasKey( 'postsl.delivery-exclude', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'postsl.billing-include' => ' 025698, 573789, 452168 ',
			'postsl.billing-exclude' => ' 025698 ,573789 ,452168 ',
			'postsl.delivery-include' => '025698, 573789, 452168',
			'postsl.delivery-exclude' => '025698 ,573789 ,452168',
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'null', $result['postsl.billing-include'] );
		$this->assertInternalType( 'null', $result['postsl.billing-exclude'] );
		$this->assertInternalType( 'null', $result['postsl.delivery-include'] );
		$this->assertInternalType( 'null', $result['postsl.delivery-exclude'] );
	}


	public function testCheckConfigBENoConfig()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'null', $result['postsl.billing-include'] );
		$this->assertInternalType( 'null', $result['postsl.billing-exclude'] );
		$this->assertInternalType( 'null', $result['postsl.delivery-include'] );
		$this->assertInternalType( 'null', $result['postsl.delivery-exclude'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'postsl.billing-include' => [],
			'postsl.billing-exclude' => 1.5,
			'postsl.delivery-include' => [],
			'postsl.delivery-exclude' => 1.5,
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'string', $result['postsl.billing-include'] );
		$this->assertInternalType( 'string', $result['postsl.billing-exclude'] );
		$this->assertInternalType( 'string', $result['postsl.delivery-include'] );
		$this->assertInternalType( 'string', $result['postsl.delivery-exclude'] );
	}


	public function testIsAvailableNoAddresses()
	{
		$config = array(
			'postsl.billing-include' => '',
			'postsl.delivery-include' => '',
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
		$this->servItem->setConfig( array( 'postsl.billing-include' => '' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeDelivery()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'postsl.delivery-include' => '' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeDeliveryFallback()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postsl.delivery-include' => '' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExcludeBilling()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postsl.billing-exclude' => '' ) );

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
		$this->servItem->setConfig( array( 'postsl.delivery-exclude' => '' ) );

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
		$this->servItem->setConfig( array( 'postsl.delivery-exclude' => '' ) );

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
		$this->servItem->setConfig( array( 'postsl.billing-exclude' => '025698' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeDelivery()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'postsl.delivery-exclude' => '025698' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeDeliveryFallback()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postsl.delivery-exclude' => '025698' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeBilling()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postsl.billing-include' => '025698' ) );

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
		$this->servItem->setConfig( array( 'postsl.delivery-include' => '025698' ) );

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
		$this->servItem->setConfig( array( 'postsl.delivery-include' => '025698' ) );

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
		$this->servItem->setConfig( array( 'postsl.billing-include' => '452168' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFailure()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'postsl.delivery-include' => '452168' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFailureFallback()
	{
		$address = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setPostal( '025698' );

		$this->basket->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'postsl.delivery-include' => '452168' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}
}