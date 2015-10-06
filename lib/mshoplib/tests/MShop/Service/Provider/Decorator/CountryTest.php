<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Service_Provider_Decorator_Country.
 */
class MShop_Service_Provider_Decorator_CountryTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp()
	{
		$this->context = TestHelper::getContext();

		$servManager = MShop_Factory::createManager( $this->context, 'service' );
		$this->servItem = $servManager->createItem();

		$this->mockProvider = $this->getMockBuilder( 'MShop_Service_Provider_Decorator_Country' )
			->disableOriginalConstructor()->getMock();

		$this->basket = MShop_Order_Manager_Factory::createManager( $this->context )
			->getSubManager( 'base' )->createItem();

		$this->object = new MShop_Service_Provider_Decorator_Country( $this->context, $this->servItem, $this->mockProvider );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'getConfigBE' )
			->will( $this->returnValue( array() ) );

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
			->will( $this->returnValue( array() ) );

		$attributes = array(
			'country.billing-include' => ' DE, AT, CH ',
			'country.billing-exclude' => ' DE ,AT ,CH ',
			'country.delivery-include' => 'DE, AT, CH',
			'country.delivery-exclude' => 'DE ,AT ,CH',
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'null', $result['country.billing-include'] );
		$this->assertInternalType( 'null', $result['country.billing-exclude'] );
		$this->assertInternalType( 'null', $result['country.delivery-include'] );
		$this->assertInternalType( 'null', $result['country.delivery-exclude'] );
	}


	public function testCheckConfigBENoConfig()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->object->checkConfigBE( array() );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'null', $result['country.billing-include'] );
		$this->assertInternalType( 'null', $result['country.billing-exclude'] );
		$this->assertInternalType( 'null', $result['country.delivery-include'] );
		$this->assertInternalType( 'null', $result['country.delivery-exclude'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array(
			'country.billing-include' => array(),
			'country.billing-exclude' => 1.5,
			'country.delivery-include' => array(),
			'country.delivery-exclude' => 1.5,
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'string', $result['country.billing-include'] );
		$this->assertInternalType( 'string', $result['country.billing-exclude'] );
		$this->assertInternalType( 'string', $result['country.delivery-include'] );
		$this->assertInternalType( 'string', $result['country.delivery-exclude'] );
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
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->servItem->setConfig( array() );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeBilling()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.billing-include' => '' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeDelivery()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'country.delivery-include' => '' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoIncludeDeliveryFallback()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.delivery-include' => '' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExcludeBilling()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.billing-exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExcludeDelivery()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'country.delivery-exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExcludeDeliveryFallback()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.delivery-exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeBilling()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.billing-exclude' => 'de' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeDelivery()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'country.delivery-exclude' => 'de' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeDeliveryFallback()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.delivery-exclude' => 'de' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeBilling()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.billing-include' => 'de' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDelivery()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'country.delivery-include' => 'de' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFallback()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.delivery-include' => 'de' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeBillingFailure()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.billing-include' => 'ch' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFailure()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->servItem->setConfig( array( 'country.delivery-include' => 'ch' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeDeliveryFailureFallback()
	{
		$address = MShop_Factory::createManager( $this->context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->servItem->setConfig( array( 'country.delivery-include' => 'ch' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}
}