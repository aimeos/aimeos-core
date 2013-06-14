<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Checkout_Standard_Order_Payment_DefaultTest extends MW_Unittest_Testcase
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

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Checkout_Standard_Order_Payment_DefaultTest');
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
		$this->_object = new Client_Html_Checkout_Standard_Order_Payment_Default( $this->_context, $paths );
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
		$this->_object->getHeader();
	}


	public function testGetBody()
	{
		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-order-payment" data-url="">', $output );
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


	public function testProcessNoService()
	{
		$view = TestHelper::getView();
		$this->_object->setView( $view );

		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->process();
	}


	public function testProcessNoOrder()
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->_context );

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $serviceItem = reset( $result ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$basketCntl = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$basketCntl->setService( 'payment', $serviceItem->getId() );

		$view = TestHelper::getView();
		$this->_object->setView( $view );

		$this->setExpectedException( 'MW_View_Exception' );
		$this->_object->process();
	}


	public function testProcessPrePay()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_context );
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->_context );

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $serviceItem = reset( $result ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$basketCntl = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$basketCntl->setService( 'payment', $serviceItem->getId() );

		$orderItem = $orderManager->createItem();
		$orderItem->setId( -1 );

		$view = TestHelper::getView();
		$view->orderItem = $orderItem;
		$this->_object->setView( $view );

		$this->_object->process();

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', array() ) ) );
		$this->assertInstanceOf( 'MShop_Common_Item_Helper_Form_Interface', $view->get( 'paymentForm' ) );
		$this->assertEquals( 'REDIRECT', $view->paymentForm->getMethod() );
		$this->assertEquals( 'baseurl/checkout/confirm/', $view->paymentForm->getUrl() );
	}


	public function testProcessPayPal()
	{
		$this->markTestSkipped( 'Connection to PayPal fails on Github' );

		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_context );
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->_context );

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'paypalexpress' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $serviceItem = reset( $result ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$basketCntl = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$basketCntl->setService( 'payment', $serviceItem->getId() );


		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2011-09-17 16:14:32' ) );
		$result = $orderManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order item found' );
		}

		$view = TestHelper::getView();
		$view->orderItem = $item;
		$this->_object->setView( $view );

		$this->_object->process();

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', array() ) ) );
		$this->assertInstanceOf( 'MShop_Common_Item_Helper_Form_Interface', $view->get( 'paymentForm' ) );
		$this->assertEquals( 'POST', $view->paymentForm->getMethod() );
		$this->assertEquals( 'baseurl/checkout/confirm/', $view->paymentForm->getUrl() );
	}
}
