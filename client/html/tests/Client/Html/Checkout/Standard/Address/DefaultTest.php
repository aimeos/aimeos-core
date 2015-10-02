<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Checkout_Standard_Address_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Client_Html_Checkout_Standard_Address_Default( $this->context, $paths );
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


	public function testGetHeaderOtherStep()
	{
		$view = TestHelper::getView();
		$view->standardStepActive = 'xyz';
		$this->object->setView( $view );

		$output = $this->object->getHeader();
		$this->assertEquals( '', $output );
	}


	public function testGetBody()
	{
		$item = $this->getCustomerItem();
		$this->context->setUserId( $item->getId() );

		$view = TestHelper::getView();
		$view->standardStepActive = 'address';
		$view->standardSteps = array( 'address', 'after' );
		$this->object->setView( $view );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-address">', $output );

		$this->assertGreaterThanOrEqual( 0, count( $view->addressLanguages ) );
		$this->assertGreaterThanOrEqual( 0, count( $view->addressCountries ) );
	}


	public function testGetBodyOtherStep()
	{
		$view = TestHelper::getView();
		$view->standardStepActive = 'xyz';
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


	/**
	 * Returns the customer item for the given code
	 *
	 * @param string $code Unique customer code
	 * @throws Exception If no customer item is found
	 * @return MShop_Customer_Item_Interface Customer item object
	 */
	protected function getCustomerItem( $code = 'UTC001' )
	{
		$customerManager = MShop_Customer_Manager_Factory::createManager( $this->context );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new Exception( 'Customer item not found' );
		}

		return $customer;
	}
}
