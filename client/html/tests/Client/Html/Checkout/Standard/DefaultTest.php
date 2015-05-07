<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Checkout_Standard_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Client_Html_Checkout_Standard_Default( $this->_context, $paths );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$this->_object->setView( TestHelper::getView() );
		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = TestHelper::getView();
		$view->standardStepActive = 'address';

		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'c_step' => 'payment' ) );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );
		$output = $this->_object->getBody();

		$this->assertStringStartsWith( '<section class="aimeos checkout-standard">', $output );
		$this->assertRegExp( '#<ol class="steps">.*<li class="step.*>.*</li>.*</ol>#smU', $output );
		$this->assertContains( '<section class="checkout-standard-address', $output );
		$this->assertNotContains( '<section class="checkout-standard-delivery', $output );
		$this->assertNotContains( '<section class="checkout-standard-payment', $output );
		$this->assertNotContains( '<section class="checkout-standard-summary', $output );
		$this->assertNotContains( '<section class="checkout-standard-order', $output );
	}


	public function testGetBodyOnepage()
	{
		$view = TestHelper::getView();

		$config = $this->_context->getConfig();
		$config->set( 'client/html/checkout/standard/onepage', array( 'address', 'delivery', 'payment', 'summary' ) );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$this->_object->setView( $view );
		$output = $this->_object->getBody();

		$this->assertContains( '<section class="checkout-standard-address', $output );
		$this->assertContains( '<section class="checkout-standard-delivery', $output );
		$this->assertContains( '<section class="checkout-standard-payment', $output );
		$this->assertContains( '<section class="checkout-standard-summary', $output );
		$this->assertNotContains( '<section class="checkout-standard-order', $output );
	}


	public function testGetBodyOnepagePartitial()
	{
		$view = TestHelper::getView();
		$view->standardStepActive = 'delivery';

		$config = $this->_context->getConfig();
		$config->set( 'client/html/checkout/standard/onepage', array( 'delivery', 'payment' ) );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$this->_object->setView( $view );
		$output = $this->_object->getBody();

		$this->assertContains( '<section class="checkout-standard-delivery', $output );
		$this->assertContains( '<section class="checkout-standard-payment', $output );
		$this->assertNotContains( '<section class="checkout-standard-address', $output );
		$this->assertNotContains( '<section class="checkout-standard-summary', $output );
		$this->assertNotContains( '<section class="checkout-standard-order', $output );
	}


	public function testGetBodyOnepageDifferentStep()
	{
		$view = TestHelper::getView();
		$view->standardStepActive = 'address';

		$config = $this->_context->getConfig();
		$config->set( 'client/html/checkout/standard/onepage', array( 'delivery', 'payment' ) );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$this->_object->setView( $view );
		$output = $this->_object->getBody();

		$this->assertContains( '<section class="checkout-standard-address', $output );
		$this->assertNotContains( '<section class="checkout-standard-delivery', $output );
		$this->assertNotContains( '<section class="checkout-standard-payment', $output );
		$this->assertNotContains( '<section class="checkout-standard-summary', $output );
		$this->assertNotContains( '<section class="checkout-standard-order', $output );
	}


	public function testGetSubClient()
	{
		$client = $this->_object->getSubClient( 'address', 'Default' );
		$this->assertInstanceOf( 'Client_HTML_Interface', $client );
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
}
