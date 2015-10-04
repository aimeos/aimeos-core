<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Checkout_Standard_Address_Delivery_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Checkout_Standard_Address_Delivery_Standard( $this->context, $paths );
		$this->object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		Controller_Frontend_Basket_Factory::createController( $this->context )->clear();
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-address-delivery">', $output );

		$this->assertGreaterThan( 0, count( $view->deliveryMandatory ) );
		$this->assertGreaterThan( 0, count( $view->deliveryOptional ) );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$this->object->process();
	}


	public function testProcessNewAddress()
	{
		$view = TestHelper::getView();

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$basket = Controller_Frontend_Basket_Factory::createController( $this->context )->get();
		$this->assertEquals( 'hamburg', $basket->getAddress( 'delivery' )->getCity() );
	}


	public function testProcessNewAddressMissing()
	{
		$view = TestHelper::getView();

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
			),
		);
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		try
		{
			$this->object->process();
		}
		catch( Client_Html_Exception $e )
		{
			$this->assertEquals( 2, count( $view->deliveryError ) );
			$this->assertArrayHasKey( 'order.base.address.salutation', $view->deliveryError );
			$this->assertArrayHasKey( 'order.base.address.languageid', $view->deliveryError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessNewAddressUnknown()
	{
		$view = TestHelper::getView();

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.languageid' => 'en',
				'order.base.address.flag' => '1',
			),
		);
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );
		$this->object->process();

		$basket = Controller_Frontend_Basket_Factory::createController( $this->context )->get();
		$this->assertEquals( 0, $basket->getAddress( 'delivery' )->getFlag() );
	}


	public function testProcessNewAddressInvalid()
	{
		$view = TestHelper::getView();

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/address/validate/postal', '^[0-9]{5}$' );
		$helper = new MW_View_Helper_Config_Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$param = array(
			'ca_deliveryoption' => 'null',
			'ca_delivery' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20AB',
				'order.base.address.city' => 'hamburg',
				'order.base.address.email' => 'me@localhost',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		try
		{
			$this->object->process();
		}
		catch( Client_Html_Exception $e )
		{
			$this->assertEquals( 1, count( $view->deliveryError ) );
			$this->assertArrayHasKey( 'order.base.address.postal', $view->deliveryError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessAddressDelete()
	{
		$manager = MShop_Customer_Manager_Factory::createManager( $this->context )->getSubManager( 'address' );
		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No customer address found' );
		}

		$item->setId( null );
		$manager->saveItem( $item );

		$view = TestHelper::getView();
		$this->context->setUserId( $item->getRefId() );

		$param = array( 'ca_delivery_delete' => $item->getId() );
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );
		$this->object->process();

		$this->setExpectedException( 'MShop_Exception' );
		$manager->getItem( $item->getId() );
	}


	public function testProcessAddressDeleteUnknown()
	{
		$view = TestHelper::getView();

		$param = array( 'ca_delivery_delete' => '-1' );
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->process();
	}


	public function testProcessAddressDeleteNoLogin()
	{
		$manager = MShop_Customer_Manager_Factory::createManager( $this->context )->getSubManager( 'address' );
		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No customer address found' );
		}

		$view = TestHelper::getView();

		$param = array( 'ca_delivery_delete' => $item->getId() );
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->process();
	}


	public function testProcessExistingAddress()
	{
		$customerManager = MShop_Customer_Manager_Factory::createManager( $this->context );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new Exception( 'Customer item not found' );
		}

		$customerAddressManager = $customerManager->getSubManager( 'address' );
		$search = $customerAddressManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.address.refid', $customer->getId() ) );
		$result = $customerAddressManager->searchItems( $search );

		if( ( $address = reset( $result ) ) === false ) {
			throw new Exception( 'Customer address item not found' );
		}

		$this->context->setUserId( $customer->getId() );

		$view = TestHelper::getView();

		$param = array( 'ca_deliveryoption' => $address->getId() );
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$this->context->setEditor( null );
		$basket = Controller_Frontend_Basket_Factory::createController( $this->context )->get();
		$this->assertEquals( 'Example company', $basket->getAddress( 'delivery' )->getCompany() );
	}


	public function testProcessInvalidId()
	{
		$view = TestHelper::getView();

		$param = array( 'ca_deliveryoption' => -1 );
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->process();
	}
}
