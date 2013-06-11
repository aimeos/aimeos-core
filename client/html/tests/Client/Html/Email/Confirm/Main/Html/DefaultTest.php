<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Email_Confirm_Main_Html_DefaultTest extends MW_Unittest_Testcase
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

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Email_Confirm_Main_Html_DefaultTest');
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
		$this->_object = new Client_Html_Email_Confirm_Main_Html_Default( $this->_context, $paths );
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
		$this->_object->setView( $this->_getView() );

		$output = $this->_object->getHeader();
	}


	public function testGetBody()
	{
		$view = $this->_getView();
		$view->mainBoundary = 'testboundary';
		$this->_object->setView( $view );

		$output = $this->_object->getBody();
file_put_contents( '/tmp/email', $output );
		$this->assertStringStartsWith( "\r\n--testboundary", $output );
	}


	public function testGetBodyPlain()
	{
		$settings = array( 'client' => array( 'html' => array(
			'email' => array( 'confirm' => array( 'main' => array( 'html' => array( 'encoded' => false ) ) ) )
		) ) );

		$view = $this->_getView();
		$helper = new MW_View_Helper_Config_Default( $view, new MW_Config_Array( $settings, array() ) );
		$view->addHelper( 'config', $helper );
		$this->_object->setView( $view );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( "<html>\n	<body>\n\n	<style type=\"text/css\">", $output );
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
		$this->_object->setView( $this->_getView() );

		$this->_object->process();
	}


	protected function _getView()
	{
		$view = TestHelper::getView();
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_context );

		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );
		$result = $orderManager->searchItems( $search );

		if( ( $orderItem = reset( $result ) ) === false ) {
			throw new Exception( 'No order found' );
		}

		$view->confirmOrderItem = $orderItem;
		$view->confirmOrderBaseItem = $orderManager->getSubManager( 'base' )->load( $orderItem->getBaseId() );
		$view->summaryBasket = $view->confirmOrderBaseItem;

		return $view;
	}
}
