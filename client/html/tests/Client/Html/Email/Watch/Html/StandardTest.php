<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Email_Watch_Html_StandardTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Client_Html_Email_Watch_Html_Standard( $this->context, $paths );

		$view = TestHelper::getView( 'unittest', $this->context->getConfig() );
		$view->extProducts = self::$productItems;
		$view->extAddressItem = self::$customerItem->getPaymentAddress();
		$view->addHelper( 'mail', new MW_View_Helper_Mail_Standard( $view, $this->emailMock ) );

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
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$ds = DIRECTORY_SEPARATOR;
		$file = '..' . $ds . 'themes' . $ds . 'classic' . $ds . 'media' . $ds . 'aimeos.png';
		$this->context->getConfig()->set( 'client/html/email/logo', $file );

		$this->emailMock->expects( $this->once() )->method( 'embedAttachment' )
			->will( $this->returnValue( 'cid:123-unique-id' ) );

		$this->emailMock->expects( $this->once() )->method( 'setBodyHtml' )
			->with( $this->matchesRegularExpression( '#<html>.*<title>E-mail notification</title>.*<meta.*Aimeos.*<body>#smu' ) );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<html>', $output );
		$this->assertContains( 'cid:123-unique-id', $output );
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
