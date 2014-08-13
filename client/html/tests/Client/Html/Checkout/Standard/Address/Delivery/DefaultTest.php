<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Checkout_Standard_Address_Delivery_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Checkout_Standard_Address_Delivery_Default( $this->_context, $paths );
		$this->_object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		Controller_Frontend_Basket_Factory::createController( $this->_context )->clear();
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = $this->_object->getView();

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-address-delivery">', $output );

		$this->assertGreaterThan( 0, count( $view->deliveryMandatory ) );
		$this->assertGreaterThan( 0, count( $view->deliveryOptional ) );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$this->_object->process();
	}


	public function testProcessNewAddress()
	{
		$view = TestHelper::getView();

		$param = array(
			'ca-delivery-option' => 'null',
			'ca-delivery' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		$this->_object->process();

		$basket = Controller_Frontend_Basket_Factory::createController( $this->_context )->get();
		$this->assertEquals( 'hamburg', $basket->getAddress( 'delivery' )->getCity() );
	}


	public function testProcessNewAddressMissing()
	{
		$view = TestHelper::getView();

		$param = array(
			'ca-delivery-option' => 'null',
			'ca-delivery' => array(
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
			),
		);
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		try
		{
			$this->_object->process();
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
			'ca-delivery-option' => 'null',
			'ca-delivery' => array(
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
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );
		$this->_object->process();

		$basket = Controller_Frontend_Basket_Factory::createController( $this->_context )->get();
		$this->assertEquals( 0, $basket->getAddress( 'delivery' )->getFlag() );
	}


	public function testProcessNewAddressInvalid()
	{
		$view = TestHelper::getView();

		$config = $this->_context->getConfig();
		$config->set( 'client/html/common/address/validate/postal', '/^[0-9]{5}$/' );
		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$param = array(
			'ca-delivery-option' => 'null',
			'ca-delivery' => array(
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
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		try
		{
			$this->_object->process();
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
		$manager = MShop_Customer_Manager_Factory::createManager( $this->_context )->getSubManager( 'address' );
		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No customer address found' );
		}

		$item->setId( null );
		$manager->saveItem( $item );

		$view = TestHelper::getView();
		$this->_context->setUserId( $item->getRefId() );

		$param = array( 'ca-delivery-delete' => $item->getId() );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );
		$this->_object->process();

		$this->setExpectedException( 'MShop_Exception' );
		$manager->getItem( $item->getId() );
	}


	public function testProcessAddressDeleteUnknown()
	{
		$view = TestHelper::getView();

		$param = array( 'ca-delivery-delete' => '-1' );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->process();
	}


	public function testProcessAddressDeleteNoLogin()
	{
		$manager = MShop_Customer_Manager_Factory::createManager( $this->_context )->getSubManager( 'address' );
		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No customer address found' );
		}

		$view = TestHelper::getView();

		$param = array( 'ca-delivery-delete' => $item->getId() );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->process();
	}


	public function testProcessExistingAddress()
	{
		$customerManager = MShop_Customer_Manager_Factory::createManager( $this->_context );
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

		$this->_context->setUserId( $customer->getId() );

		$view = TestHelper::getView();

		$param = array( 'ca-delivery-option' => $address->getId() );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		$this->_object->process();

		$this->_context->setEditor( null );
		$basket = Controller_Frontend_Basket_Factory::createController( $this->_context )->get();
		$this->assertEquals( 'Metaways', $basket->getAddress( 'delivery' )->getCompany() );
	}


	public function testProcessInvalidId()
	{
		$view = TestHelper::getView();

		$param = array( 'ca-delivery-option' => -1 );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->process();
	}
}
