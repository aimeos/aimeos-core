<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Checkout_Standard_Summary_Option_Terms_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Checkout_Standard_Summary_Option_Terms_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


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
		$this->_object = new Client_Html_Checkout_Standard_Summary_Option_Terms_Default( $this->_context, $paths );
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

		Controller_Frontend_Factory::clear();
		MShop_Factory::clear();
	}


	public function testGetHeader()
	{
		$this->_object->getHeader();
	}


	public function testGetBody()
	{
		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-summary-option-terms">', $output );
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


	public function testIsCachable()
	{
		$this->assertEquals( false, $this->_object->isCachable( Client_HTML_Abstract::CACHE_BODY ) );
		$this->assertEquals( false, $this->_object->isCachable( Client_HTML_Abstract::CACHE_HEADER ) );
	}


	public function testProcess()
	{
		$this->_object->process();
		$this->assertEquals( null, $this->_object->getView()->get( 'standardStepActive' ) );
	}


	public function testProcessOK()
	{
		$view = $this->_object->getView();

		$param = array(
			'cs-option-terms' => '1',
			'cs-option-terms-value' => '1',
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
		$this->assertEquals( null, $view->get( 'standardStepActive' ) );
	}


	public function testProcessInvalid()
	{
		$view = $this->_object->getView();

		$param = array(
			'cs-option-terms' => '1',
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
		$this->assertEquals( 'summary', $view->get( 'standardStepActive' ) );
		$this->assertEquals( true, $view->get( 'termsError' ) );
	}
}
