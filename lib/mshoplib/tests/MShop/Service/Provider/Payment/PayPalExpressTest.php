<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Service_Provider_Payment_PostPay.
 */
class MShop_Service_Provider_Payment_PayPalExpressTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Service_Provider_Payment_PayPal
	 * @access protected
	 */
	private $_object;

	private $_serviceItem;

	private $_order;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Service_Provider_Payment_PayPalExpressTest');
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

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare('==', 'service.code', 'paypalexpress') );

		$serviceItems = $serviceManager->searchItems( $search );

		if( ( $this->_serviceItem = reset( $serviceItems ) ) === false ) {
			throw new Exception( 'No paypalexpress service item available' );
		}

		$this->_object = new MShop_Service_Provider_Payment_PayPalExpress( $context, $this->_serviceItem );

		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderBaseServiceManager = $orderBaseManager->getSubManager( 'service' );
		$orderBaseServiceAttributesManager = $orderBaseServiceManager->getSubManager( 'attribute' );

		$search = $orderManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.type', MShop_Order_Item_Abstract::TYPE_WEB ),
			$search->compare( '==', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_AUTHORIZED )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$orderItems = $orderManager->searchItems( $search );

		if( ( $this->_order = reset( $orderItems ) ) === false ) {
			throw new Exception( sprintf('No Order found with statuspayment "%1$s" and type "%2$s"', MShop_Order_Item_Abstract::PAY_AUTHORIZED, MShop_Order_Item_Abstract::TYPE_WEB ) );
		}

	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$iface = 'MShop_Order_Item_Interface';

		if( $this->_order instanceof $iface )
		{
			$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
			$this->_order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_AUTHORIZED );
			$orderManager->saveItem($this->_order);
		}

		unset( $this->_object );
		unset( $this->_serviceItem );
		unset( $this->_order );
	}


	public function testCheckConfigBE()
	{
		$attributes = array( 'ApiUsername' => 'user', 'ApiPassword' => 'pw', 'ApiSignature' => '1df23eh67', 'CancelUrl' => 'http://cancelUrl', 'ReturnUrl' => 'http://returnUrl'  );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 8, count( $result ) );
		$this->assertEquals( null, $result['ApiUsername'] );
		$this->assertEquals( null, $result['ApiPassword'] );
		$this->assertEquals( null, $result['ApiSignature'] );
		$this->assertEquals( null, $result['CancelUrl'] );
		$this->assertEquals( null, $result['ReturnUrl'] );
	}

	public function testIsImplemented()
	{
		$this->assertTrue( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_CANCEL ) );
		$this->assertTrue( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_CAPTURE ) );
		$this->assertTrue( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_QUERY ) );
		$this->assertTrue( $this->_object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_REFUND ) );
	}


	public function testProcess()
	{
		$what = array( 'PAYMENTREQUEST_0_AMT' => 18.50 );
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=process method error';
		$success = '&ACK=Success&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&TOKEN=UT-99999999';

		$com = new MW_Communication_TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->_object->setCommunication( $com );

		$helperForm = $this->_object->process( $this->_order );

		$values = $testData = array(
			'TOKEN' => 'UT-99999999'
		);

		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$refOrderBase = $orderBaseManager->load( $this->_order->getBaseId() );

		$attributes = $refOrderBase->getService( 'payment' )->getAttributes();

		$attributeList = array();
		foreach( $attributes as $attribute ) {
			$attributeList[ $attribute->getCode() ] = $attribute;
		}

		$this->assertInstanceOf( 'MShop_Common_Item_Helper_Form_Interface', $helperForm );
		$this->assertEquals( 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=UT-99999999', $helperForm->getUrl() );
		$this->assertEquals( 'GET', $helperForm->getMethod() );
		$this->assertEquals( array(), $helperForm->getValues() );

		foreach( $testData AS $key => $value ) {
			$this->assertEquals( $attributeList[ $key ]->getValue(), $testData[ $key ] );
		}
	}


	public function testUpdateSync()
	{
		$what = array( 'TOKEN' => 'UT-99999999' );
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=updatesync method error';
		$success = '&TOKEN=UT-99999999&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725&EMAIL=user_paypal_email@metaways.de&PAYERID=PaypalUnitTestBuyer&TRANSACTIONID=111111111&PAYMENTSTATUS=Pending&PENDINGREASON=authorization&INVNUM='.$this->_order->getId();

		$com = new MW_Communication_TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->_object->setCommunication( $com );

		$response = array ( 'token' => 'UT-99999999' );

		$testData = array(
			'TOKEN' => 'UT-99999999',
			'PAYERID' => 'PaypalUnitTestBuyer',
			'TRANSACTIONID' => '111111111',
			'EMAIL' => 'user_paypal_email@metaways.de'
		);

		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$this->assertInstanceOf( 'MShop_Order_Item_Interface', $this->_object->updateSync( $response ) );

		$refOrderBase = $orderBaseManager->load( $this->_order->getBaseId() );

		$attributes = $refOrderBase->getService( 'payment' )->getAttributes();

		$attributeList = array();
		foreach( $attributes as $attribute ){
			$attributeList[ $attribute->getCode() ] = $attribute;
		}

		foreach( $testData AS $key => $value ) {
			$this->assertEquals( $attributeList[ $key ]->getValue(), $testData[ $key ] );
		}
	}


	public function testRefund()
	{
		$what = array(
			'METHOD' => 'RefundTransaction',
			'REFUNDSOURCE' => 'instant',
			'REFUNDTYPE' => 'Full',
			'TRANSACTIONID' => '111111111',
			'INVOICEID' => $this->_order->getId()
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=refund method error';
		$success = 'REFUNDTRANSACTIONID=88888888&FEEREFUNDAMT=2.00&TOTALREFUNDAMT=24.00&CURRENCYCODE=EUR&REFUNDSTATUS=delayed&PENDINGREASON=echeck&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new MW_Communication_TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->_object->setCommunication( $com );

		$this->_object->refund( $this->_order );

		$testData = array(
			'TOKEN' => 'UT-99999999',
			'PAYERID' => 'PaypalUnitTestBuyer',
			'TRANSACTIONID' => '111111111',
			'EMAIL' => 'user_paypal_email@metaways.de',
			'REFUNDTRANSACTIONID' => '88888888'
		);

		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$this->_order = $orderManager->getItem( $this->_order->getId() );

		$refOrderBase = $orderBaseManager->load( $this->_order->getBaseId() );

		$attributes = $refOrderBase->getService( 'payment' )->getAttributes();

		$attributeList = array();
		foreach( $attributes as $attribute ){
			$attributeList[ $attribute->getCode() ] = $attribute;
		}

		foreach( $testData AS $key => $value ) {
			$this->assertEquals( $attributeList[ $key ]->getValue(), $testData[ $key ] );
		}

		$this->assertEquals( MShop_Order_Item_Abstract::PAY_REFUND, $this->_order->getPaymentStatus() );
	}


	public function testCapture()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager('base');
		$baseItem = $orderBaseManager->getItem( $this->_order->getBaseId() );

		$what = array(
			'METHOD' => 'DoCapture',
			'COMPLETETYPE' => 'Complete',
			'AUTHORIZATIONID' => '111111111',
			'INVNUM' => $this->_order->getId(),
			'CURRENCYCODE' => $baseItem->getPrice()->getCurrencyId(),
			'AMT' => ( $baseItem->getPrice()->getValue() + $baseItem->getPrice()->getShipping() )
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=capture method error';
		$success = 'AUTHORIZATIONID=112233&TRANSACTIONID=111111111&PARENTTRANSACTIONID=12212AD&TRANSACTIONTYPE=express-checkout&AMT=22.30&FEEAMT=3.33&PAYMENTSTATUS=Completed&PENDINGREASON=None&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new MW_Communication_TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->_object->setCommunication( $com );

		$this->_object->capture( $this->_order );

		$this->_order = $orderManager->getItem( $this->_order->getId() );

		$this->assertEquals( MShop_Order_Item_Abstract::PAY_RECEIVED, $this->_order->getPaymentStatus() );
	}

	public function testQueryPaymentReceived()
	{
		$what = array(
			'METHOD' => 'GetTransactionDetails',
			'TRANSACTIONID' => '111111111'
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=query payment received test method error';
		$success = 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Completed&PENDINGREASON=None&INVNUM=34&CORRELATIONID=1f4b8e2c86ead&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new MW_Communication_TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->_object->setCommunication( $com );

		$this->_object->query( $this->_order );

		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$this->_order = $orderManager->getItem( $this->_order->getId() );

		$this->assertEquals( MShop_Order_Item_Abstract::PAY_RECEIVED, $this->_order->getPaymentStatus() );
	}


	public function testQueryPaymentRefused()
	{
		$what = array(
			'METHOD' => 'GetTransactionDetails',
			'TRANSACTIONID' => '111111111',
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=query payment refused test method error';
		$success = 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Expired&PENDINGREASON=None&INVNUM=34&CORRELATIONID=1f4b8e2c86ead&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new MW_Communication_TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->_object->setCommunication( $com );

		$this->_object->query( $this->_order );

		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$this->_order = $orderManager->getItem( $this->_order->getId() );

		$this->assertEquals( MShop_Order_Item_Abstract::PAY_REFUSED, $this->_order->getPaymentStatus() );
	}


	public function testCancel()
	{
		$what = array(
			'METHOD' => 'DoVoid',
			'AUTHORIZATIONID' => '111111111',
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=cancel test method error';
		$success = 'CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new MW_Communication_TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->_object->setCommunication( $com );

		$this->_object->cancel( $this->_order );

		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$this->_order = $orderManager->getItem( $this->_order->getId() );

		$this->assertEquals( MShop_Order_Item_Abstract::PAY_CANCELED, $this->_order->getPaymentStatus() );
	}


	public function testQueryPaymentAuthorized()
	{
		$what = array(
			'METHOD' => 'GetTransactionDetails',
			'TRANSACTIONID' => '111111111',
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=query payment authorized test method error';
		$success = 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Pending&PENDINGREASON=authorization&INVNUM=34&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new MW_Communication_TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->_object->setCommunication( $com );

		$this->_object->query( $this->_order );

		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$this->_order = $orderManager->getItem( $this->_order->getId() );

		$this->assertEquals( MShop_Order_Item_Abstract::PAY_AUTHORIZED, $this->_order->getPaymentStatus() );
	}


	public function testWrongAuthorization()
	{
		$what = array(
			'VERSION' => '87.0',
			'SIGNATURE' => 'signature',
			'USER' => 'name',
			'PWD' => 'pw',
		);
		$error = '&ACK=Error&VERSION=87.0&BUILD=3136725&CORRELATIONID=1234567890&L_ERRORCODE0=0000&L_SHORTMESSAGE0=wrong authorization test method error';
		$success = 'SHIPPINGCALCULATIONMODE=Callback&INSURANCEOPTIONSELECTED=false&RECEIVERID=unit_1340199666_biz_api1.yahoo.de&PAYERID=unittest&PAYERSTATUS=verified&COUNTRYCODE=DE&FIRSTNAME=Unit&LASTNAME=Test&SHIPTOSTREET=Unitteststr. 11&TRANSACTIONID=111111111&PARENTTRANSACTIONID=111111111&TRANSACTIONTYPE=express-checkout&AMT=22.50CURRENCYCODE=EUR&FEEAMT=4.44&PAYMENTSTATUS=Pending&PENDINGREASON=authorization&INVNUM=34&CORRELATIONID=1234567890&ACK=Success&VERSION=87.0&BUILD=3136725';

		$com = new MW_Communication_TestPayPalExpress();
		$com->addRule( $what, $error, $success );
		$this->_object->setCommunication( $com );

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->_object->process( $this->_order );
	}
}