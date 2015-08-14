<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Checkout_Standard_Summary_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->_object = new Client_Html_Checkout_Standard_Summary_Default( $this->_context, $paths );
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
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );

		$view = TestHelper::getView();
		$view->standardStepActive = 'summary';
		$view->standardBasket = $controller->get();
		$this->_object->setView( $view );

		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderOtherStep()
	{
		$output = $this->_object->getHeader();
		$this->assertEquals( '', $output );
	}


	public function testGetBody()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );

		$view = TestHelper::getView();
		$view->standardStepActive = 'summary';
		$view->standardBasket = $controller->get();
		$view->standardSteps = array( 'before', 'summary' );
		$this->_object->setView( $view );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-summary common-summary">', $output );
	}


	public function testGetBodyOtherStep()
	{
		$output = $this->_object->getBody();
		$this->assertEquals( '', $output );
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
}
