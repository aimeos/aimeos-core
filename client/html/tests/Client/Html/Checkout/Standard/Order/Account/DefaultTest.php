<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

class Client_Html_Checkout_Standard_Order_Account_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;


	protected function setUp()
	{
		MShop_Factory::setCache( true );
		$this->_context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Checkout_Standard_Order_Account_Default( $this->_context, $paths );
		$this->_object->setView( TestHelper::getView() );
	}


	protected function tearDown()
	{
		MShop_Factory::clear();
		MShop_Factory::setCache( false );

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
		$output = $this->_object->getBody();
		$this->assertNotNull( $output );
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
		$type = MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT;
		$manager = MShop_Customer_Manager_Factory::createManager( $this->_context );

		$search = $manager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $manager->searchItems( $search );

		if( ( $customerItem = reset( $result ) ) === false ) {
			throw new Exception( 'No customer item found' );
		}

		$addrItem = $customerItem->getPaymentAddress();
		$addrItem->setEmail( 'unittest@aimeos.org' );

		$basketCntl = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$basketCntl->setAddress( $type, $addrItem );

		$view = TestHelper::getView();
		$view->orderBasket = $basketCntl->get();
		$this->_object->setView( $view );

		$orderBaseStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Default' )
			->setConstructorArgs( array( $this->_context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$customerStub = $this->getMockBuilder( 'MShop_Customer_Manager_Default' )
			->setConstructorArgs( array( $this->_context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$orderBaseStub->expects( $this->once() )->method( 'saveItem' );
		$customerStub->expects( $this->once() )->method( 'saveItem' );

		MShop_Factory::injectManager( $this->_context, 'customer', $customerStub );
		MShop_Factory::injectManager( $this->_context, 'order/base', $orderBaseStub );

		$this->_object->process();
	}
}
