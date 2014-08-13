<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Account_History_List_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Client_Html_Account_History_List_Default( $this->_context, $paths );
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
		$customer = $this->_getCustomerItem( 'UTC001' );
		$this->_context->setUserId( $customer->getId() );

		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$customer = $this->_getCustomerItem( 'UTC001' );
		$this->_context->setUserId( $customer->getId() );

		$output = $this->_object->getBody();

		$this->assertStringStartsWith( '<div class="account-history-list">', $output );
		$this->assertRegExp( '#<li class="history-item">#', $output );
		$this->assertRegExp( '#<li class="attr-item order-basic">.*<span class="value">[^<]+</span>.*</li>#smuU', $output );
		$this->assertRegExp( '#<li class="attr-item order-channel">.*<span class="value">[^<]+</span>.*</li>#smuU', $output );
		$this->assertRegExp( '#<li class="attr-item order-payment">.*<span class="value">[^<]+</span>.*</li>#smuU', $output );
		$this->assertRegExp( '#<li class="attr-item order-delivery">.*<span class="value"></span>.*</li>#smuU', $output );
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
}
