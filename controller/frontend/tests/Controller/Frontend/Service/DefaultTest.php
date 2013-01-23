<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 896 2012-07-04 12:25:26Z nsendetzky $
 */

class Controller_Frontend_Service_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private static $_basket;


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('Controller_Frontend_Service_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	protected function setUp()
	{
		$this->_object = new Controller_Frontend_Service_Default( TestHelper::getContext() );
	}


	public static function setUpBeforeClass()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseMgr = $orderManager->getSubManager( 'base' );
		self::$_basket = $orderBaseMgr->createItem();
	}


	protected function tearDown()
	{
	}


	public function testGetServices()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$basket = $orderManager->getSubManager( 'base' )->createItem();

		$services = $this->_object->getServices( 'delivery', $basket );
		$this->assertGreaterThan( 0, count( $services ) );

		foreach( $services as $service ) {
			$this->assertInstanceOf( 'MShop_Service_Item_Interface', $service );
		}
	}


	public function testGetServicesCache()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$basket = $orderManager->getSubManager( 'base' )->createItem();

		$this->_object->getServices( 'delivery', $basket );
		$services = $this->_object->getServices( 'delivery', $basket );

		$this->assertGreaterThan( 0, count( $services ) );
	}


	public function testGetServiceAttributes()
	{
		$service = $this->_getServiceItem();
		$attributes = $this->_object->getServiceAttributes( 'delivery', $service->getId(), self::$_basket );

		$this->assertEquals( 0, count( $attributes ) );
	}


	public function testGetServiceAttributesCache()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$basket = $orderManager->getSubManager( 'base' )->createItem();

		$services = $this->_object->getServices( 'delivery', $basket );

		if( ( $service = reset( $services ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$attributes = $this->_object->getServiceAttributes( 'delivery', $service->getId(), self::$_basket );

		$this->assertEquals( 0, count( $attributes ) );
	}


	public function testGetServiceAttributesNoItems()
	{
		$this->setExpectedException( 'Controller_Frontend_Service_Exception' );
		$attributes = $this->_object->getServiceAttributes( 'invalid', -1, self::$_basket );
	}


	public function testGetServicePrice()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$basket = $orderManager->getSubManager( 'base' )->createItem();

		$service = $this->_getServiceItem();
		$price = $this->_object->getServicePrice( 'delivery', $service->getId(), $basket );

		$this->assertEquals( '12.95', $price->getValue() );
		$this->assertEquals( '1.99', $price->getShipping() );
	}


	public function testGetServicePriceCache()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$basket = $orderManager->getSubManager( 'base' )->createItem();

		$services = $this->_object->getServices( 'delivery', $basket );

		if( ( $service = reset( $services ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$price = $this->_object->getServicePrice( 'delivery', $service->getId(), $basket );

		$this->assertEquals( '12.95', $price->getValue() );
		$this->assertEquals( '1.99', $price->getShipping() );
	}


	public function testGetServicePriceNoItems()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$basket = $orderManager->getSubManager( 'base' )->createItem();

		$this->setExpectedException( 'Controller_Frontend_Service_Exception' );
		$attributes = $this->_object->getServicePrice( 'invalid', -1, $basket );
	}


	public function testCheckServiceAttributes()
	{
		$service = $this->_getServiceItem();
		$attributes = $this->_object->checkServiceAttributes( 'delivery', $service->getId(), array() );

		$this->assertEquals( array(), $attributes );
	}


	protected function _getServiceItem()
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $serviceManager->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'service.provider', 'Default' ),
			$search->compare( '==', 'service.type.domain', 'service' ),
			$search->compare( '==', 'service.type.code', 'delivery' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$services = $serviceManager->searchItems( $search );

		if( ( $service = reset( $services ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		return $service;
	}
}
