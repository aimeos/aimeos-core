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
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$this->_object->getView()->paymentForm = new MShop_Common_Item_Helper_Form_Default( '', 'POST', array() );

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


	public function testProcessNoService()
	{
		$basketCntl = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_context );

		$view = TestHelper::getView();
		$view->orderBasket = $basketCntl->get();
		$view->orderItem = $orderManager->createItem();
		$this->_object->setView( $view );

		$this->_object->process();

		$this->assertEquals( 'http://baseurl/checkout/confirm/', $view->standardUrlNext );
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
		$view->orderBasket = $basketCntl->get();
		$this->_object->setView( $view );

		$this->_object->process();

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', array() ) ) );
		$this->assertEquals( 'POST', $view->standardMethod );
		$this->assertEquals( 'paymenturl', $view->standardUrlNext );
	}
}
