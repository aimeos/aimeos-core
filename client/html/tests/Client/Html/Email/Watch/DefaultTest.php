<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Email_Watch_DefaultTest extends PHPUnit_Framework_TestCase
{
	private static $productItems;
	private static $customerItem;
	private $object;
	private $context;
	private $emailMock;


	public static function setUpBeforeClass()
	{
		$context = TestHelper::getContext();

		$manager = MShop_Customer_Manager_Factory::createManager( $context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $manager->searchItems( $search );

		if( ( self::$customerItem = reset( $result ) ) === false ) {
			throw new Exception( 'No customer found' );
		}

		$manager = MShop_Product_Manager_Factory::createManager( $context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );

		foreach( $manager->searchItems( $search, array( 'text', 'price', 'media' ) ) as $id => $product )
		{
			$prices = $product->getRefItems( 'price', 'default', 'default' );

			self::$productItems[$id]['price'] = reset( $prices );
			self::$productItems[$id]['item'] = $product;
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
		$this->context = TestHelper::getContext();
		$this->emailMock = $this->getMock( 'MW_Mail_Message_None' );

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Email_Watch_Default( $this->context, $paths );

		$view = TestHelper::getView( 'unittest', $this->context->getConfig() );
		$view->extProducts = self::$productItems;
		$view->extAddressItem = self::$customerItem->getPaymentAddress();
		$view->addHelper( 'mail', new MW_View_Helper_Mail_Default( $view, $this->emailMock ) );

		$this->object->setView( $view );
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
		$config = $this->context->getConfig();
		$config->set( 'client/html/email/from-email', 'me@localhost' );
		$config->set( 'client/html/email/from-name', 'My company' );

		$this->emailMock->expects( $this->once() )->method( 'addHeader' )
			->with( $this->equalTo( 'X-MailGenerator' ), $this->equalTo( 'Aimeos' ) );

		$this->emailMock->expects( $this->once() )->method( 'addTo' )
			->with( $this->equalTo( 'test@example.com' ), $this->equalTo( 'Our Unittest' ) );

		$this->emailMock->expects( $this->once() )->method( 'addFrom' )
			->with( $this->equalTo( 'me@localhost' ), $this->equalTo( 'My company' ) );

		$this->emailMock->expects( $this->once() )->method( 'addReplyTo' )
			->with( $this->equalTo( 'me@localhost' ), $this->equalTo( 'My company' ) );

		$this->emailMock->expects( $this->once() )->method( 'setSubject' )
			->with( $this->stringContains( 'Your watched products' ) );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertNotNull( $output );
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
}
