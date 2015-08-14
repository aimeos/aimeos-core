<?php

/**
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @copyright Aimeos (aimeos.org), 2015
 */

class Client_Html_Checkout_Confirm_Order_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->_context = clone TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Checkout_Confirm_Order_Default( $this->_context, $paths );
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
		$view = $this->_object->getView();
		$customer = $this->_getCustomerItem( 'UTC001' );
		$view->confirmOrderItem = $this->_getOrderItem( $customer->getId() );

		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = $this->_object->getView();
		$customer = $this->_getCustomerItem( 'UTC001' );
		$view->confirmOrderItem = $this->_getOrderItem( $customer->getId() );

		$output = $this->_object->getBody();

		$this->assertStringStartsWith( '<div class="checkout-confirm-detail common-summary">', $output );
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


	/**
	 * @param string $code
	 */
	protected function _getCustomerItem( $code )
	{
		$manager = MShop_Customer_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No customer item with code "%1$s" found', $code ) );
		}

		return $item;
	}


	protected function _getOrderItem( $customerid )
	{
		$manager = MShop_Order_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'order.base.customerid', $customerid )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No order item for customer with ID "%1$s" found', $customerid ) );
		}

		return $item;
	}
}
