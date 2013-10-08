<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Order_Item_Base_Default.
 */
class MShop_Order_Item_Base_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_values;
	private $_locale;
	private $_products;
	private $_addresses;
	private $_services;
	private $_coupons;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Order_Item_Base_DefaultTest');
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
		$priceManager = MShop_Price_Manager_Factory::createManager( TestHelper::getContext() );
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );

		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderAddressManager = $orderBaseManager->getSubManager( 'address' );
		$orderProductManager = $orderBaseManager->getSubManager( 'product' );
		$orderServiceManager = $orderBaseManager->getSubManager( 'service' );


		$this->_values = array(
			'id' => 1,
			'siteid'=>99,
			'customerid' => 'testuser',
			'comment' => 'this is a comment from unittest',
			'status' => 1,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_locale = MShop_Locale_Manager_Factory::createManager(TestHelper::getContext() )->createItem();

		$this->_object = new MShop_Order_Item_Base_Default( $priceManager->createItem(), $this->_locale, $this->_values );


		$price = $priceManager->createItem();
		$price->setRebate('3.01');
		$price->setValue('43.12');
		$price->setCosts('1.11');
		$price->setTaxRate('0.00');
		$price->setCurrencyId('EUR');

		$prod1 = $orderProductManager->createItem();
		$prod1->setProductCode('prod1');
		$prod1->setPrice($price);

		$price = $priceManager->createItem();
		$price->setRebate('4.00');
		$price->setValue('20.00');
		$price->setCosts('2.00');
		$price->setTaxRate('0.50');
		$price->setCurrencyId('EUR');

		$prod2 = $orderProductManager->createItem();
		$prod2->setProductCode('prod2');
		$prod2->setPrice($price);


		$this->_products = array( $prod1, $prod2 );

		$this->_coupons = array( 'OPQR' => array( $prod1 ) );

		$this->_addresses = array(
			MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT => $orderAddressManager->createItem(),
			MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => $orderAddressManager->createItem(),
		);

		$this->_services = array(
			'payment' => $orderServiceManager->createItem(),
			'delivery' => $orderServiceManager->createItem(),
		);


		//registering order object for plugin use
		$pluginManager = MShop_Plugin_Manager_Factory::createManager(TestHelper::getContext());
		$pluginManager->register($this->_object, 'order');
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testGetId()
	{
		$this->assertEquals( $this->_values['id'], $this->_object->getId() );
	}


	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertEquals(null, $this->_object->getId() );
		$this->assertTrue($this->_object->isModified());

		$this->_object->setId(5);
		$this->assertEquals(5, $this->_object->getId() );
		$this->assertFalse($this->_object->isModified());

		$this->setExpectedException('MShop_Exception');
		$this->_object->setId(6);
	}


	public function testSetId2()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->setId('test');
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}


	public function testGetCustomerIc()
	{
		$this->assertEquals($this->_values['customerid'], $this->_object->getCustomerId() );
	}


	public function testSetCustomerId()
	{
		$this->_object->setCustomerId('44');
		$this->assertEquals('44', $this->_object->getCustomerId() );
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetLocale()
	{
		$this->assertEquals($this->_locale, $this->_object->getLocale() );
	}


	public function testSetLocale()
	{
		$locale = MShop_Locale_Manager_Factory::createManager(TestHelper::getContext())->createItem();
		$this->_object->setLocale($locale);

		$this->assertEquals($locale, $this->_object->getLocale());
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetPrice()
	{
		foreach( $this->_products as $product ) {
			$this->_object->addProduct( $product );
		}

		$priceItem = $this->_object->getPrice();

		$this->assertEquals($priceItem->getCurrencyId(), 'EUR');
		$this->assertEquals($priceItem->getTaxRate(), '0.00');
		$this->assertEquals($priceItem->getRebate(), '7.01');
		$this->assertEquals($priceItem->getCosts(), '3.11');
		$this->assertEquals($priceItem->getValue(), '63.12');
	}


	public function testGetComment()
	{
		$this->assertEquals( $this->_values['comment'], $this->_object->getComment() );
	}


	public function testSetComment()
	{
		$this->_object->setComment( 'New unit test comment' );
		$this->assertEquals( 'New unit test comment', $this->_object->getComment() );

		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( $this->_values['status'], $this->_object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->_object->setStatus( 1 );
		$this->assertEquals( 1, $this->_object->getStatus() );

		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->_object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->_object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}

	public function testToArray()
	{
		$list = $this->_object->toArray();
		$price = $this->_object->getPrice();

		$this->assertEquals( $this->_object->getId(), $list['order.base.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $list['order.base.siteid'] );
		$this->assertEquals( $this->_object->getCustomerId(), $list['order.base.customerid'] );
		$this->assertEquals( $this->_object->getLocale()->getLanguageId(), $list['order.base.languageid'] );
		$this->assertEquals( $this->_object->getComment(), $list['order.base.comment'] );
		$this->assertEquals( $this->_object->getStatus(), $list['order.base.status'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $list['order.base.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $list['order.base.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $list['order.base.editor'] );

		$this->assertEquals( $price->getValue(), $list['order.base.price'] );
		$this->assertEquals( $price->getCosts(), $list['order.base.costs'] );
		$this->assertEquals( $price->getRebate(), $list['order.base.rebate'] );
		$this->assertEquals( $price->getCurrencyId(), $list['order.base.currencyid'] );
	}


	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}


	public function testFinish()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}


	public function testGetProducts()
	{
		foreach( $this->_products as $product ) {
			$this->_object->addProduct( $product );
		}

		$this->assertSame($this->_products, $this->_object->getProducts());
		$this->assertSame($this->_products[1], $this->_object->getProduct(1));
	}


	public function testAddProduct()
	{
		foreach( $this->_products as $product ) {
			$this->_object->addProduct( $product );
		}

		$orderManager = MShop_Order_Manager_Factory::createManager(TestHelper::getContext());
		$orderProductManager = $orderManager->getSubManager( 'base' )->getSubManager( 'product' );
		$product = $orderProductManager->createItem();

		$price = MShop_Price_Manager_Factory::createManager( TestHelper::getContext() )->createItem();
		$price->setValue( '2.99' );

		$product->setPrice( $price );
		$product->setProductCode('prodid3');


		$products = $this->_object->getProducts();
		$this->_object->addProduct($product);
		$products[] = $product;

		$this->assertSame($products, $this->_object->getProducts());
		$this->assertTrue($this->_object->isModified());


		$orderProduct = clone $product;
		$orderProduct->setProductCode('prodid4');
		array_splice($products, 2, 0, array( $orderProduct ));
		$this->_object->addProduct($orderProduct, 2 );
		$this->assertEquals( $products, $this->_object->getProducts() );


		$orderProduct = clone $product;
		$orderProduct->setProductCode('prodid5');
		array_splice($products, 2, 0, array( $orderProduct ));
		$this->_object->addProduct($orderProduct, 2 );
		$this->assertEquals( $products, $this->_object->getProducts() );


		$orderProductSame = clone $product;
		$orderProductSame->setProductCode('prodid5');
		$orderProductSame->setQuantity( 5 );
		$orderProduct->setQuantity( 4 );

		$this->_object->addProduct($orderProductSame );
		$this->assertEquals( $products, $this->_object->getProducts() );

		// Exceed limit for single product
		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->_object->addProduct($orderProductSame );
	}


	public function testDeleteProduct()
	{
		foreach( $this->_products as $product ) {
			$this->_object->addProduct( $product );
		}

		unset($this->_products[1]);
		$this->_object->deleteProduct(1);
		$this->assertSame($this->_products, $this->_object->getProducts());
		$this->assertTrue($this->_object->isModified());

		$this->setExpectedException('MShop_Order_Exception');
		$this->_object->deleteProduct( -1 );
	}


	public function testGetAddresses()
	{
		foreach( $this->_addresses as $type => $address ) {
			$this->_object->setAddress( $address, $type );
		}

		$this->assertEquals($this->_addresses, $this->_object->getAddresses());

		$address = $this->_object->getAddress(MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT);
		$this->assertEquals($this->_addresses[MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT], $address);
	}


	public function testSetAddresses()
	{
		foreach( $this->_addresses as $type => $address ) {
			$this->_object->setAddress( $address, $type );
		}

		$orderManager = MShop_Order_Manager_Factory::createManager(TestHelper::getContext());
		$orderAddressManager = $orderManager->getSubManager( 'base' )->getSubManager( 'address' );
		$address = $orderAddressManager->createItem();

		$this->_object->setAddress($address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT);
		$this->assertEquals($address, $this->_object->getAddress(MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT));

		$this->assertTrue($this->_object->isModified());
	}


	public function testGetServices()
	{
		foreach( $this->_services as $type => $service ) {
			$this->_object->setService( $service, $type );
		}

		$this->assertEquals($this->_services, $this->_object->getServices());

		$type = 'payment';
		$this->assertEquals($this->_services[$type], $this->_object->getService($type));
	}


	public function testSetService()
	{
		foreach( $this->_services as $type => $service ) {
			$this->_object->setService( $service, $type );
		}

		$type = 'delivery';
		$orderManager = MShop_Order_Manager_Factory::createManager(TestHelper::getContext());
		$orderServiceManager = $orderManager->getSubManager( 'base' )->getSubManager( 'service' );
		$service = $orderServiceManager->createItem();

		$this->_object->setService($service, $type);
		$this->assertEquals($service, $this->_object->getService($type));
		$this->assertTrue($this->_object->isModified());
	}


	public function testCoupons()
	{
		foreach( $this->_coupons as $code => $products ) {
			$this->_object->addCoupon( $code, $products );
		}

		foreach( $this->_object->getCoupons() as $coupon => $products ) {
			$this->assertEquals( $this->_coupons[$coupon], $products );
		}
	}


	public function testDeleteCoupon()
	{
		foreach( $this->_coupons as $code => $products ) {
			$this->_object->addCoupon( $code, $products );
		}

		$this->_object->deleteCoupon( 'OPQR' );

		foreach( $this->_object->getCoupons() as $coupon => $products ) {
			$this->assertEquals( array(), $products );
		}

		$this->_object->deleteCoupon( 'OPQR', true );
		$this->assertEquals( array(), $this->_object->getCoupons() );

		$this->assertTrue($this->_object->isModified());
	}


	public function testCheckInvalid()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->check( -1 );
	}


	public function testCheckAllFailure()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->check( MShop_Order_Item_Base_Abstract::PARTS_ALL );
	}


	public function testCheckProductsFailure()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->check( MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
	}


	public function testCheckAddresses()
	{
		foreach( $this->_products as $product ) {
			$this->_object->addProduct( $product );
		}

		foreach( $this->_addresses as $type => $address ) {
			$this->_object->setAddress( $address, $type );
		}

		$this->_object->check( MShop_Order_Item_Base_Abstract::PARTS_ADDRESS );
	}


	public function testCheckNoAddresses()
	{
		foreach( $this->_products as $product ) {
			$this->_object->addProduct( $product );
		}

		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$this->_object->check( MShop_Order_Item_Base_Abstract::PARTS_ADDRESS );
	}


	public function testCheckServices()
	{
		foreach( $this->_products as $product ) {
			$this->_object->addProduct( $product );
		}

		foreach( $this->_addresses as $type => $address ) {
			$this->_object->setAddress( $address, $type );
		}

		foreach( $this->_services as $type => $service ) {
			$this->_object->setService( $service, $type );
		}

		$this->_object->check( MShop_Order_Item_Base_Abstract::PARTS_SERVICE );
	}


	public function testCheckNoServices()
	{

		foreach( $this->_products as $product ) {
			$this->_object->addProduct( $product );
		}

		foreach( $this->_addresses as $type => $address ) {
			$this->_object->setAddress( $address, $type );
		}

		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$this->_object->check( MShop_Order_Item_Base_Abstract::PARTS_SERVICE );
	}
}