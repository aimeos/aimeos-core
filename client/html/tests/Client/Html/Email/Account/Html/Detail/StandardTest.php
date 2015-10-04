<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

class Client_Html_Email_Account_Html_Detail_StandardTest extends PHPUnit_Framework_TestCase
{
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
		$this->object = new Client_Html_Email_Account_Html_Detail_Standard( $this->context, $paths );

		$view = TestHelper::getView();
		$view->extAddressItem = self::$customerItem->getPaymentAddress();
		$view->extAccountCode = self::$customerItem->getCode();
		$view->extAccountPassword = 'testpwd';
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
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<div class="account-detail content-block">', $output );
		$this->assertContains( 'Account', $output );
		$this->assertContains( 'Password', $output );
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
