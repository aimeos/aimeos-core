<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Service_Provider_Delivery_Default.
 */
class MShop_Service_Provider_Delivery_DefaultTest extends MW_Unittest_Testcase
{

	/**
	 * @var    MShop_Service_Provider_Delivery_Default
	 * @access protected
	 */
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Service_Provider_Delivery_DefaultTest');
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
		$serviceManager = MShop_Service_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $serviceManager->createSearch();
		$search->setConditions($search->compare('==', 'service.provider', 'Default'));
		$result = $serviceManager->searchItems($search, array('price'));

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order base item found' );
		}

		$item->setConfig( array( 'default.project' => '8502_TEST' ) );
		$item->setCode( 'test' );

		$this->_object = new MShop_Service_Provider_Delivery_Default(TestHelper::getContext(), $item );
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
		$this->assertArrayHasKey( 'default.project', $this->_object->getConfigBE() );
	}


	public function testGetConfigFE()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$basket = $orderManager->getSubManager( 'base' )->createItem();

		$this->assertEquals( array(), $this->_object->getConfigFE( $basket ) );
	}


	public function testCheckConfigBE()
	{
		$attributes = array( 'default.project' => 'Unit', 'default.url' => 'http://unittest.com' );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertInternalType( 'null', $result['default.project'] );
		$this->assertInternalType( 'null', $result['default.url'] );


		$attributes = array( 'default.project' => '', 'default.url' => 'http://unittest.com' );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertInternalType( 'string', $result['default.project'] );
		$this->assertInternalType( 'null', $result['default.url'] );


		$attributes = array( 'default.project' => 'Unit', 'default.url' => null );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertInternalType( 'null', $result['default.project'] );
		$this->assertInternalType( 'string', $result['default.url'] );
	}


	public function testCheckConfigBEwrongTypes()
	{
		$attributes = array( 'default.project' => true, 'default.url' => 'http://unittest.com', 'default.password' => 1111 );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 5, count( $result ) );
		$this->assertInternalType( 'null', $result['default.url'] );
		$this->assertInternalType( 'null', $result['default.ssl'] );
		$this->assertInternalType( 'null', $result['default.username'] );
		$this->assertInternalType( 'string', $result['default.password'] );
		$this->assertInternalType( 'string', $result['default.project'] );
	}


	public function testCheckConfigFE()
	{
		$this->assertEquals(array(), $this->_object->checkConfigFE( array() ));
	}


	public function testCalcPrice()
	{
		$orderBaseManager = MShop_Order_Manager_Factory::createManager(TestHelper::getContext())->getSubManager('base');
		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare('==', 'order.base.price', '672.00'));
		$result = $orderBaseManager->searchItems($search);

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order base item found' );
		}

		$price = $this->_object->calcPrice( $item );

		$this->assertInstanceOf('MShop_Price_Item_Interface', $price);
		$this->assertEquals($price->getValue(), '12.95');

	}

	public function testIsAvaible()
	{
		$orderBaseManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() )->getSubManager('base');

		$this->assertTrue( $this->_object->isAvailable( $orderBaseManager->createItem() ) );
	}

	public function testIsImplemented()
	{
		$this->assertFalse( $this->_object->isImplemented( MShop_Service_Provider_Delivery_Abstract::FEAT_QUERY ) );
	}

	public function testProcess()
	{
	}

	public function testBuildXML()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$criteria = $orderManager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );
		$criteria->setSlice( 0, 1 );
		$items = $orderManager->searchItems( $criteria );

		if( ( $order = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No order item available for order statuspayment "%1s" and "%2s"', MShop_Order_Item_Abstract::STAT_REFUSED, MShop_Order_Item_Abstract::TYPE_WEB ) );
		}

		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderBase = $orderBaseManager->getItem( $order->getBaseId() );

		$expected = '<?xml version="1.0" encoding="UTF-8"?>
			<orderlist>
				<orderitem>
					<id><![CDATA['. $order->getId() .']]></id>
					<type><![CDATA[web]]></type>
					<datetime><![CDATA[2008-02-15T12:34:56Z]]></datetime>
					<customerid><![CDATA[' . $orderBase->getCustomerId() . ']]></customerid>
					<projectcode><![CDATA[8502_TEST]]></projectcode>
					<languagecode><![CDATA[DE]]></languagecode>
					<currencycode><![CDATA[EUR]]></currencycode>
					<deliveryitem>
						<code><![CDATA[73]]></code>
						<name><![CDATA[solucia]]></name>
					</deliveryitem>
					<paymentitem>
						<code><![CDATA[OGONE]]></code>
						<name><![CDATA[ogone]]></name>
						<fieldlist>
							<fielditem>
								<name><![CDATA[ACOWNER]]></name>
								<value><![CDATA[test user]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[ACSTRING]]></name>
								<value><![CDATA[9876543]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[NAME]]></name>
								<value><![CDATA[CreditCard]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[REFID]]></name>
								<value><![CDATA[12345678]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[TXDATE]]></name>
								<value><![CDATA[2009-08-18]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[X-ACCOUNT]]></name>
								<value><![CDATA[Kraft02]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[X-STATUS]]></name>
								<value><![CDATA[9]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[ogone-alias-name]]></name>
								<value><![CDATA[aliasName]]></value>
							</fielditem>
							<fielditem>
								<name><![CDATA[ogone-alias-value]]></name>
								<value><![CDATA[aliasValue]]></value>
							</fielditem>
						</fieldlist>
					</paymentitem>
					<priceitem>
						<price><![CDATA[53.50]]></price>
						<shipping><![CDATA[1.50]]></shipping>
						<discount><![CDATA[0.00]]></discount>
						<total><![CDATA[55.00]]></total>
					</priceitem>
					<productlist>
						<productitem>
							<position><![CDATA[1]]></position>
							<code><![CDATA[CNE]]></code>
							<name><![CDATA[Cafe Noire Expresso]]></name>
							<quantity><![CDATA[9]]></quantity>
							<priceitem>
								<price><![CDATA[4.50]]></price>
								<shipping><![CDATA[0.00]]></shipping>
								<discount><![CDATA[0.00]]></discount>
								<total><![CDATA[4.50]]></total>
							</priceitem>
						</productitem>
						<productitem>
							<position><![CDATA[2]]></position>
							<code><![CDATA[CNC]]></code>
							<name><![CDATA[Cafe Noire Cappuccino]]></name>
							<quantity><![CDATA[3]]></quantity>
							<priceitem>
								<price><![CDATA[6.00]]></price>
								<shipping><![CDATA[0.50]]></shipping>
								<discount><![CDATA[0.00]]></discount>
								<total><![CDATA[6.50]]></total>
							</priceitem>
						</productitem>
						<productitem>
							<position><![CDATA[3]]></position>
							<code><![CDATA[U:MD]]></code>
							<name><![CDATA[Unittest: Monetary rebate]]></name>
							<quantity><![CDATA[1]]></quantity>
							<priceitem>
								<price><![CDATA[-5.00]]></price>
								<shipping><![CDATA[0.00]]></shipping>
								<discount><![CDATA[0.00]]></discount>
								<total><![CDATA[-5.00]]></total>
							</priceitem>
						</productitem>
						<productitem>
							<position><![CDATA[4]]></position>
							<code><![CDATA[ABCD]]></code>
							<name><![CDATA[16 discs]]></name>
							<quantity><![CDATA[1]]></quantity>
							<priceitem>
								<price><![CDATA[0.00]]></price>
								<shipping><![CDATA[0.00]]></shipping>
								<discount><![CDATA[0.00]]></discount>
								<total><![CDATA[0.00]]></total>
							</priceitem>
						</productitem>
					</productlist>
					<addresslist>
						<addressitem>
							<type><![CDATA[delivery]]></type>
							<salutation><![CDATA[mr]]></salutation>
							<title><![CDATA[Dr.]]></title>
							<firstname><![CDATA[Our]]></firstname>
							<lastname><![CDATA[Unittest]]></lastname>
							<company><![CDATA[Metaways]]></company>
							<address1><![CDATA[Pickhuben]]></address1>
							<address2><![CDATA[2-4]]></address2>
							<address3><![CDATA[]]></address3>
							<postalcode><![CDATA[20457]]></postalcode>
							<city><![CDATA[Hamburg]]></city>
							<state><![CDATA[Hamburg]]></state>
							<countrycode><![CDATA[DE]]></countrycode>
							<email><![CDATA[eshop@metaways.de]]></email>
							<phone><![CDATA[055544332211]]></phone>
						</addressitem>
						<addressitem>
							<type><![CDATA[payment]]></type>
							<salutation><![CDATA[mr]]></salutation>
							<title><![CDATA[]]></title>
							<firstname><![CDATA[Our]]></firstname>
							<lastname><![CDATA[Unittest]]></lastname>
							<company><![CDATA[]]></company>
							<address1><![CDATA[Durchschnitt]]></address1>
							<address2><![CDATA[1]]></address2>
							<address3><![CDATA[]]></address3>
							<postalcode><![CDATA[20146]]></postalcode>
							<city><![CDATA[Hamburg]]></city>
							<state><![CDATA[Hamburg]]></state>
							<countrycode><![CDATA[DE]]></countrycode>
							<email><![CDATA[eshop@metaways.de]]></email>
							<phone><![CDATA[055544332211]]></phone>
						</addressitem>
					</addresslist>
					<additional>
						<comment><![CDATA[]]></comment>
						<discount/>
					</additional>
				</orderitem>
			</orderlist>';

		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;

		if ( $dom->loadXML( $expected ) !== true ) {
			throw new Exception( 'Loading XML failed' );
		}

		$this->assertEquals( $dom->saveXML(), $this->_object->buildXML( $order ) );
	}

	public function testBuildXMLWithBundle()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$criteria = $orderManager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.datepayment', '2009-03-18 16:14:32' ) );
		$criteria->setSlice( 0, 1 );
		$items = $orderManager->searchItems( $criteria );

		if( ( $order = reset( $items ) ) === false ) {
			throw new Exception( 'No order item available' );
		}

		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderBase = $orderBaseManager->getItem( $order->getBaseId() );

		$expected = '<?xml version="1.0" encoding="UTF-8"?>
<orderlist>
	<orderitem>
		<id><![CDATA['. $order->getId() .']]></id>
		<type><![CDATA[web]]></type>
		<datetime><![CDATA[2009-03-18T16:14:32Z]]></datetime>
		<customerid><![CDATA[' . $orderBase->getCustomerId() . ']]></customerid>
		<projectcode><![CDATA[8502_TEST]]></projectcode>
		<languagecode><![CDATA[DE]]></languagecode>
		<currencycode><![CDATA[EUR]]></currencycode>
		<deliveryitem>
			<code><![CDATA[73]]></code>
			<name><![CDATA[solucia]]></name>
		</deliveryitem>
		<paymentitem>
			<code><![CDATA[OGONE]]></code>
			<name><![CDATA[ogone]]></name>
			<fieldlist />
		</paymentitem>
		<priceitem>
			<price><![CDATA[4800.00]]></price>
			<shipping><![CDATA[180.00]]></shipping>
			<discount><![CDATA[0.00]]></discount>
			<total><![CDATA[4980.00]]></total>
		</priceitem>
		<productlist>
			<productitem>
				<position><![CDATA[1]]></position>
				<code><![CDATA[bdl:zyx]]></code>
				<name><![CDATA[Bundle Unittest1]]></name>
				<quantity><![CDATA[1]]></quantity>
				<priceitem>
					<price><![CDATA[1200.00]]></price>
					<shipping><![CDATA[30.00]]></shipping>
					<discount><![CDATA[0.00]]></discount>
					<total><![CDATA[1230.00]]></total>
				</priceitem>
				<childlist>
					<productitem>
						<position><![CDATA[2]]></position>
						<code><![CDATA[bdl:EFG]]></code>
						<name><![CDATA[Bundle Unittest1]]></name>
						<quantity><![CDATA[1]]></quantity>
						<priceitem>
							<price><![CDATA[600.00]]></price>
							<shipping><![CDATA[30.00]]></shipping>
							<discount><![CDATA[0.00]]></discount>
							<total><![CDATA[630.00]]></total>
						</priceitem>
					</productitem>
					<productitem>
						<position><![CDATA[3]]></position>
						<code><![CDATA[bdl:HIJ]]></code>
						<name><![CDATA[Bundle Unittest 1]]></name>
						<quantity><![CDATA[1]]></quantity>
						<priceitem>
							<price><![CDATA[600.00]]></price>
							<shipping><![CDATA[30.00]]></shipping>
							<discount><![CDATA[0.00]]></discount>
							<total><![CDATA[630.00]]></total>
						</priceitem>
					</productitem>
				</childlist>
			</productitem>
			<productitem>
				<position><![CDATA[4]]></position>
				<code><![CDATA[bdl:hal]]></code>
				<name><![CDATA[Bundle Unittest2]]></name>
				<quantity><![CDATA[1]]></quantity>
				<priceitem>
					<price><![CDATA[1200.00]]></price>
					<shipping><![CDATA[30.00]]></shipping>
					<discount><![CDATA[0.00]]></discount>
					<total><![CDATA[1230.00]]></total>
				</priceitem>
				<childlist>
					<productitem>
						<position><![CDATA[5]]></position>
						<code><![CDATA[bdl:EFX]]></code>
						<name><![CDATA[Bundle Unittest 2]]></name>
						<quantity><![CDATA[1]]></quantity>
						<priceitem>
							<price><![CDATA[600.00]]></price>
							<shipping><![CDATA[30.00]]></shipping>
							<discount><![CDATA[0.00]]></discount>
							<total><![CDATA[630.00]]></total>
						</priceitem>
					</productitem>
					<productitem>
						<position><![CDATA[6]]></position>
						<code><![CDATA[bdl:HKL]]></code>
						<name><![CDATA[Bundle Unittest 2]]></name>
						<quantity><![CDATA[1]]></quantity>
						<priceitem>
							<price><![CDATA[600.00]]></price>
							<shipping><![CDATA[30.00]]></shipping>
							<discount><![CDATA[0.00]]></discount>
							<total><![CDATA[630.00]]></total>
						</priceitem>
					</productitem>
				</childlist>
			</productitem>
		</productlist>
		<addresslist>
			<addressitem>
				<type><![CDATA[payment]]></type>
				<salutation><![CDATA[mrs]]></salutation>
				<title><![CDATA[]]></title>
				<firstname><![CDATA[Adelheid]]></firstname>
				<lastname><![CDATA[Mustertest]]></lastname>
				<company><![CDATA[]]></company>
				<address1><![CDATA[Königallee]]></address1>
				<address2><![CDATA[1]]></address2>
				<address3><![CDATA[]]></address3>
				<postalcode><![CDATA[20146]]></postalcode>
				<city><![CDATA[Hamburg]]></city>
				<state><![CDATA[Hamburg]]></state>
				<countrycode><![CDATA[DE]]></countrycode>
				<email><![CDATA[eshop@metaways.de]]></email>
				<phone><![CDATA[055544332211]]></phone>
			</addressitem>
		</addresslist>
		<additional>
			<comment><![CDATA[]]></comment>
			<discount />
		</additional>
	</orderitem>
</orderlist>';

		$dom = new DOMDocument('1.0', 'UTF-8');
		$dom->preserveWhiteSpace = false;

		if ( $dom->loadXML( $expected ) !== true ) {
			throw new Exception( 'Loading XML failed' );
		}

		$this->assertEquals( $dom->saveXML(), $this->_object->buildXML( $order ) );
	}
}