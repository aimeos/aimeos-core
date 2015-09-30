<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015
 */

class Client_Html_Checkout_Standard_Process_DefaultTest extends PHPUnit_Framework_TestCase
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
		MShop_Factory::setCache( true );

		$this->_context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Checkout_Standard_Process_Default( $this->_context, $paths );
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
		MShop_Factory::setCache( false );
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$this->_object->getView()->standardStepActive = 'process';

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-process">', $output );
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
		$view = $this->_object->getView();
		$param = array( 'c_step' => 'process' );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$orderid = $this->_getOrder( '2008-02-15 12:34:56' )->getId();
		$this->_context->getSession()->set( 'arcavias/orderid', $orderid );

		$paths = TestHelper::getHtmlTemplatePaths();
		$mock = $this->getMockBuilder( 'Client_Html_Checkout_Standard_Process_Default' )
			->setConstructorArgs( array( $this->_context, $paths ) )
			->setMethods( array( '_getOrderServiceCode' ) )
			->getMock();

		$mock->expects( $this->once() )->method( '_getOrderServiceCode' )
			->will( $this->returnValue( null ) );

		$mock->setView( $view );
		$mock->process();

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', array() ) ) );
		$this->assertEquals( 0, count( $view->get( 'standardProcessParams', array() ) ) );
		$this->assertEquals( 'GET', $view->standardMethod );
		$this->assertEquals( 'http://baseurl/checkout/standard/?c_step=payment', $view->standardUrlPayment );
	}


	public function testProcessDirectDebit()
	{
		$mock = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setConstructorArgs( array( $this->_context ) )
			->setMethods( array( 'saveItem', ) )
			->getMock();

		MShop_Factory::injectManager( $this->_context, 'order', $mock );

		$view = $this->_object->getView();
		$param = array( 'c_step' => 'process' );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$orderid = $this->_getOrder( '2009-03-18 16:14:32' )->getId();
		$this->_context->getSession()->set( 'arcavias/orderid', $orderid );

		$this->_object->process();

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', array() ) ) );
		$this->assertEquals( 'POST', $view->standardMethod );
		$this->assertEquals( array(), $view->standardProcessParams );
		$this->assertEquals( true, $view->standardUrlExternal );
		$this->assertEquals( 'http://baseurl/checkout/standard/?c_step=payment', $view->standardUrlPayment );
	}


	public function testProcessNoStep()
	{
		$this->assertNull( $this->_object->process() );
	}


	/**
	 * @param string $date
	 */
	protected function _getOrder( $date )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->_context );

		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $date ) );

		$result = $orderManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order found' );
		}

		return $item;
	}
}
