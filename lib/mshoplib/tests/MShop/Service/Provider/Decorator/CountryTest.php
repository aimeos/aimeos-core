<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Test class for \Aimeos\MShop\Service\Provider\Decorator\Country.
 */
class CountryTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop::create( $this->context, 'service' );
		$this->servItem = $servManager->create();

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Country::class )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )
			->getSubManager( 'base' )->create();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Country( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'getConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'country.billing-include', $result );
		$this->assertArrayHasKey( 'country.billing-exclude', $result );
		$this->assertArrayHasKey( 'country.delivery-include', $result );
		$this->assertArrayHasKey( 'country.delivery-exclude', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'country.billing-include' => ' DE, AT, CH ',
			'country.billing-exclude' => ' DE ,AT ,CH ',
			'country.delivery-include' => 'DE, AT, CH',
			'country.delivery-exclude' => 'DE ,AT ,CH',
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertNull( $result['country.billing-include'] );
		$this->assertNull( $result['country.billing-exclude'] );
		$this->assertNull( $result['country.delivery-include'] );
		$this->assertNull( $result['country.delivery-exclude'] );
	}


	public function testCheckConfigBENoConfig()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 4, count( $result ) );
		$this->assertNull( $result['country.billing-include'] );
		$this->assertNull( $result['country.billing-exclude'] );
		$this->assertNull( $result['country.delivery-include'] );
		$this->assertNull( $result['country.delivery-exclude'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'country.billing-include' => [],
			'country.billing-exclude' => [],
			'country.delivery-include' => [],
			'country.delivery-exclude' => [],
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertIsString( $result['country.billing-include'] );
		$this->assertIsString( $result['country.billing-exclude'] );
		$this->assertIsString( $result['country.delivery-include'] );
		$this->assertIsString( $result['country.delivery-exclude'] );
	}


	public function testIsAvailableNoAddresses()
	{
		$config = array(
			'country.billing-include' => '',
			'country.delivery-include' => '',
		);
		$this->servItem->setConfig( $config );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoConfig()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( [] );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeBilling()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.billing-include' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeDelivery()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'country.delivery-include' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeDeliveryFallback()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.delivery-include' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExcludeBilling()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.billing-exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExcludeDelivery()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'country.delivery-exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExcludeDeliveryFallback()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.delivery-exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeBilling()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.billing-exclude' => 'de' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeDelivery()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'country.delivery-exclude' => 'de' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeDeliveryFallback()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.delivery-exclude' => 'de' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeBilling()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.billing-include' => 'de' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDelivery()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'country.delivery-include' => 'de' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFallback()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.delivery-include' => 'de' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeBillingFailure()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.billing-include' => 'ch' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFailure()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'country.delivery-include' => 'ch' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFailureFallback()
	{
		$address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create();
		$address->setCountryId( 'DE' );

		$this->basket->addAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.delivery-include' => 'ch' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}
}
