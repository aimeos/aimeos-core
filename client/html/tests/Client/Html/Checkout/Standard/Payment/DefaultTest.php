<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Checkout_Standard_Payment_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Client_Html_Checkout_Standard_Payment_Default( $this->_context, $paths );
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
		$view = TestHelper::getView();
		$view->standardStepActive = 'payment';
		$view->standardSteps = array( 'before', 'payment', 'after' );
		$this->_object->setView( $view );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-payment">', $output );
		$this->assertRegExp( '#<li class="form-item directdebit.accountowner mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="form-item directdebit.accountno mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="form-item directdebit.bankcode mandatory">#smU', $output );
		$this->assertRegExp( '#<li class="form-item directdebit.bankname mandatory">#smU', $output );

		$this->assertGreaterThan( 0, count( $view->paymentServices ) );
		$this->assertGreaterThanOrEqual( 0, count( $view->paymentServiceAttributes ) );
	}


	public function testGetBodyOtherStep()
	{
		$view = TestHelper::getView();
		$this->_object->setView( $view );

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


	public function testProcessExistingId()
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->_context );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $service = reset( $result ) ) === false ) {
			throw new Exception( 'Service item not found' );
		}

		$view = TestHelper::getView();

		$param = array(
			'c_paymentoption' => $service->getId(),
		);
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		$this->_object->process();

		$basket = Controller_Frontend_Basket_Factory::createController( $this->_context )->get();
		$this->assertEquals( 'unitpaymentcode', $basket->getService( 'payment' )->getCode() );
	}


	public function testProcessInvalidId()
	{
		$view = TestHelper::getView();

		$param = array( 'c_paymentoption' => -1 );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		$this->setExpectedException( 'Controller_Frontend_Service_Exception' );
		$this->_object->process();
	}


	public function testProcessNotExistingAttributes()
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->_context );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $service = reset( $result ) ) === false ) {
			throw new Exception( 'Service item not found' );
		}

		$view = TestHelper::getView();

		$param = array(
			'c_paymentoption' => $service->getId(),
			'c_payment' => array(
				$service->getId() => array(
					'notexisting' => 'invalid value',
				),
			),
		);
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->setView( $view );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->_object->process();
	}
}
