<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Email_Watch_Text_Detail_DefaultTest extends MW_Unittest_Testcase
{
	private static $_productItems;
	private static $_customerItem;
	private $_object;
	private $_context;
	private $_emailMock;


	public static function setUpBeforeClass()
	{
		$context = TestHelper::getContext();

		$manager = MShop_Customer_Manager_Factory::createManager( $context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $manager->searchItems( $search );

		if( ( self::$_customerItem = reset( $result ) ) === false ) {
			throw new Exception( 'No customer found' );
		}

		$manager = MShop_Product_Manager_Factory::createManager( $context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );

		foreach( $manager->searchItems( $search, array( 'text', 'price', 'media' ) ) as $id => $product )
		{
			$prices = $product->getRefItems( 'price', 'default', 'default' );

			self::$_productItems[$id]['price'] = reset( $prices );
			self::$_productItems[$id]['item'] = $product;
		}
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_emailMock = $this->getMock( 'MW_Mail_Message_None' );

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Email_Watch_Text_Detail_Default( $this->_context, $paths );

		$view = TestHelper::getView();
		$view->extProducts = self::$_productItems;
		$view->extAddressItem = self::$_customerItem->getPaymentAddress();
		$view->addHelper( 'mail', new MW_View_Helper_Mail_Default( $view, $this->_emailMock ) );

		$this->_object->setView( $view );
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
		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->_object->getBody();

		$this->assertContains( 'Cafe Noire Cappuccino', $output );
		$this->assertContains( 'Cafe Noire Expresso', $output );
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
}
