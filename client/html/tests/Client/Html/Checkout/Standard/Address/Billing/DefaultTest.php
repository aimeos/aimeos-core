<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Checkout_Standard_Address_Billing_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Client_Html_Checkout_Standard_Address_Billing_Default( $this->context, $paths );
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
		$customer = $this->getCustomerItem();
		$this->context->setUserId( $customer->getId() );

		$view = TestHelper::getView();
		$this->object->setView( $view );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-address-billing">', $output );
		$this->assertRegexp( '/form-item city.*form-item postal/smU', $output );

		$this->assertGreaterThan( 0, count( $view->billingMandatory ) );
		$this->assertGreaterThan( 0, count( $view->billingOptional ) );
	}


	public function testGetBodyAddressEU()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/html/common/partials/address', 'common/partials/address-eu.html' );

		$view = TestHelper::getView( 'unittest', $config );
		$this->object->setView( $view );

		$output = $this->object->getBody();
		$this->assertRegexp( '/form-item postal.*form-item city/smU', $output );
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
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.email' => 'me@localhost',
				'order.base.address.languageid' => 'en',
			),
		);
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$basket = Controller_Frontend_Basket_Factory::createController( $this->context )->get();
		$this->assertEquals( 'hamburg', $basket->getAddress( 'payment' )->getCity() );
	}


	public function testProcessNewAddressMissing()
	{
		$view = TestHelper::getView();

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
			),
		);
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		try
		{
			$this->object->process();
		}
		catch( Client_Html_Exception $e )
		{
			$this->assertEquals( 3, count( $view->billingError ) );
			$this->assertArrayHasKey( 'order.base.address.salutation', $view->billingError );
			$this->assertArrayHasKey( 'order.base.address.email', $view->billingError );
			$this->assertArrayHasKey( 'order.base.address.languageid', $view->billingError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessNewAddressUnknown()
	{
		$view = TestHelper::getView();

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
				'order.base.address.salutation' => 'mr',
				'order.base.address.firstname' => 'test',
				'order.base.address.lastname' => 'user',
				'order.base.address.address1' => 'mystreet 1',
				'order.base.address.postal' => '20000',
				'order.base.address.city' => 'hamburg',
				'order.base.address.email' => 'me@localhost',
				'order.base.address.languageid' => 'en',
				'order.base.address.flag' => '1',
			),
		);
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );
		$this->object->process();

		$basket = Controller_Frontend_Basket_Factory::createController( $this->context )->get();
		$this->assertEquals( 0, $basket->getAddress( 'payment' )->getFlag() );
	}


	public function testProcessNewAddressInvalid()
	{
		$view = TestHelper::getView();

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/address/validate/postal', '^[0-9]{5}$' );
		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$param = array(
			'ca_billingoption' => 'null',
			'ca_billing' => array(
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

		$this->object->setView( $view );

		try
		{
			$this->object->process();
		}
		catch( Client_Html_Exception $e )
		{
			$this->assertEquals( 1, count( $view->billingError ) );
			$this->assertArrayHasKey( 'order.base.address.postal', $view->billingError );
			return;
		}

		$this->fail( 'Expected exception not thrown' );
	}


	public function testProcessExistingAddress()
	{
		$customer = $this->getCustomerItem();
		$this->context->setUserId( $customer->getId() );

		$view = TestHelper::getView();

		$param = array( 'ca_billingoption' => $customer->getId() );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$this->context->setEditor( null );
		$basket = Controller_Frontend_Basket_Factory::createController( $this->context )->get();
		$this->assertEquals( 'Example company', $basket->getAddress( 'payment' )->getCompany() );
	}


	public function testProcessInvalidId()
	{
		$view = TestHelper::getView();

		$param = array( 'ca_billingoption' => -1 );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->process();
	}


	/**
	 * Returns the customer item for the given code
	 *
	 * @param string $code Unique customer code
	 * @throws Exception If no customer item is found
	 * @return MShop_Customer_Item_Iface Customer item object
	 */
	protected function getCustomerItem( $code = 'UTC001' )
	{
		$customerManager = MShop_Customer_Manager_Factory::createManager( $this->context );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new Exception( 'Customer item not found' );
		}

		return $customer;
	}
}
