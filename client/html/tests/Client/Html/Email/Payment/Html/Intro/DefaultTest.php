<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Email_Payment_Html_Intro_DefaultTest extends PHPUnit_Framework_TestCase
{
	private static $orderItem;
	private static $orderBaseItem;
	private $object;
	private $context;
	private $emailMock;


	public static function setUpBeforeClass()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );
		$result = $orderManager->searchItems( $search );

		if( ( self::$orderItem = reset( $result ) ) === false ) {
			throw new Exception( 'No order found' );
		}

		self::$orderBaseItem = $orderBaseManager->load( self::$orderItem->getBaseId() );
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
		$this->object = new Client_Html_Email_Payment_Html_Intro_Default( $this->context, $paths );

		$view = TestHelper::getView();
		$view->extOrderItem = self::$orderItem;
		$view->extOrderBaseItem = self::$orderBaseItem;
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
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<p class="email-common-intro', $output );
		$this->assertContains( 'Thank you for your order', $output );
	}


	public function testGetBodyPaymentRefund()
	{
		$orderItem = clone self::$orderItem;
		$view = $this->object->getView();

		$orderItem->setPaymentStatus( MShop_Order_Item_Abstract::PAY_REFUND );
		$view->extOrderItem = $orderItem;

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<p class="email-common-intro', $output );
		$this->assertContains( 'The payment for your order', $output );
	}


	public function testGetBodyPaymentPending()
	{
		$orderItem = clone self::$orderItem;
		$view = $this->object->getView();

		$orderItem->setPaymentStatus( MShop_Order_Item_Abstract::PAY_PENDING );
		$view->extOrderItem = $orderItem;

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<p class="email-common-intro', $output );
		$this->assertContains( 'The order is pending until we receive the final payment', $output );
	}


	public function testGetBodyPaymentReceived()
	{
		$orderItem = clone self::$orderItem;
		$view = $this->object->getView();

		$orderItem->setPaymentStatus( MShop_Order_Item_Abstract::PAY_RECEIVED );
		$view->extOrderItem = $orderItem;

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<p class="email-common-intro', $output );
		$this->assertContains( 'We received the payment', $output );
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
