<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Order_Item_Base_Product_Default.
 */
class MShop_Order_Item_Base_Product_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_values;
	private $_price;
	private $_attribute = array();
	private $_subProducts;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_price = MShop_Price_Manager_Factory::createManager( TestHelper::getContext() )->createItem();

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
		$this->_attribute = array( new MShop_Order_Item_Base_Product_Attribute_Default( $attrValues ) );

		$this->_values = array(
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
			'flags' => MShop_Order_Item_Base_Product_Abstract::FLAG_NONE,
			'status' => MShop_Order_Item_Abstract::STAT_PROGRESS,
			'pos' => 1,
			'mtime' => '2000-12-31 23:59:59',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser',
		);

		$this->_subProducts = array(
			new MShop_Order_Item_Base_Product_Default( clone $this->_price ),
			new MShop_Order_Item_Base_Product_Default( clone $this->_price )
		);
		$this->_object = new MShop_Order_Item_Base_Product_Default( $this->_price, $this->_values, $this->_attribute, $this->_subProducts );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset($this->_object);
	}

	public function testCompare()
	{
		$product = new MShop_Order_Item_Base_Product_Default( $this->_price, $this->_values, $this->_attribute, $this->_subProducts );
		$this->assertTrue( $this->_object->compare( $product ) );
	}

	public function testCompareFail()
	{
		$price = clone $this->_price;
		$price->setValue( '1.00' );

		$product = new MShop_Order_Item_Base_Product_Default( $price, $this->_values, $this->_attribute, $this->_subProducts );
		$this->assertFalse( $this->_object->compare( $product ) );
	}

	public function testGetId()
	{
		$this->assertEquals($this->_values['id'], $this->_object->getId());
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

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}

	public function testGetOrderProductId()
	{
		$this->assertEquals( 10, $this->_object->getOrderProductId() );
	}


	public function testSetOrderProductId()
	{
		$this->_object->setOrderProductId( 1001 );
		$this->assertEquals( 1001, $this->_object->getOrderProductId() );
		$this->assertTrue( $this->_object->isModified() );

		$this->_object->setOrderProductId( null );
		$this->assertEquals( null, $this->_object->getOrderProductId() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'bundle', $this->_object->getType() );
	}


	public function testSetType()
	{
		$this->_object->setType( 'default' );
		$this->assertEquals( 'default', $this->_object->getType() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetSupplierCode()
	{
		$this->assertEquals($this->_values['suppliercode'], $this->_object->getSupplierCode());
	}

	public function testSetSupplierCode()
	{
		$this->_object->setSupplierCode('testId');
		$this->assertEquals('testId', $this->_object->getSupplierCode());
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetProductId()
	{
		$this->assertEquals($this->_values['prodid'], $this->_object->getProductId());
	}

	public function testSetProductId()
	{
		$this->_object->setProductId('testProdId');
		$this->assertEquals('testProdId', $this->_object->getProductId());
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetProductCode()
	{
		$this->assertEquals($this->_values['prodcode'], $this->_object->getProductCode());
	}

	public function testSetProductCode()
	{
		$this->_object->setProductCode('testProdCode');
		$this->assertEquals('testProdCode', $this->_object->getProductCode());
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetWarehouseCode()
	{
		$this->assertEquals( $this->_values['warehousecode'], $this->_object->getWarehouseCode() );
	}

	public function testSetWarehouseCode()
	{
		$this->_object->setWarehouseCode( 'testWarehouseCode' );
		$this->assertEquals( 'testWarehouseCode', $this->_object->getWarehouseCode() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetName()
	{
		$this->assertEquals($this->_values['name'], $this->_object->getName());
	}

	public function testSetName()
	{
		$this->_object->setName('Testname2');
		$this->assertEquals('Testname2', $this->_object->getName());
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetMediaUrl()
	{
		$this->assertEquals($this->_values['mediaurl'], $this->_object->getMediaUrl());
	}

	public function testSetMediaUrl()
	{
		$this->_object->setMediaUrl('testUrl');
		$this->assertEquals('testUrl', $this->_object->getMediaUrl());
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetQuantity()
	{
		$this->assertEquals($this->_values['quantity'], $this->_object->getQuantity());
	}

	public function testSetQuantity()
	{
		$this->_object->setQuantity( 20 );
		$this->assertEquals( 20, $this->_object->getQuantity() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testSetQuantityDecimal()
	{
		$this->_object->setQuantity( 1.5 );
		$this->assertEquals( 1, $this->_object->getQuantity() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testSetQuantityNoNumber()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->setQuantity( 'a' );
	}

	public function testSetQuantityNegative()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->setQuantity( -5 );
	}

	public function testSetQuantityZero()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->setQuantity( 0 );
	}

	public function testSetQuantityOverflow()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->setQuantity( 2147483648 );
	}

	public function testGetPrice()
	{
		$this->assertSame($this->_price, $this->_object->getPrice());
	}

	public function testSetPrice()
	{
		$this->_price->setValue('5.00');
		$this->_object->setPrice($this->_price);
		$this->assertSame($this->_price, $this->_object->getPrice());
		$this->assertFalse($this->_object->isModified());
	}

	public function testGetSumPrice()
	{
		$qty = $this->_values['quantity'];
		$this->assertEquals($this->_price->getValue() * $qty, $this->_object->getSumPrice()->getValue());
		$this->assertEquals($this->_price->getCosts() * $qty, $this->_object->getSumPrice()->getCosts());
		$this->assertEquals($this->_price->getRebate() * $qty, $this->_object->getSumPrice()->getRebate());
		$this->assertEquals($this->_price->getTaxRate(), $this->_object->getSumPrice()->getTaxRate());
	}

	public function testGetFlags()
	{
		$this->assertEquals($this->_values['flags'], $this->_object->getFlags());
	}

	public function testSetFlags()
	{
		$this->_object->setFlags(MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE);
		$this->assertEquals(MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE, $this->_object->getFlags());
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->_object->getPosition() );
	}

	public function testSetPosition()
	{
		$this->_object->setPosition( 2 );
		$this->assertEquals( 2, $this->_object->getPosition() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testSetPositionReset()
	{
		$this->_object->setPosition( null );
		$this->assertEquals( null, $this->_object->getPosition() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testSetPositionInvalid()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->setPosition( 0 );
	}

	public function testGetStatus()
	{
		$this->assertEquals($this->_values['status'], $this->_object->getStatus());
	}

	public function testSetStatus()
	{
		$this->_object->setStatus( MShop_Order_Item_Abstract::STAT_LOST );
		$this->assertEquals( MShop_Order_Item_Abstract::STAT_LOST, $this->_object->getStatus() );
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetBaseId()
	{
		$this->assertEquals(42, $this->_object->getBaseId());
	}

	public function testSetBaseId()
	{
		$this->_object->setBaseId(111);
		$this->assertEquals(111, $this->_object->getBaseId());
		$this->assertTrue($this->_object->isModified());
	}

	public function testSetBaseIdReset()
	{
		$this->_object->setBaseId( null );
		$this->assertEquals( null, $this->_object->getBaseId() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetTimeModified()
	{
		$regexp = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/';
		$this->assertRegExp($regexp, $this->_object->getTimeModified());
		$this->assertEquals( '2000-12-31 23:59:59', $this->_object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->_object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}

	public function testGetAttribute()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'product' )->getSubManager( 'attribute' );

		$attrItem001 = $attManager->createItem();
		$attrItem001->setCode( 'code_001');
		$attrItem001->setValue( 'value_001');

		$attrItem002 = $attManager->createItem();
		$attrItem002->setCode( 'code_002');
		$attrItem002->setValue( 'value_002');

		$this->_object->setAttributes( array( $attrItem001, $attrItem002 ) );

		$result = $this->_object->getAttribute( 'code_001' );
		$this->assertEquals( 'value_001', $result );

		$result = $this->_object->getAttribute( 'code_003' );
		$this->assertEquals( null, $result );

		$this->_object->setAttributes( array() );

		$result = $this->_object->getAttribute( 'code_001' );
		$this->assertEquals( null, $result );
	}

	public function testGetAttributeItem()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'product' )->getSubManager( 'attribute' );

		$attrItem001 = $attManager->createItem();
		$attrItem001->setCode( 'code_001');
		$attrItem001->setValue( 'value_001');

		$attrItem002 = $attManager->createItem();
		$attrItem002->setCode( 'code_002');
		$attrItem002->setValue( 'value_002');

		$this->_object->setAttributes( array( $attrItem001, $attrItem002 ) );

		$result = $this->_object->getAttributeItem( 'code_001' );
		$this->assertEquals( 'value_001', $result->getValue() );

		$result = $this->_object->getAttribute( 'code_003' );
		$this->assertEquals( null, $result );

		$this->_object->setAttributes( array() );

		$result = $this->_object->getAttribute( 'code_001' );
		$this->assertEquals( null, $result );
	}

	public function testGetAttributes()
	{
		$this->assertEquals( $this->_attribute, $this->_object->getAttributes() );
	}

	public function testGetAttributesByType()
	{
		$this->assertEquals( $this->_attribute, $this->_object->getAttributes( 'default' ) );
	}

	public function testGetAttributesInvalidType()
	{
		$this->assertEquals( array(), $this->_object->getAttributes( 'invalid' ) );
	}

	public function testSetAttributes()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'product' )->getSubManager( 'attribute' );

		$list = array(
			$attManager->createItem(),
			$attManager->createItem(),
		);

		$this->_object->setAttributes( $list );

		$this->assertEquals( true, $this->_object->isModified() );
		$this->assertEquals( $list, $this->_object->getAttributes() );
	}

	public function testGetProducts()
	{
		$this->assertEquals( $this->_subProducts, $this->_object->getProducts() );
	}

	public function testSetProducts()
	{
		$this->_object->setProducts( array() );
		$this->assertEquals( array(), $this->_object->getProducts() );

		$this->_object->setProducts( $this->_subProducts );
		$this->assertEquals( $this->_subProducts, $this->_object->getProducts() );
		$this->assertEquals( true, $this->_object->isModified() );
	}


	public function testFromArray()
	{
		$item = new MShop_Order_Item_Base_Product_Default(new MShop_Price_Item_Default());

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

		$unknown = $item->fromArray($list);

		$this->assertEquals(array(), $unknown);

		$this->assertEquals($list['order.base.product.id'], $item->getId());
		$this->assertEquals($list['order.base.product.baseid'], $item->getBaseId());
		$this->assertEquals($list['order.base.product.productid'], $item->getProductId());
		$this->assertEquals($list['order.base.product.prodcode'], $item->getProductCode());
		$this->assertEquals($list['order.base.product.name'], $item->getName());
		$this->assertEquals($list['order.base.product.suppliercode'], $item->getSupplierCode());
		$this->assertEquals($list['order.base.product.prodcode'], $item->getProductCode());
		$this->assertEquals($list['order.base.product.mediaurl'], $item->getMediaUrl());
		$this->assertEquals($list['order.base.product.position'], $item->getPosition());
		$this->assertEquals($list['order.base.product.quantity'], $item->getQuantity());
		$this->assertEquals($list['order.base.product.status'], $item->getStatus());
		$this->assertEquals($list['order.base.product.flags'], $item->getFlags());
		$this->assertEquals($list['order.base.product.price'], $item->getPrice()->getValue());
		$this->assertEquals($list['order.base.product.costs'], $item->getPrice()->getCosts());
		$this->assertEquals($list['order.base.product.rebate'], $item->getPrice()->getRebate());
		$this->assertEquals($list['order.base.product.taxrate'], $item->getPrice()->getTaxRate());
	}


	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();

		$this->assertEquals( $this->_object->getId(), $arrayObject['order.base.product.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['order.base.product.siteid'] );
		$this->assertEquals( $this->_object->getBaseId(), $arrayObject['order.base.product.baseid'] );
		$this->assertEquals( $this->_object->getSupplierCode(), $arrayObject['order.base.product.suppliercode'] );
		$this->assertEquals( $this->_object->getProductId(), $arrayObject['order.base.product.productid'] );
		$this->assertEquals( $this->_object->getProductCode(), $arrayObject['order.base.product.prodcode'] );
		$this->assertEquals( $this->_object->getName(), $arrayObject['order.base.product.name'] );
		$this->assertEquals( $this->_object->getMediaUrl(), $arrayObject['order.base.product.mediaurl'] );
		$this->assertEquals( $this->_object->getPosition(), $arrayObject['order.base.product.position'] );
		$this->assertEquals( $this->_object->getPrice()->getValue(), $arrayObject['order.base.product.price'] );
		$this->assertEquals( $this->_object->getPrice()->getCosts(), $arrayObject['order.base.product.costs'] );
		$this->assertEquals( $this->_object->getPrice()->getRebate(), $arrayObject['order.base.product.rebate'] );
		$this->assertEquals( $this->_object->getPrice()->getTaxRate(), $arrayObject['order.base.product.taxrate'] );
		$this->assertEquals( $this->_object->getQuantity(), $arrayObject['order.base.product.quantity'] );
		$this->assertEquals( $this->_object->getStatus(), $arrayObject['order.base.product.status'] );
		$this->assertEquals( $this->_object->getFlags(), $arrayObject['order.base.product.flags'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['order.base.product.mtime'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['order.base.product.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['order.base.product.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['order.base.product.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}

	public function testCopyFrom()
	{
		$productCopy = new MShop_Order_Item_Base_Product_Default( $this->_price );

		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNE') );
		$products = $manager->searchItems( $search );
		if( ( $product = reset( $products ) ) !== false ) {
			$productCopy->copyFrom( $product );
		}


		$this->assertEquals( 'Cafe Noire Expresso', $productCopy->getName() );
		$this->assertEquals( 'unitSupplier', $productCopy->getSupplierCode() );
		$this->assertEquals( 'default', $productCopy->getType() );
		$this->assertEquals( 'CNE', $productCopy->getProductCode() );
		$this->assertEquals( $product->getId(), $productCopy->getProductId() );
		$this->assertEquals( MShop_Order_Item_Abstract::STAT_UNFINISHED, $productCopy->getStatus() );
		$this->assertEquals( '', $productCopy->getMediaUrl() );

		$this->assertTrue( $productCopy->isModified() );
	}
}
