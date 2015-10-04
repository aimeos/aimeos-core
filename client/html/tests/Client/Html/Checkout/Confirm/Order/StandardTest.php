<?php

/**
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @copyright Aimeos (aimeos.org), 2015
 */

class Client_Html_Checkout_Confirm_Order_StandardTest extends PHPUnit_Framework_TestCase
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
		$this->context = clone TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Checkout_Confirm_Order_Standard( $this->context, $paths );
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
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$view = $this->object->getView();
		$customer = $this->getCustomerItem( 'UTC001' );
		$view->confirmOrderItem = $this->getOrderItem( $customer->getId() );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$customer = $this->getCustomerItem( 'UTC001' );
		$view->confirmOrderItem = $this->getOrderItem( $customer->getId() );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<div class="checkout-confirm-detail common-summary">', $output );
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


	/**
	 * @param string $code
	 */
	protected function getCustomerItem( $code )
	{
		$manager = MShop_Customer_Manager_Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No customer item with code "%1$s" found', $code ) );
		}

		return $item;
	}


	protected function getOrderItem( $customerid )
	{
		$manager = MShop_Order_Manager_Factory::createManager( $this->context );
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
