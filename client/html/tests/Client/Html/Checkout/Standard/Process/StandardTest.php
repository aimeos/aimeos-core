<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015
 */

class Client_Html_Checkout_Standard_Process_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$this->context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Checkout_Standard_Process_Standard( $this->context, $paths );
		$this->object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		Controller_Frontend_Basket_Factory::createController( $this->context )->clear();
		MShop_Factory::setCache( false );
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$this->object->getView()->standardStepActive = 'process';

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-process">', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testProcessNoService()
	{
		$view = $this->object->getView();
		$param = array( 'c_step' => 'process' );
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$orderid = $this->getOrder( '2008-02-15 12:34:56' )->getId();
		$this->context->getSession()->set( 'aimeos/orderid', $orderid );

		$paths = TestHelper::getHtmlTemplatePaths();
		$mock = $this->getMockBuilder( 'Client_Html_Checkout_Standard_Process_Standard' )
			->setConstructorArgs( array( $this->context, $paths ) )
			->setMethods( array( 'getOrderServiceCode' ) )
			->getMock();

		$mock->expects( $this->once() )->method( 'getOrderServiceCode' )
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
		$mock = $this->getMockBuilder( 'MShop_Order_Manager_Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem', ) )
			->getMock();

		MShop_Factory::injectManager( $this->context, 'order', $mock );

		$view = $this->object->getView();
		$param = array( 'c_step' => 'process' );
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$orderid = $this->getOrder( '2009-03-18 16:14:32' )->getId();
		$this->context->getSession()->set( 'aimeos/orderid', $orderid );

		$this->object->process();

		$this->assertEquals( 0, count( $view->get( 'standardErrorList', array() ) ) );
		$this->assertEquals( 'POST', $view->standardMethod );
		$this->assertEquals( array(), $view->standardProcessParams );
		$this->assertEquals( true, $view->standardUrlExternal );
		$this->assertEquals( 'http://baseurl/checkout/standard/?c_step=payment', $view->standardUrlPayment );
	}


	public function testProcessNoStep()
	{
		$this->assertNull( $this->object->process() );
	}


	/**
	 * @param string $date
	 */
	protected function getOrder( $date )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $this->context );

		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $date ) );

		$result = $orderManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order found' );
		}

		return $item;
	}
}
