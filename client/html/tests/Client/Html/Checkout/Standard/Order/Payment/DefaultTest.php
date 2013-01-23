<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Checkout_Standard_Order_Payment_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;
	protected $_context;


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
		$output = $this->_object->getHeader();
		$this->assertStringStartsWith( '<script type="text/javascript">', $output );
	}


	public function testGetBody()
	{
		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-order-payment">', $output );
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

		$this->_object->process();

		$this->assertGreaterThan( 0, count( $view->standardErrorList ) );
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

		$this->_object->process();

		$this->assertGreaterThan( 0, count( $view->standardErrorList ) );
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
		$this->assertEquals( 'baseurl/basket/confirm/?&arcavias=-1', $view->get( 'paymentUrl' ) );
		$this->assertEquals( null, $view->get( 'paymentForm' ) );
	}


	public function testProcessPayPal()
	{
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
print_r( $view->get( 'standardErrorList', array() ) );

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', array() ) ) );
		$this->assertEquals( 'baseurl/basket/confirm/?&arcavias=' . $item->getId(), $view->get( 'paymentUrl' ) );
		$this->assertInstanceOf( 'MShop_Common_Item_Helper_Form_Interface', $view->get( 'paymentForm' ) );
	}
}
