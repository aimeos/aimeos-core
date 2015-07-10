<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Checkout_Standard_Address_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Client_Html_Checkout_Standard_Address_Default( $this->_context, $paths );
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


	public function testGetHeaderOtherStep()
	{
		$view = TestHelper::getView();
		$view->standardStepActive = 'xyz';
		$this->_object->setView( $view );

		$output = $this->_object->getHeader();
		$this->assertEquals( '', $output );
	}


	public function testGetBody()
	{
		$item = $this->_getCustomerItem();
		$this->_context->setUserId( $item->getId() );

		$view = TestHelper::getView();
		$view->standardStepActive = 'address';
		$view->standardSteps = array( 'address', 'after' );
		$this->_object->setView( $view );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-address">', $output );

		$this->assertGreaterThanOrEqual( 0, count( $view->addressLanguages ) );
		$this->assertGreaterThanOrEqual( 0, count( $view->addressCountries ) );
	}


	public function testGetBodyOtherStep()
	{
		$view = TestHelper::getView();
		$view->standardStepActive = 'xyz';
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


	/**
	 * Returns the customer item for the given code
	 *
	 * @param string $code Unique customer code
	 * @throws Exception If no customer item is found
	 * @return MShop_Customer_Item_Interface Customer item object
	 */
	protected function _getCustomerItem( $code = 'UTC001' )
	{
		$customerManager = MShop_Customer_Manager_Factory::createManager( $this->_context );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', $code ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new Exception( 'Customer item not found' );
		}

		return $customer;
	}
}
