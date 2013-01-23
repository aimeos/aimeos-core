<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14843 2012-01-13 08:11:39Z nsendetzky $
 */


/**
 * Test class for MShop_Service_Provider_Payment_DirectDebit.
 */
class MShop_Service_Provider_Payment_DirectDebitTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Service_Provider_Payment_DirectDebit
	 * @access protected
	 */
	protected $_object;
	protected static $_basket;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Service_Provider_Payment_DirectDebitTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$serviceManager = MShop_Service_Manager_Factory::createManager( $context );

		$serviceItem = $serviceManager->createItem();
		$serviceItem->setCode( 'test' );

		$this->_object = new MShop_Service_Provider_Payment_DirectDebit( $context, $serviceItem );
	}


	public static function setUpBeforeClass()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$search = $orderManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.type', MShop_Order_Item_Abstract::TYPE_WEB ),
			$search->compare( '==', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_AUTHORIZED )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$orderItems = $orderManager->searchItems( $search );

		if( ( $order = reset( $orderItems ) ) === false ) {
			throw new Exception( sprintf('No Order found with statuspayment "%1$s" and type "%2$s"', MShop_Order_Item_Abstract::PAY_AUTHORIZED, MShop_Order_Item_Abstract::TYPE_WEB ) );
		}

		self::$_basket = $orderBaseManager->load( $order->getBaseId() );
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


	public function testGetConfigBE()
	{
		$this->assertArrayHasKey( 'url', $this->_object->getConfigBE() );
	}


	public function testCheckConfigBE()
	{
		$this->assertEquals( array( 'url' => null ), $this->_object->checkConfigBE( array('url' => 'testurl' ) ) );
	}


	public function testCheckConfigBEwrongType()
	{
		$result = $this->_object->checkConfigBE( array('url' => 123 ) );
		$this->assertInternalType( 'string', $result['url'] );
	}


	public function testGetConfigFE()
	{
		$config = $this->_object->getConfigFE( self::$_basket );

		$this->assertArrayHasKey( 'payment.directdebit.accountowner', $config );
		$this->assertArrayHasKey( 'payment.directdebit.accountnumber', $config );
		$this->assertArrayHasKey( 'payment.directdebit.bankcode', $config );
		$this->assertArrayHasKey( 'payment.directdebit.bankname', $config );
		$this->assertEquals( 'Our Unittest', $config['payment.directdebit.accountowner']->getDefault() );
	}


	public function testCheckConfigFE()
	{
		$config = array(
			'payment.directdebit.accountowner' => 'test user',
			'payment.directdebit.accountnumber' => '123456789',
			'payment.directdebit.bankcode' => '1000000',
			'payment.directdebit.bankname' => 'Federal reserve',
		);

		$result = $this->_object->checkConfigFE( $config );

		$expected = array(
			'payment.directdebit.accountowner' => null,
			'payment.directdebit.accountnumber' => null,
			'payment.directdebit.bankcode' => null,
			'payment.directdebit.bankname' => null,
		);

		$this->assertEquals( $expected, $result );
	}


	public function testCheckConfigFEwrongType()
	{
		$config = array(
			'payment.directdebit.accountowner' => 123,
			'payment.directdebit.accountnumber' => 0.1,
			'payment.directdebit.bankcode' => '1000000',
			'payment.directdebit.bankname' => 'Federal reserve',
		);

		$result = $this->_object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'payment.directdebit.accountowner', $result );
		$this->assertArrayHasKey( 'payment.directdebit.accountnumber', $result );

		$this->assertFalse( $result['payment.directdebit.accountowner'] === null );
		$this->assertFalse( $result['payment.directdebit.accountnumber'] === null );
		$this->assertTrue( $result['payment.directdebit.bankcode'] === null );
		$this->assertTrue( $result['payment.directdebit.bankname'] === null );
	}


	public function testProcess()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$order = $manager->createItem();

		$this->_object->process( $order );

		$this->assertEquals( MShop_Order_Item_Abstract::PAY_AUTHORIZED, $order->getPaymentStatus() );
	}


	public function testIsImplemented()
	{
		$this->assertFalse( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_QUERY ) );
		$this->assertFalse( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_CAPTURE ) );
		$this->assertFalse( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_CANCEL ) );
	}
}