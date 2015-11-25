<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Order\Item\Base\Product;


/**
 * Test class for \Aimeos\MShop\Order\Item\Base\Product\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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
		$this->price = \Aimeos\MShop\Price\Manager\Factory::createManager( \TestHelper::getContext() )->createItem();

		$attrValues = array(
			'order.base.product.attribute.id' => 4,
			'order.base.product.attribute.siteid' => 99,
			'order.base.product.attribute.parentid' => 11,
			'order.base.product.attribute.type' => 'default',
			'order.base.product.attribute.code' => 'size',
			'order.base.product.attribute.value' => '30',
			'order.base.product.attribute.name' => 'small',
			'order.base.product.attribute.mtime' => '2011-01-06 13:20:34',
			'order.base.product.attribute.ctime' => '2011-01-01 00:00:01',
			'order.base.product.attribute.editor' => 'unitTestUser'
		);
		$this->attribute = array( new \Aimeos\MShop\Order\Item\Base\Product\Attribute\Standard( $attrValues ) );

		$this->values = array(
			'order.base.product.id' => 1,
			'order.base.product.siteid' => 99,
			'order.base.product.ordprodid' => 10,
			'order.base.product.type' => 'bundle',
			'order.base.product.productid' => 100,
			'order.base.product.baseid' => 42,
			'order.base.product.suppliercode' => 'UnitSupplier',
			'order.base.product.prodcode' => 'UnitProd',
			'order.base.product.warehousecode' => 'unitwarehouse',
			'order.base.product.name' => 'UnitProduct',
			'order.base.product.mediaurl' => 'testurl',
			'order.base.product.quantity' => 11,
			'order.base.product.flags' => \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_NONE,
			'order.base.product.status' => \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS,
			'order.base.product.position' => 1,
			'order.base.product.mtime' => '2000-12-31 23:59:59',
			'order.base.product.ctime' => '2011-01-01 00:00:01',
			'order.base.product.editor' => 'unitTestUser',
		);

		$this->subProducts = array(
			new \Aimeos\MShop\Order\Item\Base\Product\Standard( clone $this->price ),
			new \Aimeos\MShop\Order\Item\Base\Product\Standard( clone $this->price )
		);
		$this->object = new \Aimeos\MShop\Order\Item\Base\Product\Standard( $this->price, $this->values, $this->attribute, $this->subProducts );
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
		$product = new \Aimeos\MShop\Order\Item\Base\Product\Standard( $this->price, $this->values, $this->attribute, $this->subProducts );
		$this->assertTrue( $this->object->compare( $product ) );
	}

	public function testCompareFail()
	{
		$price = clone $this->price;
		$price->setValue( '1.00' );

		$product = new \Aimeos\MShop\Order\Item\Base\Product\Standard( $price, $this->values, $this->attribute, $this->subProducts );
		$this->assertFalse( $this->object->compare( $product ) );
	}

	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$this->object->setId( 5 );
		$this->assertEquals( 5, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
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
		$this->assertEquals( 'UnitSupplier', $this->object->getSupplierCode() );
	}

	public function testSetSupplierCode()
	{
		$this->object->setSupplierCode( 'testId' );
		$this->assertEquals( 'testId', $this->object->getSupplierCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetProductId()
	{
		$this->assertEquals( 100, $this->object->getProductId() );
	}

	public function testSetProductId()
	{
		$this->object->setProductId( 'testProdId' );
		$this->assertEquals( 'testProdId', $this->object->getProductId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetProductCode()
	{
		$this->assertEquals( 'UnitProd', $this->object->getProductCode() );
	}

	public function testSetProductCode()
	{
		$this->object->setProductCode( 'testProdCode' );
		$this->assertEquals( 'testProdCode', $this->object->getProductCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetWarehouseCode()
	{
		$this->assertEquals( 'unitwarehouse', $this->object->getWarehouseCode() );
	}

	public function testSetWarehouseCode()
	{
		$this->object->setWarehouseCode( 'testWarehouseCode' );
		$this->assertEquals( 'testWarehouseCode', $this->object->getWarehouseCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetName()
	{
		$this->assertEquals( 'UnitProduct', $this->object->getName() );
	}

	public function testSetName()
	{
		$this->object->setName( 'Testname2' );
		$this->assertEquals( 'Testname2', $this->object->getName() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetMediaUrl()
	{
		$this->assertEquals( 'testurl', $this->object->getMediaUrl() );
	}

	public function testSetMediaUrl()
	{
		$this->object->setMediaUrl( 'testUrl' );
		$this->assertEquals( 'testUrl', $this->object->getMediaUrl() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetQuantity()
	{
		$this->assertEquals( 11, $this->object->getQuantity() );
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
		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->setQuantity( 'a' );
	}

	public function testSetQuantityNegative()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->setQuantity( -5 );
	}

	public function testSetQuantityZero()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->setQuantity( 0 );
	}

	public function testSetQuantityOverflow()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
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
		$this->assertEquals( $this->price->getValue() * 11, $this->object->getSumPrice()->getValue() );
		$this->assertEquals( $this->price->getCosts() * 11, $this->object->getSumPrice()->getCosts() );
		$this->assertEquals( $this->price->getRebate() * 11, $this->object->getSumPrice()->getRebate() );
		$this->assertEquals( $this->price->getTaxRate(), $this->object->getSumPrice()->getTaxRate() );
	}

	public function testGetFlags()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_NONE, $this->object->getFlags() );
	}

	public function testSetFlags()
	{
		$this->object->setFlags( \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE, $this->object->getFlags() );
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
		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->setPosition( 0 );
	}

	public function testGetStatus()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, $this->object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->object->setStatus( \Aimeos\MShop\Order\Item\Base::STAT_LOST );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_LOST, $this->object->getStatus() );
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
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelper::getContext() );
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
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelper::getContext() );
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
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelper::getContext() );
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
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelper::getContext() );
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


	public function testGetResourceType()
	{
		$this->assertEquals( 'order/base/product', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Base\Product\Standard( new \Aimeos\MShop\Price\Item\Standard() );

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
		$productCopy = new \Aimeos\MShop\Order\Item\Base\Product\Standard( $this->price );

		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelper::getContext() );
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
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_UNFINISHED, $productCopy->getStatus() );
		$this->assertEquals( '', $productCopy->getMediaUrl() );

		$this->assertTrue( $productCopy->isModified() );
	}
}
