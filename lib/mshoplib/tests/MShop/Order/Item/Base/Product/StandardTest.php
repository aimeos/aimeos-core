<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Order_Item_Base_Product_Standard.
 */
class MShop_Order_Item_Base_Product_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $values;
	private $price;
	private $attribute = array();
	private $subProducts;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->price = MShop_Price_Manager_Factory::createManager( TestHelper::getContext() )->createItem();

		$attrValues = array(
			'id' => 4,
			'siteid' => 99,
			'ordprodid' => 11,
			'type' => 'default',
			'code' => 'size',
			'value' => '30',
			'name' => 'small',
			'mtime' => '2011-01-06 13:20:34',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);
		$this->attribute = array( new MShop_Order_Item_Base_Product_Attribute_Standard( $attrValues ) );

		$this->values = array(
			'id' => 1,
			'siteid' => 99,
			'ordprodid' => 10,
			'type' => 'bundle',
			'prodid' => 10,
			'baseid' => 42,
			'suppliercode' => 'UnitSupplier',
			'productid' => 111,
			'prodcode' => 'UnitProd',
			'warehousecode' => 'unitwarehouse',
			'name' => 'UnitProduct',
			'mediaurl' => 'testurl',
			'quantity' => 11,
			'flags' => MShop_Order_Item_Base_Product_Base::FLAG_NONE,
			'status' => MShop_Order_Item_Base::STAT_PROGRESS,
			'pos' => 1,
			'mtime' => '2000-12-31 23:59:59',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser',
		);

		$this->subProducts = array(
			new MShop_Order_Item_Base_Product_Standard( clone $this->price ),
			new MShop_Order_Item_Base_Product_Standard( clone $this->price )
		);
		$this->object = new MShop_Order_Item_Base_Product_Standard( $this->price, $this->values, $this->attribute, $this->subProducts );
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

	public function testCompare()
	{
		$product = new MShop_Order_Item_Base_Product_Standard( $this->price, $this->values, $this->attribute, $this->subProducts );
		$this->assertTrue( $this->object->compare( $product ) );
	}

	public function testCompareFail()
	{
		$price = clone $this->price;
		$price->setValue( '1.00' );

		$product = new MShop_Order_Item_Base_Product_Standard( $price, $this->values, $this->attribute, $this->subProducts );
		$this->assertFalse( $this->object->compare( $product ) );
	}

	public function testGetId()
	{
		$this->assertEquals( $this->values['id'], $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$this->object->setId( 5 );
		$this->assertEquals( 5, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->setId( 6 );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetOrderProductId()
	{
		$this->assertEquals( 10, $this->object->getOrderProductId() );
	}


	public function testSetOrderProductId()
	{
		$this->object->setOrderProductId( 1001 );
		$this->assertEquals( 1001, $this->object->getOrderProductId() );
		$this->assertTrue( $this->object->isModified() );

		$this->object->setOrderProductId( null );
		$this->assertEquals( null, $this->object->getOrderProductId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'bundle', $this->object->getType() );
	}


	public function testSetType()
	{
		$this->object->setType( 'default' );
		$this->assertEquals( 'default', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSupplierCode()
	{
		$this->assertEquals( $this->values['suppliercode'], $this->object->getSupplierCode() );
	}

	public function testSetSupplierCode()
	{
		$this->object->setSupplierCode( 'testId' );
		$this->assertEquals( 'testId', $this->object->getSupplierCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetProductId()
	{
		$this->assertEquals( $this->values['prodid'], $this->object->getProductId() );
	}

	public function testSetProductId()
	{
		$this->object->setProductId( 'testProdId' );
		$this->assertEquals( 'testProdId', $this->object->getProductId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetProductCode()
	{
		$this->assertEquals( $this->values['prodcode'], $this->object->getProductCode() );
	}

	public function testSetProductCode()
	{
		$this->object->setProductCode( 'testProdCode' );
		$this->assertEquals( 'testProdCode', $this->object->getProductCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetWarehouseCode()
	{
		$this->assertEquals( $this->values['warehousecode'], $this->object->getWarehouseCode() );
	}

	public function testSetWarehouseCode()
	{
		$this->object->setWarehouseCode( 'testWarehouseCode' );
		$this->assertEquals( 'testWarehouseCode', $this->object->getWarehouseCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetName()
	{
		$this->assertEquals( $this->values['name'], $this->object->getName() );
	}

	public function testSetName()
	{
		$this->object->setName( 'Testname2' );
		$this->assertEquals( 'Testname2', $this->object->getName() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetMediaUrl()
	{
		$this->assertEquals( $this->values['mediaurl'], $this->object->getMediaUrl() );
	}

	public function testSetMediaUrl()
	{
		$this->object->setMediaUrl( 'testUrl' );
		$this->assertEquals( 'testUrl', $this->object->getMediaUrl() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetQuantity()
	{
		$this->assertEquals( $this->values['quantity'], $this->object->getQuantity() );
	}

	public function testSetQuantity()
	{
		$this->object->setQuantity( 20 );
		$this->assertEquals( 20, $this->object->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testSetQuantityDecimal()
	{
		$this->object->setQuantity( 1.5 );
		$this->assertEquals( 1, $this->object->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testSetQuantityNoNumber()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->object->setQuantity( 'a' );
	}

	public function testSetQuantityNegative()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->object->setQuantity( -5 );
	}

	public function testSetQuantityZero()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->object->setQuantity( 0 );
	}

	public function testSetQuantityOverflow()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->object->setQuantity( 2147483648 );
	}

	public function testGetPrice()
	{
		$this->assertSame( $this->price, $this->object->getPrice() );
	}

	public function testSetPrice()
	{
		$this->price->setValue( '5.00' );
		$this->object->setPrice( $this->price );
		$this->assertSame( $this->price, $this->object->getPrice() );
		$this->assertFalse( $this->object->isModified() );
	}

	public function testGetSumPrice()
	{
		$qty = $this->values['quantity'];
		$this->assertEquals( $this->price->getValue() * $qty, $this->object->getSumPrice()->getValue() );
		$this->assertEquals( $this->price->getCosts() * $qty, $this->object->getSumPrice()->getCosts() );
		$this->assertEquals( $this->price->getRebate() * $qty, $this->object->getSumPrice()->getRebate() );
		$this->assertEquals( $this->price->getTaxRate(), $this->object->getSumPrice()->getTaxRate() );
	}

	public function testGetFlags()
	{
		$this->assertEquals( $this->values['flags'], $this->object->getFlags() );
	}

	public function testSetFlags()
	{
		$this->object->setFlags( MShop_Order_Item_Base_Product_Base::FLAG_IMMUTABLE );
		$this->assertEquals( MShop_Order_Item_Base_Product_Base::FLAG_IMMUTABLE, $this->object->getFlags() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->object->getPosition() );
	}

	public function testSetPosition()
	{
		$this->object->setPosition( 2 );
		$this->assertEquals( 2, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testSetPositionReset()
	{
		$this->object->setPosition( null );
		$this->assertEquals( null, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testSetPositionInvalid()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->object->setPosition( 0 );
	}

	public function testGetStatus()
	{
		$this->assertEquals( $this->values['status'], $this->object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->object->setStatus( MShop_Order_Item_Base::STAT_LOST );
		$this->assertEquals( MShop_Order_Item_Base::STAT_LOST, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetBaseId()
	{
		$this->assertEquals( 42, $this->object->getBaseId() );
	}

	public function testSetBaseId()
	{
		$this->object->setBaseId( 111 );
		$this->assertEquals( 111, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testSetBaseIdReset()
	{
		$this->object->setBaseId( null );
		$this->assertEquals( null, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetTimeModified()
	{
		$regexp = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/';
		$this->assertRegExp( $regexp, $this->object->getTimeModified() );
		$this->assertEquals( '2000-12-31 23:59:59', $this->object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}

	public function testGetAttribute()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'product' )->getSubManager( 'attribute' );

		$attrItem001 = $attManager->createItem();
		$attrItem001->setCode( 'code_001' );
		$attrItem001->setValue( 'value_001' );

		$attrItem002 = $attManager->createItem();
		$attrItem002->setCode( 'code_002' );
		$attrItem002->setType( 'test_002' );
		$attrItem002->setValue( 'value_002' );

		$this->object->setAttributes( array( $attrItem001, $attrItem002 ) );

		$result = $this->object->getAttribute( 'code_001' );
		$this->assertEquals( 'value_001', $result );

		$result = $this->object->getAttribute( 'code_002', 'test_002' );
		$this->assertEquals( 'value_002', $result );

		$result = $this->object->getAttribute( 'code_002' );
		$this->assertEquals( null, $result );

		$result = $this->object->getAttribute( 'code_003' );
		$this->assertEquals( null, $result );

		$this->object->setAttributes( array() );

		$result = $this->object->getAttribute( 'code_001' );
		$this->assertEquals( null, $result );
	}

	public function testGetAttributeItem()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'product' )->getSubManager( 'attribute' );

		$attrItem001 = $attManager->createItem();
		$attrItem001->setCode( 'code_001' );
		$attrItem001->setValue( 'value_001' );

		$attrItem002 = $attManager->createItem();
		$attrItem002->setCode( 'code_002' );
		$attrItem002->setType( 'test_002' );
		$attrItem002->setValue( 'value_002' );

		$this->object->setAttributes( array( $attrItem001, $attrItem002 ) );

		$result = $this->object->getAttributeItem( 'code_001' );
		$this->assertEquals( 'value_001', $result->getValue() );

		$result = $this->object->getAttributeItem( 'code_002', 'test_002' );
		$this->assertEquals( 'value_002', $result->getValue() );

		$result = $this->object->getAttributeItem( 'code_002' );
		$this->assertEquals( null, $result );

		$result = $this->object->getAttribute( 'code_003' );
		$this->assertEquals( null, $result );

		$this->object->setAttributes( array() );

		$result = $this->object->getAttribute( 'code_001' );
		$this->assertEquals( null, $result );
	}

	public function testGetAttributes()
	{
		$this->assertEquals( $this->attribute, $this->object->getAttributes() );
	}

	public function testGetAttributesByType()
	{
		$this->assertEquals( $this->attribute, $this->object->getAttributes( 'default' ) );
	}

	public function testGetAttributesInvalidType()
	{
		$this->assertEquals( array(), $this->object->getAttributes( 'invalid' ) );
	}

	public function testSetAttributeItem()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'product' )->getSubManager( 'attribute' );

		$item = $attManager->createItem();
		$item->setCode( 'test_code' );
		$item->setType( 'test_type' );
		$item->setValue( 'test_value' );

		$this->object->setAttributeItem( $item );

		$this->assertEquals( true, $this->object->isModified() );
		$this->assertEquals( 'test_value', $this->object->getAttributeItem( 'test_code', 'test_type' )->getValue() );

		$item = $attManager->createItem();
		$item->setCode( 'test_code' );
		$item->setType( 'test_type' );
		$item->setValue( 'test_value2' );

		$this->object->setAttributeItem( $item );

		$this->assertEquals( 'test_value2', $this->object->getAttributeItem( 'test_code', 'test_type' )->getValue() );
	}

	public function testSetAttributes()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'product' )->getSubManager( 'attribute' );

		$list = array(
			$attManager->createItem(),
			$attManager->createItem(),
		);

		$this->object->setAttributes( $list );

		$this->assertEquals( true, $this->object->isModified() );
		$this->assertEquals( $list, $this->object->getAttributes() );
	}

	public function testGetProducts()
	{
		$this->assertEquals( $this->subProducts, $this->object->getProducts() );
	}

	public function testSetProducts()
	{
		$this->object->setProducts( array() );
		$this->assertEquals( array(), $this->object->getProducts() );

		$this->object->setProducts( $this->subProducts );
		$this->assertEquals( $this->subProducts, $this->object->getProducts() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testFromArray()
	{
		$item = new MShop_Order_Item_Base_Product_Standard( new MShop_Price_Item_Standard() );

		$list = array(
			'order.base.product.id' => 1,
			'order.base.product.baseid' => 2,
			'order.base.product.productid' => 3,
			'order.base.product.prodcode' => 'test',
			'order.base.product.name' => 'test item',
			'order.base.product.suppliercode' => 'testsup',
			'order.base.product.prodcode' => 'test',
			'order.base.product.mediaurl' => '/path/to/image.jpg',
			'order.base.product.position' => 4,
			'order.base.product.quantity' => 5,
			'order.base.product.status' => 0,
			'order.base.product.flags' => 1,
			'order.base.product.price' => '10.00',
			'order.base.product.costs' => '5.00',
			'order.base.product.rebate' => '2.00',
			'order.base.product.taxrate' => '20.00',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['order.base.product.id'], $item->getId() );
		$this->assertEquals( $list['order.base.product.baseid'], $item->getBaseId() );
		$this->assertEquals( $list['order.base.product.productid'], $item->getProductId() );
		$this->assertEquals( $list['order.base.product.prodcode'], $item->getProductCode() );
		$this->assertEquals( $list['order.base.product.name'], $item->getName() );
		$this->assertEquals( $list['order.base.product.suppliercode'], $item->getSupplierCode() );
		$this->assertEquals( $list['order.base.product.prodcode'], $item->getProductCode() );
		$this->assertEquals( $list['order.base.product.mediaurl'], $item->getMediaUrl() );
		$this->assertEquals( $list['order.base.product.position'], $item->getPosition() );
		$this->assertEquals( $list['order.base.product.quantity'], $item->getQuantity() );
		$this->assertEquals( $list['order.base.product.status'], $item->getStatus() );
		$this->assertEquals( $list['order.base.product.flags'], $item->getFlags() );
		$this->assertEquals( $list['order.base.product.price'], $item->getPrice()->getValue() );
		$this->assertEquals( $list['order.base.product.costs'], $item->getPrice()->getCosts() );
		$this->assertEquals( $list['order.base.product.rebate'], $item->getPrice()->getRebate() );
		$this->assertEquals( $list['order.base.product.taxrate'], $item->getPrice()->getTaxRate() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();

		$this->assertEquals( $this->object->getId(), $arrayObject['order.base.product.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['order.base.product.siteid'] );
		$this->assertEquals( $this->object->getBaseId(), $arrayObject['order.base.product.baseid'] );
		$this->assertEquals( $this->object->getSupplierCode(), $arrayObject['order.base.product.suppliercode'] );
		$this->assertEquals( $this->object->getProductId(), $arrayObject['order.base.product.productid'] );
		$this->assertEquals( $this->object->getProductCode(), $arrayObject['order.base.product.prodcode'] );
		$this->assertEquals( $this->object->getName(), $arrayObject['order.base.product.name'] );
		$this->assertEquals( $this->object->getMediaUrl(), $arrayObject['order.base.product.mediaurl'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['order.base.product.position'] );
		$this->assertEquals( $this->object->getPrice()->getValue(), $arrayObject['order.base.product.price'] );
		$this->assertEquals( $this->object->getPrice()->getCosts(), $arrayObject['order.base.product.costs'] );
		$this->assertEquals( $this->object->getPrice()->getRebate(), $arrayObject['order.base.product.rebate'] );
		$this->assertEquals( $this->object->getPrice()->getTaxRate(), $arrayObject['order.base.product.taxrate'] );
		$this->assertEquals( $this->object->getQuantity(), $arrayObject['order.base.product.quantity'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['order.base.product.status'] );
		$this->assertEquals( $this->object->getFlags(), $arrayObject['order.base.product.flags'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['order.base.product.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['order.base.product.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['order.base.product.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['order.base.product.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}

	public function testCopyFrom()
	{
		$productCopy = new MShop_Order_Item_Base_Product_Standard( $this->price );

		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNE' ) );
		$products = $manager->searchItems( $search );
		if( ( $product = reset( $products ) ) !== false ) {
			$productCopy->copyFrom( $product );
		}


		$this->assertEquals( 'Cafe Noire Expresso', $productCopy->getName() );
		$this->assertEquals( 'unitSupplier', $productCopy->getSupplierCode() );
		$this->assertEquals( 'default', $productCopy->getType() );
		$this->assertEquals( 'CNE', $productCopy->getProductCode() );
		$this->assertEquals( $product->getId(), $productCopy->getProductId() );
		$this->assertEquals( MShop_Order_Item_Base::STAT_UNFINISHED, $productCopy->getStatus() );
		$this->assertEquals( '', $productCopy->getMediaUrl() );

		$this->assertTrue( $productCopy->isModified() );
	}
}
