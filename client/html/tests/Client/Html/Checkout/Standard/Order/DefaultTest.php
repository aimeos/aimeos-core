<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Checkout_Standard_Order_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Client_Html_Checkout_Standard_Order_Default( $this->context, $paths );
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
		$view = TestHelper::getView();
		$view->standardStepActive = 'order';
		$this->object->setView( $view );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderOtherStep()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = TestHelper::getView();
		$view->standardStepActive = 'order';
		$view->paymentForm = new MShop_Common_Item_Helper_Form_Default( '', 'POST', array() );
		$this->object->setView( $view );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-order">', $output );
	}


	public function testGetBodyOtherStep()
	{
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


	public function testProcessOK()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->context );
		$baseManager = MShop_Order_Manager_Factory::createManager( $this->context )->getSubManager( 'base' );
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->context );


		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $serviceItem = reset( $result ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$controller->setService( 'payment', $serviceItem->getId() );
		$controller->setAddress( 'payment', array( 'order.base.address.languageid' => 'en' ) );
		$this->context->setUserId( '-1' );


		$view = TestHelper::getView();

		$param = array( 'cs_order' => 1 );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );
		$this->object->process();


		$search = $baseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.customerid', '-1' ) );
		$result = $baseManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order placed' );
		}

		$baseManager->deleteItem( $item->getId() );

		$this->assertInstanceOf( 'MShop_Order_Item_Iface', $view->orderItem );
		$this->assertEquals( $item->getId(), $view->orderItem->getBaseId() );
	}
}
