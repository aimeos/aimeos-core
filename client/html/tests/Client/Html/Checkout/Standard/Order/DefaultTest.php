<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Checkout_Standard_Order_DefaultTest extends MW_Unittest_Testcase
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

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Checkout_Standard_Order_DefaultTest');
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
		$this->_object = new Client_Html_Checkout_Standard_Order_Default( $this->_context, $paths );
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
		$view = TestHelper::getView();
		$view->standardStepActive = 'order';
		$this->_object->setView( $view );

		$this->_object->getHeader();
	}


	public function testGetHeaderOtherStep()
	{
		$output = $this->_object->getHeader();
		$this->assertEquals( '', $output );
	}


	public function testGetBody()
	{
		$view = TestHelper::getView();
		$view->standardStepActive = 'order';
		$view->paymentForm = new MShop_Common_Item_Helper_Form_Default( '', 'POST', array() );
		$this->_object->setView( $view );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-order">', $output );
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


	public function testIsCachable()
	{
		$this->assertEquals( false, $this->_object->isCachable( Client_HTML_Abstract::CACHE_BODY ) );
		$this->assertEquals( false, $this->_object->isCachable( Client_HTML_Abstract::CACHE_HEADER ) );
	}


	public function testProcess()
	{
		$this->_object->process();
	}


	public function testProcessOK()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$baseManager = MShop_Order_Manager_Factory::createManager( $this->_context )->getSubManager( 'base' );
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->_context );


		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $serviceItem = reset( $result ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$controller->setService( 'payment', $serviceItem->getId() );
		$controller->setAddress( 'payment', array( 'order.base.address.languageid' => 'en' ) );
		$this->_context->setUserId( '-1' );


		$view = TestHelper::getView();

		$param = array( 'cs-order' => 1 );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		$this->_object->process();


		$search = $baseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.customerid', '-1' ) );
		$result = $baseManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order placed' );
		}

		$baseManager->deleteItem( $item->getId() );

		$this->assertInstanceOf( 'MShop_Order_Item_Interface', $view->orderItem );
		$this->assertEquals( $item->getId(), $view->orderItem->getBaseId() );
	}
}
