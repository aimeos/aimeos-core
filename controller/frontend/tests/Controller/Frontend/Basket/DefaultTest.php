<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 1182 2012-08-30 14:40:13Z gwussow $
 */

class Controller_Frontend_Basket_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_testItem;


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('Controller_Frontend_Basket_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	protected function setUp()
	{
		$this->_object = new Controller_Frontend_Basket_Default( TestHelper::getContext() );

		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTP') );

		$items = $productManager->searchItems( $search, array( 'text' ) );

		if( ( $this->_testItem = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}
	}


	protected function tearDown()
	{
	}


	public function testAddDeleteProduct()
	{
		$basket = $this->_object->get();


		$this->_object->addProduct( $this->_testItem->getId(), 2 );

		$this->assertEquals( 1, count( $basket->getProducts() ) );
		$this->assertEquals( 2, $basket->getProduct( 0 )->getQuantity() );
		$this->assertEquals( 'U:TESTPSUB01', $basket->getProduct( 0 )->getProductCode() );


		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC') );

		$items = $productManager->searchItems( $search, array( 'text' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->_object->addProduct( $item->getId(), 2 );
		$item2 = $this->_object->get()->getProduct( 1 );
		$this->_object->deleteProduct( 0 );

		$this->assertEquals( 1, count( $basket->getProducts() ) );
		$this->assertEquals( $item2, $basket->getProduct( 1 ) );
		$this->assertEquals( 'CNC', $basket->getProduct( 1 )->getProductCode() );
	}


	public function testAddProductVariant()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC') );

		$items = $productManager->searchItems( $search, array( 'text' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.code', array( 'xs', 'white' ) ) );

		$attributes = $attributeManager->searchItems( $search );

		if( count( $attributes ) === 0) {
			throw new Exception( 'Attributes not found' );
		}


		$this->_object->addProduct( $item->getId(), 1, array(), array_keys( $attributes ) );

		$this->assertEquals( 1, count( $this->_object->get()->getProducts() ) );
		$this->assertEquals( 'CNC', $this->_object->get()->getProduct( 0 )->getProductCode() );
	}


	public function testAddProductVariantNotRequired()
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.code', 'xs' ) );

		$attributes = $attributeManager->searchItems( $search );

		if( count( $attributes ) === 0) {
			throw new Exception( 'Attribute not found' );
		}


		$this->_object->addProduct( $this->_testItem->getId(), 1, array(), array_keys( $attributes ), false );

		$this->assertEquals( 1, count( $this->_object->get()->getProducts() ) );
		$this->assertEquals( 'U:TESTP', $this->_object->get()->getProduct( 0 )->getProductCode() );
	}


	public function testAddProductConfigAttribute()
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.code', 'xs' ) );

		$attributes = $attributeManager->searchItems( $search );

		if( count( $attributes ) === 0) {
			throw new Exception( 'Attribute not found' );
		}


		$this->_object->addProduct( $this->_testItem->getId(), 1, array_keys( $attributes ) );

		$this->assertEquals( 1, count( $this->_object->get()->getProducts() ) );
		$this->assertEquals( 'U:TESTPSUB01', $this->_object->get()->getProduct( 0 )->getProductCode() );
	}


	public function testAddProductNoItemException()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->addProduct( 0 );
	}


	public function testAddProductNegativeQuantityException()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->addProduct( $this->_testItem->getId(), -1 );
	}


	public function testAddProductNoPriceException()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTSUB03') );

		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->setExpectedException( 'MShop_Price_Exception' );
		$this->_object->addProduct( $item->getId(), 1 );
	}


	public function testAddProductLowQuantityException()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTSUB04') );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->setExpectedException( 'MShop_Price_Exception' );
		$this->_object->addProduct( $item->getId(), 1 );
	}


	public function testAddProductAttributeException()
	{
		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->_object->addProduct( $this->_testItem->getId(), 1, array( -1 ) );
	}


	public function testAddProductEmptySelectionException()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:noSel') );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->_object->addProduct( $item->getId(), 1 );
	}


	public function testAddProductSelectionWithPricelessItem()
	{
		$this->_object->addProduct( $this->_testItem->getId(), 1 );

		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();

		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTPSUB01') );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->assertEquals( 'U:TESTPSUB01', $this->_object->get()->getProduct( 0 )->getProductCode() );
	}


	public function testAddProductHigherQuantities()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTSUB05') );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->_object->addProduct( $item->getId(), 5 );

		$this->assertEquals( 5, $this->_object->get()->getProduct( 0 )->getQuantity() );
		$this->assertEquals( 'U:TESTSUB05', $this->_object->get()->getProduct( 0 )->getProductCode() );
	}


	public function testDeleteProductFlagError()
	{
		$this->_object->addProduct( $this->_testItem->getId(), 2 );

		$item = $this->_object->get()->getProduct( 0 );
		$item->setFlags( MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->_object->deleteProduct( 0 );
	}


	public function testEditProduct()
	{
		$this->_object->addProduct( $this->_testItem->getId(), 1 );

		$item = $this->_object->get()->getProduct( 0 );
		$this->assertEquals( 1, $item->getQuantity() );

		$this->_object->editProduct( 0, 4 );

		$item = $this->_object->get()->getProduct( 0 );
		$this->assertEquals( 4, $item->getQuantity() );
		$this->assertEquals( 'U:TESTPSUB01', $item->getProductCode() );
	}


	public function testEditProductAttributes()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTSUB05') );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $attributeManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.code', array( 'm', 'white' ) ),
			$search->compare( '==', 'attribute.domain', 'product' )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$attributes = $attributeManager->searchItems( $search );

		if( ( $attribute = reset( $attributes ) ) === false ) {
			throw new Exception( 'No attributes available' );
		}


		$this->_object->addProduct( $item->getId(), 1, array_keys( $attributes ) );
		$this->_object->editProduct( 0, 4 );

		$item = $this->_object->get()->getProduct( 0 );
		$this->assertEquals( 2, count( $item->getAttributes() ) );
		$this->assertEquals( 4, $item->getQuantity() );


		$this->_object->editProduct( 0, 3, array( $attribute->getType() ), true );

		$item = $this->_object->get()->getProduct( 0 );
		$this->assertEquals( 3, $item->getQuantity() );
		$this->assertEquals( 1, count( $item->getAttributes() ) );
		$this->assertEquals( 'U:TESTSUB05', $item->getProductCode() );
	}


	public function testEditProductFlagError()
	{
		$this->_object->addProduct( $this->_testItem->getId(), 2 );

		$item = $this->_object->get()->getProduct( 0 );
		$item->setFlags( MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->_object->editProduct( 0, 4 );
	}


	public function testClear()
	{
		$this->_object->addProduct( $this->_testItem->getId(), 2 );
		$this->_object->clear();

		$this->assertEquals( 0, count( $this->_object->get()->getProducts() ) );
	}


	public function testSetBillingAddressById()
	{
		$customer = MShop_Customer_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $customer->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.label', 'unitCustomer001' ) );
		$result = $customer->searchItems( $search );

		if( ( $customerItem = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No customer item found for label "%1$s".', 'unitCustomer001' ) );
		}

		$this->_object->setBillingAddress( $customerItem->getId() );

		$address = $this->_object->get()->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING );
		$this->assertEquals( 'Metaways', $address->getCompany() );
	}


	public function testSetBillingAddressByItem()
	{
		$customer = MShop_Customer_Manager_Factory::createManager( TestHelper::getContext() );
		$addressManager = $customer->getSubManager( 'address', 'Default' );

		$search = $addressManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.address.company', 'Metaways' ) );
		$items = $addressManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No address item with company "Metaways" found' );
		}

		$this->_object->setBillingAddress( $item );

		$address = $this->_object->get()->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING );
		$this->assertEquals( 'Metaways', $address->getCompany() );
	}


	public function testSetBillingAddressByArray()
	{
		$fixture = array(
			'customer.company' => '<p onclick="javascript: alert(\'gotcha\');">Metaways</p>',
			'customer.title' => '<br/>Dr.',
			'customer.salutation' => MShop_Common_Item_Address_Abstract::SALUTATION_MR,
			'customer.firstname' => 'firstunit',
			'customer.lastname' => 'lastunit',
			'customer.address1' => 'unit str.',
			'customer.address2' => ' 166',
			'customer.address3' => '4.OG',
			'customer.postal' => '22769',
			'customer.city' => 'Hamburg',
			'customer.state' => 'Hamburg',
			'customer.countryid' => 'de',
			'customer.langid' => 'de',
			'customer.telephone' => '05554433221',
			'customer.email' => 'unit.test@metaways.de',
			'customer.telefax' => '05554433222',
			'customer.website' => 'www.metaways.de',
			'customer.flag' => 0,
		);

		$this->_object->setBillingAddress( $fixture );

		$address = $this->_object->get()->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING );
		$this->assertEquals( 'Metaways', $address->getCompany() );
		$this->assertEquals( 'Dr.', $address->getTitle() );
		$this->assertEquals( 'firstunit', $address->getFirstname() );
	}


	public function testSetBillingAddressByArrayError()
	{
		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->_object->setBillingAddress( array( 'error' => false ) );
	}


	public function testSetBillingAddressParameterError()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setBillingAddress( 'error' );
	}


	public function testSetBillingAddressByIdError()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setBillingAddress( -1 );
	}


	public function testSetDeliveryAddressById()
	{
		$customer = MShop_Customer_Manager_Factory::createManager( TestHelper::getContext() );
		$addressManager = $customer->getSubManager( 'address', 'Default' );

		$search = $addressManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.address.company', 'Metaways' ) );
		$items = $addressManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No address item with company "Metaways" found' );
		}

		$this->_object->setDeliveryAddress( $item->getId() );

		$address = $this->_object->get()->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->assertEquals( 'Metaways', $address->getCompany() );
	}


	public function testSetDeliveryAddressByItem()
	{
		$customer = MShop_Customer_Manager_Factory::createManager( TestHelper::getContext() );
		$addressManager = $customer->getSubManager( 'address', 'Default' );

		$search = $addressManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.address.company', 'Metaways' ) );
		$items = $addressManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No address item with company "Metaways" found' );
		}

		$this->_object->setDeliveryAddress( $item );

		$address = $this->_object->get()->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->assertEquals( 'Metaways', $address->getCompany() );
	}


	public function testSetDeliveryAddressByArray()
	{
		$fixture = array(
			'customer.address.company' => '<p onclick="javascript: alert(\'gotcha\');">Metaways</p>',
			'customer.address.title' => '<br/>Dr.',
			'customer.address.salutation' => MShop_Common_Item_Address_Abstract::SALUTATION_MR,
			'customer.address.firstname' => 'firstunit',
			'customer.address.lastname' => 'lastunit',
			'customer.address.address1' => 'unit str.',
			'customer.address.address2' => ' 166',
			'customer.address.address3' => '4.OG',
			'customer.address.postal' => '22769',
			'customer.address.city' => 'Hamburg',
			'customer.address.state' => 'Hamburg',
			'customer.address.countryid' => 'de',
			'customer.address.langid' => 'de',
			'customer.address.telephone' => '05554433221',
			'customer.address.email' => 'unit.test@metaways.de',
			'customer.address.telefax' => '05554433222',
			'customer.address.website' => 'www.metaways.de',
			'customer.address.flag' => 0,
		);
		$this->_object->setDeliveryAddress( $fixture );

		$address = $this->_object->get()->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->assertEquals( 'Metaways', $address->getCompany() );
		$this->assertEquals( 'Dr.', $address->getTitle() );
		$this->assertEquals( 'firstunit', $address->getFirstname() );
	}


	public function testSetDeliveryAddressByArrayError()
	{
		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->_object->setDeliveryAddress( array( 'error' => false ) );
	}


	public function testSetDeliveryAddressTypeError()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setDeliveryAddress( 'error' );
	}


	public function testSetDeliveryAddressByIdError()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setDeliveryAddress( -1 );
	}


	public function testSetPaymentOption()
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode') );
		$result = $serviceManager->searchItems( $search, array( 'text' ) );

		if( ( $service = reset( $result ) ) === false ) {
			throw new Exception('No item found');
		}

		$this->_object->setPaymentOption( $service->getId(), array() );
		$this->assertEquals( 'unitpaymentcode', $this->_object->get()->getService( 'payment' )->getCode() );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->_object->setPaymentOption( $service->getId(), array( 'prepay' => true ) );
	}


	public function testSetDeliveryOption()
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitcode') );

		$result = $serviceManager->searchItems( $search, array( 'text' ) );

		if( ( $service = reset( $result ) ) === false ) {
			throw new Exception('No item found');
		}

		$this->_object->setDeliveryOption( $service->getId(), array() );
		$this->assertEquals( 'unitcode', $this->_object->get()->getService( 'delivery' )->getCode() );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->_object->setDeliveryOption( $service->getId(), array( 'fast shipping' => true, 'air shipping' => false ) );
	}
}
