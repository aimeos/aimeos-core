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
		$view->standardStepActive = 'payment';

		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'c_step' => 'address' ) );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );
		$output = $this->_object->getBody();

		$this->assertStringStartsWith( '<section class="aimeos checkout-standard">', $output );
		$this->assertRegExp( '#<ol class="steps">.*<li class="step.*>.*</li>.*</ol>#smU', $output );
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
