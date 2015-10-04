<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Checkout_Standard_Delivery_StandardTest extends PHPUnit_Framework_TestCase
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
		$this->context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Checkout_Standard_Delivery_Standard( $this->context, $paths );
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
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = TestHelper::getView();
		$this->object->setView( $view );
		$view->standardStepActive = 'delivery';
		$view->standardSteps = array( 'before', 'delivery', 'after' );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-delivery">', $output );

		$this->assertGreaterThan( 0, count( $view->deliveryServices ) );
		$this->assertGreaterThanOrEqual( 0, count( $view->deliveryServiceAttributes ) );
	}


	public function testGetBodyOtherStep()
	{
		$view = TestHelper::getView();
		$this->object->setView( $view );

		$output = $this->object->getBody();
		$this->assertEquals( '', $output );
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


	public function testProcess()
	{
		$this->object->process();
	}


	public function testProcessExistingId()
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->context );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $service = reset( $result ) ) === false ) {
			throw new Exception( 'Service item not found' );
		}

		$view = TestHelper::getView();

		$param = array(
			'c_deliveryoption' => $service->getId(),
		);
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$basket = Controller_Frontend_Basket_Factory::createController( $this->context )->get();
		$this->assertEquals( 'unitcode', $basket->getService( 'delivery' )->getCode() );
	}


	public function testProcessInvalidId()
	{
		$view = TestHelper::getView();

		$param = array( 'c_deliveryoption' => -1 );
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( 'Controller_Frontend_Service_Exception' );
		$this->object->process();
	}


	public function testProcessNotExistingAttributes()
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->context );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $service = reset( $result ) ) === false ) {
			throw new Exception( 'Service item not found' );
		}

		$view = TestHelper::getView();

		$param = array(
			'c_deliveryoption' => $service->getId(),
			'c_delivery' => array(
				$service->getId() => array(
					'notexisting' => 'invalid value',
				),
			),
		);
		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->process();
	}
}
