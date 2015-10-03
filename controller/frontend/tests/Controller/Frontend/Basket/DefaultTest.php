<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Controller_Frontend_Basket_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $context;
	private $testItem;


	protected function setUp()
	{
		$this->context = TestHelper::getContext();
		$this->object = new Controller_Frontend_Basket_Default( $this->context );

		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTP' ) );

		$items = $productManager->searchItems( $search, array( 'text' ) );

		if( ( $this->testItem = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}
	}


	protected function tearDown()
	{
		$this->object->clear();
		$this->context->getSession()->set( 'aimeos', array() );

		unset( $this->object, $this->testItem );
	}


	public function testAddDeleteProduct()
	{
		$basket = $this->object->get();


		$this->object->addProduct( $this->testItem->getId(), 2 );

		$this->assertEquals( 1, count( $basket->getProducts() ) );
		$this->assertEquals( 2, $basket->getProduct( 0 )->getQuantity() );
		$this->assertEquals( 'U:TESTPSUB01', $basket->getProduct( 0 )->getProductCode() );


		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );

		$items = $productManager->searchItems( $search, array( 'text' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->object->addProduct( $item->getId(), 2, array(), array(), array(), array(), array(), 'default' );
		$item2 = $this->object->get()->getProduct( 1 );
		$this->object->deleteProduct( 0 );

		$this->assertEquals( 1, count( $basket->getProducts() ) );
		$this->assertEquals( $item2, $basket->getProduct( 1 ) );
		$this->assertEquals( 'CNC', $basket->getProduct( 1 )->getProductCode() );
	}


	public function testAddProductBundle()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:BUNDLE' ) );

		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}


		$this->object->addProduct( $item->getId(), 1 );

		$this->assertEquals( 1, count( $this->object->get()->getProducts() ) );
		$this->assertEquals( 'U:BUNDLE', $this->object->get()->getProduct( 0 )->getProductCode() );
		$this->assertEquals( 2, count( $this->object->get()->getProduct( 0 )->getProducts() ) );
	}


	public function testAddProductVariant()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );

		$items = $productManager->searchItems( $search, array( 'text' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.code', array( 'xs', 'white' ) ) );

		$attributes = $attributeManager->searchItems( $search );

		if( count( $attributes ) === 0 ) {
			throw new Exception( 'Attributes not found' );
		}


		$this->object->addProduct( $item->getId(), 1, array(), array_keys( $attributes ), array(), array(), array(), 'default' );

		$this->assertEquals( 1, count( $this->object->get()->getProducts() ) );
		$this->assertEquals( 'CNC', $this->object->get()->getProduct( 0 )->getProductCode() );
	}


	public function testAddProductVariantIncomplete()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TEST' ) );

		$items = $productManager->searchItems( $search, array() );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.code', '30' ),
			$search->compare( '==', 'attribute.type.code', 'length' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$attributes = $attributeManager->searchItems( $search );

		if( count( $attributes ) === 0 ) {
			throw new Exception( 'Attributes not found' );
		}


		$this->object->addProduct( $item->getId(), 1, array(), array_keys( $attributes ) );

		$this->assertEquals( 1, count( $this->object->get()->getProducts() ) );
		$this->assertEquals( 'U:TESTSUB02', $this->object->get()->getProduct( 0 )->getProductCode() );
		$this->assertEquals( 2, count( $this->object->get()->getProduct( 0 )->getAttributes() ) );
	}


	public function testAddProductVariantNonUnique()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TEST' ) );

		$items = $productManager->searchItems( $search, array() );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.code', '30' ),
			$search->compare( '==', 'attribute.type.code', 'width' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$attributes = $attributeManager->searchItems( $search );

		if( count( $attributes ) === 0 ) {
			throw new Exception( 'Attributes not found' );
		}


		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->addProduct( $item->getId(), 1, array(), array_keys( $attributes ) );
	}


	public function testAddProductVariantNotRequired()
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.code', 'xs' ) );

		$attributes = $attributeManager->searchItems( $search );

		if( count( $attributes ) === 0 ) {
			throw new Exception( 'Attribute not found' );
		}

		$options = array( 'variant' => false );

		$this->object->addProduct( $this->testItem->getId(), 1, $options, array_keys( $attributes ) );

		$this->assertEquals( 1, count( $this->object->get()->getProducts() ) );
		$this->assertEquals( 'U:TESTP', $this->object->get()->getProduct( 0 )->getProductCode() );
	}


	public function testAddProductConfigAttribute()
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.code', 'xs' ) );

		$attributes = $attributeManager->searchItems( $search );

		if( empty( $attributes ) ) {
			throw new Exception( 'Attribute not found' );
		}

		$this->object->addProduct( $this->testItem->getId(), 1, array(), array(), array_keys( $attributes ) );
		$basket = $this->object->get();

		$this->assertEquals( 1, count( $basket->getProducts() ) );
		$this->assertEquals( 'U:TESTPSUB01', $basket->getProduct( 0 )->getProductCode() );
		$this->assertEquals( 'xs', $basket->getProduct( 0 )->getAttribute( 'size', 'config' ) );
	}


	public function testAddProductHiddenAttribute()
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', '29' ),
			$search->compare( '==', 'attribute.type.code', 'width' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$attributes = $attributeManager->searchItems( $search );

		if( empty( $attributes ) ) {
			throw new Exception( 'Attribute not found' );
		}

		$this->object->addProduct( $this->testItem->getId(), 1, array(), array(), array(), array_keys( $attributes ) );

		$basket = $this->object->get();
		$this->assertEquals( 1, count( $basket->getProducts() ) );

		$product = $basket->getProduct( 0 );
		$this->assertEquals( 'U:TESTPSUB01', $product->getProductCode() );

		$attributes = $product->getAttributes();
		$this->assertEquals( 1, count( $attributes ) );

		if( ( $attribute = reset( $attributes ) ) === false ) {
			throw new Exception( 'No attribute' );
		}

		$this->assertEquals( 'hidden', $attribute->getType() );
		$this->assertEquals( '29', $product->getAttribute( 'width', 'hidden' ) );
	}


	public function testAddProductCustomAttribute()
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'custom' ),
			$search->compare( '==', 'attribute.type.code', 'date' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$attributes = $attributeManager->searchItems( $search );

		if( ( $attrItem = reset( $attributes ) ) === false ) {
			throw new Exception( 'Attribute not found' );
		}

		$attrValues = array( $attrItem->getId() => '2000-01-01' );

		$this->object->addProduct( $this->testItem->getId(), 1, array(), array(), array(), array(), $attrValues );
		$basket = $this->object->get();

		$this->assertEquals( 1, count( $basket->getProducts() ) );
		$this->assertEquals( '2000-01-01', $basket->getProduct( 0 )->getAttribute( 'date', 'custom' ) );
	}


	public function testAddProductAttributeNotAssigned()
	{
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $attributeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', '30' ),
			$search->compare( '==', 'attribute.type.code', 'width' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$attribute = $attributeManager->searchItems( $search );

		if( empty( $attribute ) ) {
			throw new Exception( 'Attribute not found' );
		}

		$hiddenAttrIds = array_keys( $attribute );
		$configAttrIds = array_keys( $attribute );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->addProduct( $this->testItem->getId(), 1, array(), array(), $configAttrIds, $hiddenAttrIds );
	}


	public function testAddProductNegativeQuantityException()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->object->addProduct( $this->testItem->getId(), -1 );
	}


	public function testAddProductNotEnoughStockException()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'IJKL' ) );

		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		try
		{
			$this->object->addProduct( $item->getId(), 5, array(), array(), array(), array(), array(), 'unit_warehouse3' );
			throw new Exception( 'Expected exception not thrown' );
		}
		catch( Controller_Frontend_Basket_Exception $e )
		{
			$item = $this->object->get()->getProduct( 0 );
			$this->assertEquals( 3, $item->getQuantity() );
		}
	}


	public function testAddProductNoStockException()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'EFGH' ) );

		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		try
		{
			$this->object->addProduct( $item->getId(), 5, array(), array(), array(), array(), array(), 'unit_warehouse2' );
			throw new Exception( 'Expected exception not thrown' );
		}
		catch( Controller_Frontend_Basket_Exception $e )
		{
			$this->assertEquals( array(), $this->object->get()->getProducts() );
		}
	}


	public function testAddProductNoStockRequired()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'IJKL' ) );

		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->object->addProduct( $item->getId(), 5, array( 'stock' => false ) );
	}


	public function testAddProductNoStockItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'QRST' ) );

		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->addProduct( $item->getId(), 1 );
	}


	public function testAddProductNoPriceException()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'MNOP' ) );

		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->setExpectedException( 'MShop_Price_Exception' );
		$this->object->addProduct( $item->getId(), 1 );
	}


	public function testAddProductConfigAttributeException()
	{
		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->addProduct( $this->testItem->getId(), 1, array(), array(), array( -1 ) );
	}


	public function testAddProductEmptySelectionException()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:noSel' ) );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->addProduct( $item->getId(), 1 );
	}


	public function testAddProductSelectionWithPricelessItem()
	{
		$this->object->addProduct( $this->testItem->getId(), 1 );

		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();

		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTPSUB01' ) );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->assertEquals( 'U:TESTPSUB01', $this->object->get()->getProduct( 0 )->getProductCode() );
	}


	public function testAddProductLowQuantityPriceException()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'IJKL' ) );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->setExpectedException( 'MShop_Price_Exception' );
		$this->object->addProduct( $item->getId(), 1 );
	}


	public function testAddProductHigherQuantities()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'IJKL' ) );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->object->addProduct( $item->getId(), 2, array(), array(), array(), array(), array(), 'unit_warehouse3' );

		$this->assertEquals( 2, $this->object->get()->getProduct( 0 )->getQuantity() );
		$this->assertEquals( 'IJKL', $this->object->get()->getProduct( 0 )->getProductCode() );
	}


	public function testDeleteProductFlagError()
	{
		$this->object->addProduct( $this->testItem->getId(), 2 );

		$item = $this->object->get()->getProduct( 0 );
		$item->setFlags( MShop_Order_Item_Base_Product_Base::FLAG_IMMUTABLE );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->deleteProduct( 0 );
	}


	public function testEditProduct()
	{
		$this->object->addProduct( $this->testItem->getId(), 1 );

		$item = $this->object->get()->getProduct( 0 );
		$this->assertEquals( 1, $item->getQuantity() );

		$this->object->editProduct( 0, 4 );

		$item = $this->object->get()->getProduct( 0 );
		$this->assertEquals( 4, $item->getQuantity() );
		$this->assertEquals( 'U:TESTPSUB01', $item->getProductCode() );
	}


	public function testEditProductAttributes()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTP' ) );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$attributeManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $attributeManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->combine( '||', array(
				$search->combine( '&&', array(
					$search->compare( '==', 'attribute.code', 'xs' ),
					$search->compare( '==', 'attribute.type.code', 'size' ),
				) ),
				$search->combine( '&&', array(
					$search->compare( '==', 'attribute.code', 'white' ),
					$search->compare( '==', 'attribute.type.code', 'color' ),
				) ),
			) )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$attributes = $attributeManager->searchItems( $search );

		if( ( $attribute = reset( $attributes ) ) === false ) {
			throw new Exception( 'No attributes available' );
		}


		$this->object->addProduct( $item->getId(), 1, array(), array(), array_keys( $attributes ) );
		$this->object->editProduct( 0, 4 );

		$item = $this->object->get()->getProduct( 0 );
		$this->assertEquals( 2, count( $item->getAttributes() ) );
		$this->assertEquals( 4, $item->getQuantity() );


		$this->object->editProduct( 0, 3, array(), array( $attribute->getType() ) );

		$item = $this->object->get()->getProduct( 0 );
		$this->assertEquals( 3, $item->getQuantity() );
		$this->assertEquals( 1, count( $item->getAttributes() ) );
		$this->assertEquals( 'U:TESTPSUB01', $item->getProductCode() );
	}


	public function testEditProductNotEnoughStock()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'IJKL' ) );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->object->addProduct( $item->getId(), 2, array(), array(), array(), array(), array(), 'unit_warehouse3' );

		$item = $this->object->get()->getProduct( 0 );
		$this->assertEquals( 2, $item->getQuantity() );

		try
		{
			$this->object->editProduct( 0, 5 );
			throw new Exception( 'Expected exception not thrown' );
		}
		catch( Controller_Frontend_Basket_Exception $e )
		{
			$item = $this->object->get()->getProduct( 0 );
			$this->assertEquals( 3, $item->getQuantity() );
			$this->assertEquals( 'IJKL', $item->getProductCode() );
		}
	}


	public function testEditProductNoStock()
	{
		$context = TestHelper::getContext();

		$productManager = MShop_Factory::createManager( $context, 'product' );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'IJKL' ) );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$orderProductManager = MShop_Factory::createManager( $context, 'order/base/product' );
		$orderProductItem = $orderProductManager->createItem();
		$orderProductItem->copyFrom( $item );
		$orderProductItem->setQuantity( 2 );
		$orderProductItem->setWarehouseCode( 'unit_warehouse3' );

		$pos = $this->object->get()->addProduct( $orderProductItem, 1 );

		$item = $this->object->get()->getProduct( $pos );
		$this->assertEquals( 2, $item->getQuantity() );

		try
		{
			$this->object->editProduct( $pos, 5 );
			throw new Exception( 'Expected exception not thrown' );
		}
		catch( Controller_Frontend_Basket_Exception $e )
		{
			$this->assertEquals( 3, $this->object->get()->getProduct( $pos )->getQuantity() );
		}
	}


	public function testEditProductStockNotChecked()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'IJKL' ) );
		$items = $productManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Product not found' );
		}

		$this->object->addProduct( $item->getId(), 2, array(), array(), array(), array(), array(), 'unit_warehouse3' );

		$item = $this->object->get()->getProduct( 0 );
		$this->assertEquals( 2, $item->getQuantity() );

		$this->object->editProduct( 0, 5, array( 'stock' => false ) );

		$item = $this->object->get()->getProduct( 0 );
		$this->assertEquals( 5, $item->getQuantity() );
		$this->assertEquals( 'IJKL', $item->getProductCode() );
	}


	public function testEditProductFlagError()
	{
		$this->object->addProduct( $this->testItem->getId(), 2 );

		$item = $this->object->get()->getProduct( 0 );
		$item->setFlags( MShop_Order_Item_Base_Product_Base::FLAG_IMMUTABLE );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->editProduct( 0, 4 );
	}


	public function testAddCoupon()
	{
		$this->object->addProduct( $this->testItem->getId(), 2 );
		$this->object->addCoupon( 'GHIJ' );

		$basket = $this->object->get();

		$this->assertEquals( 1, count( $basket->getCoupons() ) );
	}


	public function testAddCouponInvalidCode()
	{
		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->addCoupon( 'invalid' );
	}


	public function testAddCouponMissingRequirements()
	{
		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->addCoupon( 'OPQR' );
	}


	public function testDeleteCoupon()
	{
		$this->object->addProduct( $this->testItem->getId(), 2 );
		$this->object->addCoupon( '90AB' );
		$this->object->deleteCoupon( '90AB' );

		$basket = $this->object->get();

		$this->assertEquals( 0, count( $basket->getCoupons() ) );
	}


	public function testClear()
	{
		$this->object->addProduct( $this->testItem->getId(), 2 );
		$this->object->clear();

		$this->assertEquals( 0, count( $this->object->get()->getProducts() ) );
	}


	public function testSetBillingAddressByItem()
	{
		$item = $this->getAddress( 'Example company' );

		$this->object->setAddress( MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT, $item );

		$address = $this->object->get()->getAddress( MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT );
		$this->assertEquals( 'Example company', $address->getCompany() );
	}


	public function testSetBillingAddressByArray()
	{
		$fixture = array(
			'order.base.address.company' => '<p onclick="javascript: alert(\'gotcha\');">Example company</p>',
			'order.base.address.vatid' => 'DE999999999',
			'order.base.address.title' => '<br/>Dr.',
			'order.base.address.salutation' => MShop_Common_Item_Address_Base::SALUTATION_MR,
			'order.base.address.firstname' => 'firstunit',
			'order.base.address.lastname' => 'lastunit',
			'order.base.address.address1' => 'unit str.',
			'order.base.address.address2' => ' 166',
			'order.base.address.address3' => '4.OG',
			'order.base.address.postal' => '22769',
			'order.base.address.city' => 'Hamburg',
			'order.base.address.state' => 'Hamburg',
			'order.base.address.countryid' => 'de',
			'order.base.address.languageid' => 'de',
			'order.base.address.telephone' => '05554433221',
			'order.base.address.email' => 'test@example.com',
			'order.base.address.telefax' => '05554433222',
			'order.base.address.website' => 'www.example.com',
			'order.base.address.flag' => 0,
		);

		$this->object->setAddress( MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT, $fixture );

		$address = $this->object->get()->getAddress( MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT );
		$this->assertEquals( 'Example company', $address->getCompany() );
		$this->assertEquals( 'Dr.', $address->getTitle() );
		$this->assertEquals( 'firstunit', $address->getFirstname() );
	}


	public function testSetBillingAddressByArrayError()
	{
		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->setAddress( MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT, array( 'error' => false ) );
	}


	public function testSetBillingAddressParameterError()
	{
		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->setAddress( MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT, 'error' );
	}


	public function testSetDeliveryAddressByItem()
	{
		$item = $this->getAddress( 'Example company' );

		$this->object->setAddress( MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY, $item );

		$address = $this->object->get()->getAddress( MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY );
		$this->assertEquals( 'Example company', $address->getCompany() );
	}


	public function testSetDeliveryAddressByArray()
	{
		$fixture = array(
			'order.base.address.company' => '<p onclick="javascript: alert(\'gotcha\');">Example company</p>',
			'order.base.address.vatid' => 'DE999999999',
			'order.base.address.title' => '<br/>Dr.',
			'order.base.address.salutation' => MShop_Common_Item_Address_Base::SALUTATION_MR,
			'order.base.address.firstname' => 'firstunit',
			'order.base.address.lastname' => 'lastunit',
			'order.base.address.address1' => 'unit str.',
			'order.base.address.address2' => ' 166',
			'order.base.address.address3' => '4.OG',
			'order.base.address.postal' => '22769',
			'order.base.address.city' => 'Hamburg',
			'order.base.address.state' => 'Hamburg',
			'order.base.address.countryid' => 'de',
			'order.base.address.languageid' => 'de',
			'order.base.address.telephone' => '05554433221',
			'order.base.address.email' => 'test@example.com',
			'order.base.address.telefax' => '05554433222',
			'order.base.address.website' => 'www.example.com',
			'order.base.address.flag' => 0,
		);
		$this->object->setAddress( MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY, $fixture );

		$address = $this->object->get()->getAddress( MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY );
		$this->assertEquals( 'Example company', $address->getCompany() );
		$this->assertEquals( 'Dr.', $address->getTitle() );
		$this->assertEquals( 'firstunit', $address->getFirstname() );
	}


	public function testSetDeliveryAddressByArrayError()
	{
		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->setAddress( MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY, array( 'error' => false ) );
	}


	public function testSetDeliveryAddressTypeError()
	{
		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->setAddress( MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY, 'error' );
	}


	public function testSetServicePayment()
	{
		$service = $this->getService( 'unitpaymentcode' );

		$this->object->setService( 'payment', $service->getId(), array() );
		$this->assertEquals( 'unitpaymentcode', $this->object->get()->getService( 'payment' )->getCode() );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->setService( 'payment', $service->getId(), array( 'prepay' => true ) );
	}


	public function testSetDeliveryOption()
	{
		$service = $this->getService( 'unitcode' );

		$this->object->setService( 'delivery', $service->getId(), array() );
		$this->assertEquals( 'unitcode', $this->object->get()->getService( 'delivery' )->getCode() );

		$this->setExpectedException( 'Controller_Frontend_Basket_Exception' );
		$this->object->setService( 'delivery', $service->getId(), array( 'fast shipping' => true, 'air shipping' => false ) );
	}


	public function testCheckLocale()
	{
		$this->object->addProduct( $this->testItem->getId(), 2 );
		$this->object->addCoupon( 'OPQR' );

		$this->object->setService( 'payment', $this->getService( 'unitpaymentcode' )->getId() );
		$this->object->setService( 'delivery', $this->getService( 'unitcode' )->getId() );

		$basket = $this->object->get();
		$price = $basket->getPrice();

		foreach( $basket->getProducts() as $product )
		{
			$this->assertEquals( 2, $product->getQuantity() );
			$product->getPrice()->setCurrencyId( 'CHF' );
		}

		$basket->getService( 'delivery' )->getPrice()->setCurrencyId( 'CHF' );
		$basket->getService( 'payment' )->getPrice()->setCurrencyId( 'CHF' );
		$basket->getLocale()->setCurrencyId( 'CHF' );
		$price->setCurrencyId( 'CHF' );

		$this->context->getLocale()->setCurrencyId( 'CHF' );
		$this->object->setAddress( MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT, $this->getAddress( 'Example company' ) );

		$this->context->getSession()->set( 'aimeos/basket/currency', 'CHF' );
		$this->context->getLocale()->setCurrencyId( 'EUR' );

		$this->context->getSession()->set( 'aimeos/basket/content-unittest-en-EUR-', null );

		$object = new Controller_Frontend_Basket_Default( $this->context );
		$basket = $object->get();

		foreach( $basket->getProducts() as $product )
		{
			$this->assertEquals( 'EUR', $product->getPrice()->getCurrencyId() );
			$this->assertEquals( 2, $product->getQuantity() );
		}

		$this->assertEquals( 'EUR', $basket->getService( 'payment' )->getPrice()->getCurrencyId() );
		$this->assertEquals( 'EUR', $basket->getService( 'delivery' )->getPrice()->getCurrencyId() );
		$this->assertEquals( 'EUR', $basket->getLocale()->getCurrencyId() );
		$this->assertEquals( 'EUR', $basket->getPrice()->getCurrencyId() );
	}


	/**
	 * @param string $company
	 */
	protected function getAddress( $company )
	{
		$customer = MShop_Customer_Manager_Factory::createManager( TestHelper::getContext(), 'Default' );
		$addressManager = $customer->getSubManager( 'address', 'Default' );

		$search = $addressManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.address.company', $company ) );
		$items = $addressManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No address item with company "%1$s" found', $company ) );
		}

		return $item;
	}


	/**
	 * @param string $code
	 */
	protected function getService( $code )
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', $code ) );

		$result = $serviceManager->searchItems( $search, array( 'text' ) );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No service item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
