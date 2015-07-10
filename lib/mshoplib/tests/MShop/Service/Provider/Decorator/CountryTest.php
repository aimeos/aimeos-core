<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Service_Provider_Decorator_Country.
 */
class MShop_Service_Provider_Decorator_CountryTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_basket;
	private $_context;
	private $_servItem;
	private $_mockProvider;


	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$servManager = MShop_Factory::createManager( $this->_context, 'service' );
		$this->_servItem = $servManager->createItem();

		$this->_mockProvider = $this->getMockBuilder( 'MShop_Service_Provider_Decorator_Country' )
			->disableOriginalConstructor()->getMock();

		$this->_basket = MShop_Order_Manager_Factory::createManager( $this->_context )
			->getSubManager( 'base' )->createItem();

		$this->_object = new MShop_Service_Provider_Decorator_Country( $this->_context, $this->_servItem, $this->_mockProvider );
	}


	protected function tearDown()
	{
		unset( $this->_object, $this->_basket, $this->_mockProvider, $this->_servItem, $this->_context );
	}


	public function testGetConfigBE()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'getConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->_object->getConfigBE();

		$this->assertArrayHasKey( 'country.billing-include', $result );
		$this->assertArrayHasKey( 'country.billing-exclude', $result );
		$this->assertArrayHasKey( 'country.delivery-include', $result );
		$this->assertArrayHasKey( 'country.delivery-exclude', $result );
	}


	public function testCheckConfigBE()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array(
			'country.billing-include' => ' DE, AT, CH ',
			'country.billing-exclude' => ' DE ,AT ,CH ',
			'country.delivery-include' => 'DE, AT, CH',
			'country.delivery-exclude' => 'DE ,AT ,CH',
		);
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'null', $result['country.billing-include'] );
		$this->assertInternalType( 'null', $result['country.billing-exclude'] );
		$this->assertInternalType( 'null', $result['country.delivery-include'] );
		$this->assertInternalType( 'null', $result['country.delivery-exclude'] );
	}


	public function testCheckConfigBENoConfig()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->_object->checkConfigBE( array() );

		$this->assertEquals( 4, count( $result ) );
		$this->assertInternalType( 'null', $result['country.billing-include'] );
		$this->assertInternalType( 'null', $result['country.billing-exclude'] );
		$this->assertInternalType( 'null', $result['country.delivery-include'] );
		$this->assertInternalType( 'null', $result['country.delivery-exclude'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array(
			'country.billing-include' => array(),
			'country.billing-exclude' => 1.5,
			'country.delivery-include' => array(),
			'country.delivery-exclude' => 1.5,
		);
		$result = $this->_object->checkConfigBE( $attributes );

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
		$this->_servItem->setConfig( $config );

		$this->_mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableNoConfig()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->_servItem->setConfig( array() );

		$this->_mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableNoIncludeBilling()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_servItem->setConfig( array( 'country.billing-include' => '' ) );

		$this->_mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableNoIncludeDelivery()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->_servItem->setConfig( array( 'country.delivery-include' => '' ) );

		$this->_mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableNoIncludeDeliveryFallback()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_servItem->setConfig( array( 'country.delivery-include' => '' ) );

		$this->_mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableNoExcludeBilling()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_servItem->setConfig( array( 'country.billing-exclude' => '' ) );

		$this->_mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableNoExcludeDelivery()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->_servItem->setConfig( array( 'country.delivery-exclude' => '' ) );

		$this->_mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableNoExcludeDeliveryFallback()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_servItem->setConfig( array( 'country.delivery-exclude' => '' ) );

		$this->_mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableExcludeBilling()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_servItem->setConfig( array( 'country.billing-exclude' => 'de' ) );

		$this->_mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableExcludeDelivery()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->_servItem->setConfig( array( 'country.delivery-exclude' => 'de' ) );

		$this->_mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableExcludeDeliveryFallback()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_servItem->setConfig( array( 'country.delivery-exclude' => 'de' ) );

		$this->_mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableIncludeBilling()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_servItem->setConfig( array( 'country.billing-include' => 'de' ) );

		$this->_mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableIncludeDelivery()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->_servItem->setConfig( array( 'country.delivery-include' => 'de' ) );

		$this->_mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableIncludeDeliveryFallback()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_servItem->setConfig( array( 'country.delivery-include' => 'de' ) );

		$this->_mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableIncludeBillingFailure()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_servItem->setConfig( array( 'country.billing-include' => 'ch' ) );

		$this->_mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableIncludeDeliveryFailure()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->_servItem->setConfig( array( 'country.delivery-include' => 'ch' ) );

		$this->_mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->_object->isAvailable( $this->_basket ) );
	}


	public function testIsAvailableIncludeDeliveryFailureFallback()
	{
		$address = MShop_Factory::createManager( $this->_context, 'order/base/address' )->createItem();
		$address->setCountryId( 'DE' );

		$this->_basket->setAddress( $address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_servItem->setConfig( array( 'country.delivery-include' => 'ch' ) );

		$this->_mockProvider->expects( $this->never() )->method( 'isAvailable' );

		$this->assertFalse( $this->_object->isAvailable( $this->_basket ) );
	}
}