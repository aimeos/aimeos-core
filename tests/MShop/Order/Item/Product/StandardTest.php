<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Order\Item\Product;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;
	private $price;
	private $attribute = [];
	private $subProducts;


	protected function setUp() : void
	{
		$this->price = \Aimeos\MShop::create( \TestHelper::context(), 'price' )->create();

		$attrValues = array(
			'order.product.attribute.id' => 4,
			'order.product.attribute.siteid' => '99',
			'order.product.attribute.parentid' => 11,
			'order.product.attribute.type' => 'default',
			'order.product.attribute.code' => 'size',
			'order.product.attribute.value' => '30',
			'order.product.attribute.name' => 'small',
			'order.product.attribute.mtime' => '2011-01-06 13:20:34',
			'order.product.attribute.ctime' => '2011-01-01 00:00:01',
			'order.product.attribute.editor' => 'unitTestUser'
		);
		$this->attribute = array( new \Aimeos\MShop\Order\Item\Product\Attribute\Standard( $attrValues ) );

		$this->values = array(
			'order.product.id' => 1,
			'order.product.parentid' => 42,
			'order.product.siteid' => '99',
			'order.product.orderproductid' => 10,
			'order.product.orderaddressid' => 11,
			'order.product.type' => 'bundle',
			'order.product.productid' => '100',
			'order.product.parentproductid' => '99',
			'order.product.vendor' => 'UnitSupplier',
			'order.product.prodcode' => 'UnitProd',
			'order.product.stocktype' => 'unittype',
			'order.product.timeframe' => '1-2w',
			'order.product.name' => 'UnitProduct',
			'order.product.description' => 'Unit product description',
			'order.product.mediaurl' => 'testurl',
			'order.product.target' => 'testtarget',
			'order.product.quantity' => 11,
			'order.product.qtyopen' => 5,
			'order.product.scale' => 0.1,
			'order.product.flags' => \Aimeos\MShop\Order\Item\Product\Base::FLAG_NONE,
			'order.product.statuspayment' => \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED,
			'order.product.statusdelivery' => \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS,
			'order.product.position' => 1,
			'order.product.notes' => 'test note',
			'order.product.mtime' => '2000-12-31 23:59:59',
			'order.product.ctime' => '2011-01-01 00:00:01',
			'order.product.editor' => 'unitTestUser',
			'.product' => \Aimeos\MShop::create( \TestHelper::context(), 'product' )->create(),
			'.supplier' => \Aimeos\MShop::create( \TestHelper::context(), 'supplier' )->create(),
		);

		$this->subProducts = array(
			new \Aimeos\MShop\Order\Item\Product\Standard( clone $this->price ),
			new \Aimeos\MShop\Order\Item\Product\Standard( clone $this->price )
		);

		$this->object = new \Aimeos\MShop\Order\Item\Product\Standard(
			$this->price, $this->values, $this->attribute, $this->subProducts
		);
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetProductItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->getProductItem() );
		$this->assertNull( ( new \Aimeos\MShop\Order\Item\Product\Standard( $this->price ) )->getProductItem() );
	}


	public function testGetSupplierItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Supplier\Item\Iface::class, $this->object->getSupplierItem() );
		$this->assertNull( ( new \Aimeos\MShop\Order\Item\Product\Standard( $this->price ) )->getSupplierItem() );
	}


	public function testCompare()
	{
		$product = new \Aimeos\MShop\Order\Item\Product\Standard( $this->price, $this->values, $this->attribute, $this->subProducts );
		$this->assertTrue( $this->object->compare( $product ) );
	}


	public function testCompareFail()
	{
		$this->values['order.product.stocktype'] = 'default';

		$product = new \Aimeos\MShop\Order\Item\Product\Standard( $this->price, $this->values, $this->attribute, $this->subProducts );
		$this->assertFalse( $this->object->compare( $product ) );
	}


	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 5 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 5, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testSetSiteId()
	{
		$this->object->setSiteId( 100 );
		$this->assertEquals( 100, $this->object->getSiteId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetOrderProductId()
	{
		$this->assertEquals( 10, $this->object->getOrderProductId() );
	}


	public function testSetOrderProductId()
	{
		$return = $this->object->setOrderProductId( 1001 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 1001, $this->object->getOrderProductId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setOrderProductId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( null, $this->object->getOrderProductId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetOrderAddressId()
	{
		$this->assertEquals( 11, $this->object->getOrderAddressId() );
	}


	public function testSetOrderAddressId()
	{
		$return = $this->object->setOrderAddressId( 1011 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 1011, $this->object->getOrderAddressId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setOrderAddressId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( null, $this->object->getOrderAddressId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'bundle', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'default' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'default', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetVendor()
	{
		$this->assertEquals( 'UnitSupplier', $this->object->getVendor() );
	}


	public function testSetVendor()
	{
		$return = $this->object->setVendor( 'test supplier' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'test supplier', $this->object->getVendor() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetProductId()
	{
		$this->assertEquals( 100, $this->object->getProductId() );
	}


	public function testSetProductId()
	{
		$return = $this->object->setProductId( 'testProdId' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'testProdId', $this->object->getProductId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetParentProductId()
	{
		$this->assertEquals( 99, $this->object->getParentProductId() );
	}


	public function testSetParentProductId()
	{
		$return = $this->object->setParentProductId( 'testParentProdId' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'testParentProdId', $this->object->getParentProductId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetProductCode()
	{
		$this->assertEquals( 'UnitProd', $this->object->getProductCode() );
	}


	public function testSetProductCode()
	{
		$return = $this->object->setProductCode( 'testProdCode' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'testProdCode', $this->object->getProductCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStockType()
	{
		$this->assertEquals( 'unittype', $this->object->getStockType() );
	}


	public function testSetStockType()
	{
		$return = $this->object->setStockType( 'testStockType' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'testStockType', $this->object->getStockType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetName()
	{
		$this->assertEquals( 'UnitProduct', $this->object->getName() );
	}


	public function testGetNameUrl()
	{
		$this->assertEquals( 'unitproduct', $this->object->getName( 'url' ) );
	}


	public function testSetName()
	{
		$return = $this->object->setName( 'Testname2' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'Testname2', $this->object->getName() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDescription()
	{
		$this->assertEquals( 'Unit product description', $this->object->getDescription() );
	}


	public function testSetDescription()
	{
		$return = $this->object->setDescription( 'Test description' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'Test description', $this->object->getDescription() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetMediaUrl()
	{
		$this->assertEquals( 'testurl', $this->object->getMediaUrl() );
	}


	public function testSetMediaUrl()
	{
		$return = $this->object->setMediaUrl( 'testUrl' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'testUrl', $this->object->getMediaUrl() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTarget()
	{
		$this->assertEquals( 'testtarget', $this->object->getTarget() );
	}


	public function testSetTarget()
	{
		$return = $this->object->setTarget( 'ttarget' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'ttarget', $this->object->getTarget() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeFrame()
	{
		$this->assertEquals( '1-2w', $this->object->getTimeFrame() );
	}


	public function testSetTimeFrame()
	{
		$return = $this->object->setTimeFrame( '3-4d' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( '3-4d', $this->object->getTimeFrame() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetScale()
	{
		$this->assertEquals( 0.1, $this->object->getScale() );
	}


	public function testSetScale()
	{
		$return = $this->object->setScale( 2.5 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 2.5, $this->object->getScale() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetQuantity()
	{
		$this->assertEquals( 11, $this->object->getQuantity() );
	}


	public function testSetQuantity()
	{
		$return = $this->object->setQuantity( 20 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 20, $this->object->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetQuantityDecimal()
	{
		$return = $this->object->setQuantity( 1.5 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 1.5, $this->object->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetQuantityNegative()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->setQuantity( -5 );
	}


	public function testSetQuantityZero()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->setQuantity( 0 );
	}


	public function testSetQuantityOverflow()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->setQuantity( 2147483648 );
	}


	public function testGetQuantityOpen()
	{
		$this->assertEquals( 5, $this->object->getQuantityOpen() );
	}


	public function testSetQuantityOpen()
	{
		$return = $this->object->setQuantityOpen( 3 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 3, $this->object->getQuantityOpen() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetQuantityOpenDecimal()
	{
		$return = $this->object->setQuantityOpen( 1.5 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 1.5, $this->object->getQuantityOpen() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetQuantityOpenNegative()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->setQuantityOpen( -5 );
	}


	public function testSetQuantityOpenZero()
	{
		$return = $this->object->setQuantityOpen( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getQuantityOpen() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetQuantityOpenOverflow()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->setQuantityOpen( 12 );
	}


	public function testGetNotes()
	{
		$this->assertEquals( 'test note', $this->object->getNotes() );
	}


	public function testSetNotes()
	{
		$return = $this->object->setNotes( 'some notes' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'some notes', $this->object->getNotes() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPrice()
	{
		$this->assertSame( $this->price, $this->object->getPrice() );
	}


	public function testSetPrice()
	{
		$this->price->setValue( '5.00' );
		$return = $this->object->setPrice( $this->price );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertSame( $this->price, $this->object->getPrice() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetFlags()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Product\Base::FLAG_NONE, $this->object->getFlags() );
	}


	public function testSetFlags()
	{
		$return = $this->object->setFlags( \Aimeos\MShop\Order\Item\Product\Base::FLAG_IMMUTABLE );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Product\Base::FLAG_IMMUTABLE, $this->object->getFlags() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->object->getPosition() );
	}


	public function testSetPosition()
	{
		$return = $this->object->setPosition( 2 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 2, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPositionReset()
	{
		$return = $this->object->setPosition( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( null, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPositionInvalid()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->setPosition( -1 );
	}


	public function testGetStatusDelivery()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, $this->object->getStatusDelivery() );
	}


	public function testSetStatusDelivery()
	{
		$return = $this->object->setStatusDelivery( \Aimeos\MShop\Order\Item\Base::STAT_LOST );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_LOST, $this->object->getStatusDelivery() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatusPayment()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $this->object->getStatusPayment() );
	}


	public function testSetStatusPayment()
	{
		$return = $this->object->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_PENDING );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_PENDING, $this->object->getStatusPayment() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetParentId()
	{
		$this->assertEquals( 42, $this->object->getParentId() );
	}


	public function testSetParentId()
	{
		$return = $this->object->setParentId( 111 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 111, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetParentIdReset()
	{
		$return = $this->object->setParentId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( null, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$regexp = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/';
		$this->assertMatchesRegularExpression( $regexp, $this->object->getTimeModified() );
		$this->assertEquals( '2000-12-31 23:59:59', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->editor() );
	}


	public function testAddAttributeItems()
	{
		$item = \Aimeos\MShop::create( \TestHelper::context(), 'order/product' )->createAttributeItem();

		$return = $this->object->addAttributeItems( [$item] );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 2, count( $this->object->getAttributeItems() ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetAttribute()
	{
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/product/attribute' );

		$attrItem001 = $attManager->create();
		$attrItem001->setAttributeId( '1' );
		$attrItem001->setCode( 'code_001' );
		$attrItem001->setValue( 'value_001' );

		$attrItem002 = $attManager->create();
		$attrItem002->setAttributeId( '2' );
		$attrItem002->setCode( 'code_002' );
		$attrItem002->setType( 'test_002' );
		$attrItem002->setValue( 'value_002' );

		$this->object->setAttributeItems( array( $attrItem001, $attrItem002 ) );

		$result = $this->object->getAttribute( 'code_001' );
		$this->assertEquals( 'value_001', $result );

		$result = $this->object->getAttribute( 'code_002', ['test_002'] );
		$this->assertEquals( 'value_002', $result );

		$result = $this->object->getAttribute( 'code_002', 'test_002' );
		$this->assertEquals( 'value_002', $result );

		$result = $this->object->getAttribute( 'code_002' );
		$this->assertEquals( null, $result );

		$result = $this->object->getAttribute( 'code_003' );
		$this->assertEquals( null, $result );

		$this->object->setAttributeItems( [] );

		$result = $this->object->getAttribute( 'code_001' );
		$this->assertEquals( null, $result );
	}


	public function testGetAttributeList()
	{
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/product/attribute' );

		$attrItem001 = $attManager->create();
		$attrItem001->setAttributeId( '1' );
		$attrItem001->setCode( 'code_001' );
		$attrItem001->setType( 'test_001' );
		$attrItem001->setValue( 'value_001' );

		$attrItem002 = $attManager->create();
		$attrItem002->setAttributeId( '2' );
		$attrItem002->setCode( 'code_001' );
		$attrItem002->setType( 'test_001' );
		$attrItem002->setValue( 'value_002' );

		$this->object->setAttributeItems( array( $attrItem001, $attrItem002 ) );

		$result = $this->object->getAttribute( 'code_001', 'test_001' );
		$this->assertEquals( ['value_001', 'value_002'], $result );
	}


	public function testGetAttributeItem()
	{
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/product/attribute' );

		$attrItem001 = $attManager->create();
		$attrItem001->setAttributeId( '1' );
		$attrItem001->setCode( 'code_001' );
		$attrItem001->setValue( 'value_001' );

		$attrItem002 = $attManager->create();
		$attrItem002->setAttributeId( '2' );
		$attrItem002->setCode( 'code_002' );
		$attrItem002->setType( 'test_002' );
		$attrItem002->setValue( 'value_002' );

		$this->object->setAttributeItems( array( $attrItem001, $attrItem002 ) );

		$result = $this->object->getAttributeItem( 'code_001' );
		$this->assertEquals( 'value_001', $result->getValue() );

		$result = $this->object->getAttributeItem( 'code_002', 'test_002' );
		$this->assertEquals( 'value_002', $result->getValue() );

		$result = $this->object->getAttributeItem( 'code_002' );
		$this->assertEquals( null, $result );

		$result = $this->object->getAttribute( 'code_003' );
		$this->assertEquals( null, $result );

		$this->object->setAttributeItems( [] );

		$result = $this->object->getAttribute( 'code_001' );
		$this->assertEquals( null, $result );
	}


	public function testGetAttributeItemList()
	{
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/product/attribute' );

		$attrItem001 = $attManager->create();
		$attrItem001->setAttributeId( '1' );
		$attrItem001->setCode( 'code_001' );
		$attrItem001->setType( 'test_001' );
		$attrItem001->setValue( 'value_001' );

		$attrItem002 = $attManager->create();
		$attrItem002->setAttributeId( '2' );
		$attrItem002->setCode( 'code_001' );
		$attrItem002->setType( 'test_001' );
		$attrItem002->setValue( 'value_002' );

		$this->object->setAttributeItems( array( $attrItem001, $attrItem002 ) );

		$result = $this->object->getAttributeItem( 'code_001', 'test_001' );
		$this->assertEquals( 2, count( $result ) );
	}


	public function testGetAttributeItems()
	{
		$this->assertEquals( $this->attribute, $this->object->getAttributeItems()->toArray() );
	}


	public function testGetAttributeItemsByType()
	{
		$this->assertEquals( $this->attribute, $this->object->getAttributeItems( 'default' )->toArray() );
	}


	public function testGetAttributeItemsInvalidType()
	{
		$this->assertEquals( [], $this->object->getAttributeItems( 'invalid' )->toArray() );
	}


	public function testSetAttributeItem()
	{
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/product/attribute' );

		$item = $attManager->create();
		$item->setAttributeId( '1' );
		$item->setCode( 'test_code' );
		$item->setType( 'test_type' );
		$item->setValue( 'test_value' );

		$return = $this->object->setAttributeItem( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'test_value', $this->object->getAttributeItem( 'test_code', 'test_type' )->getValue() );
		$this->assertTrue( $this->object->isModified() );


		$item = $attManager->create();
		$item->setAttributeId( '1' );
		$item->setCode( 'test_code' );
		$item->setType( 'test_type' );
		$item->setValue( 'test_value2' );

		$return = $this->object->setAttributeItem( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'test_value2', $this->object->getAttributeItem( 'test_code', 'test_type' )->getValue() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetAttributeItems()
	{
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/product/attribute' );

		$list = array(
			$attManager->create(),
			$attManager->create(),
		);

		$return = $this->object->setAttributeItems( $list );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( $list, $this->object->getAttributeItems()->toArray() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetProducts()
	{
		$this->assertEquals( $this->subProducts, $this->object->getProducts()->toArray() );
	}


	public function testSetProducts()
	{
		$return = $this->object->setProducts( [] );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( [], $this->object->getProducts()->toArray() );

		$return = $this->object->setProducts( $this->subProducts );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( $this->subProducts, $this->object->getProducts()->toArray() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'order/product', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Product\Standard( new \Aimeos\MShop\Price\Item\Standard() );

		$list = $entries = array(
			'order.product.id' => 1,
			'order.product.parentid' => 2,
			'order.product.siteid' => 123,
			'order.product.orderproductid' => 10,
			'order.product.orderaddressid' => 11,
			'order.product.parentproductid' => 3,
			'order.product.productid' => 4,
			'order.product.prodcode' => 'test',
			'order.product.currencyid' => 'EUR',
			'order.product.price' => '10.00',
			'order.product.costs' => '5.00',
			'order.product.rebate' => '2.00',
			'order.product.taxrates' => ['tax' => '20.00'],
			'order.product.taxvalue' => '2.5000',
			'order.product.taxflag' => 1,
			'order.product.name' => 'test item',
			'order.product.description' => 'test description',
			'order.product.stocktype' => 'stocktype',
			'order.product.vendor' => 'testsup',
			'order.product.mediaurl' => '/path/to/image.jpg',
			'order.product.target' => 'ttarget',
			'order.product.timeframe' => '1-2d',
			'order.product.position' => 4,
			'order.product.quantity' => 5,
			'order.product.qtyopen' => 3,
			'order.product.scale' => 0.5,
			'order.product.statuspayment' => 6,
			'order.product.statusdelivery' => 0,
			'order.product.flags' => 1,
			'order.product.notes' => 'note',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['order.product.id'], $item->getId() );
		$this->assertEquals( $list['order.product.parentid'], $item->getParentId() );
		$this->assertEquals( $list['order.product.siteid'], $item->getSiteId() );
		$this->assertEquals( $list['order.product.orderproductid'], $item->getOrderProductId() );
		$this->assertEquals( $list['order.product.orderaddressid'], $item->getOrderAddressId() );
		$this->assertEquals( $list['order.product.parentproductid'], $item->getParentProductId() );
		$this->assertEquals( $list['order.product.productid'], $item->getProductId() );
		$this->assertEquals( $list['order.product.prodcode'], $item->getProductCode() );
		$this->assertEquals( $list['order.product.name'], $item->getName() );
		$this->assertEquals( $list['order.product.description'], $item->getDescription() );
		$this->assertEquals( $list['order.product.stocktype'], $item->getStockType() );
		$this->assertEquals( $list['order.product.vendor'], $item->getVendor() );
		$this->assertEquals( $list['order.product.prodcode'], $item->getProductCode() );
		$this->assertEquals( $list['order.product.mediaurl'], $item->getMediaUrl() );
		$this->assertEquals( $list['order.product.timeframe'], $item->getTimeFrame() );
		$this->assertEquals( $list['order.product.target'], $item->getTarget() );
		$this->assertEquals( $list['order.product.position'], $item->getPosition() );
		$this->assertEquals( $list['order.product.scale'], $item->getScale() );
		$this->assertEquals( $list['order.product.quantity'], $item->getQuantity() );
		$this->assertEquals( $list['order.product.qtyopen'], $item->getQuantityOpen() );
		$this->assertEquals( $list['order.product.statuspayment'], $item->getStatusPayment() );
		$this->assertEquals( $list['order.product.statusdelivery'], $item->getStatusDelivery() );
		$this->assertEquals( $list['order.product.flags'], $item->getFlags() );
		$this->assertEquals( $list['order.product.notes'], $item->getNotes() );
		$this->assertEquals( $list['order.product.currencyid'], $item->getPrice()->getCurrencyId() );
		$this->assertEquals( $list['order.product.price'], $item->getPrice()->getValue() );
		$this->assertEquals( $list['order.product.costs'], $item->getPrice()->getCosts() );
		$this->assertEquals( $list['order.product.rebate'], $item->getPrice()->getRebate() );
		$this->assertEquals( $list['order.product.taxrates'], $item->getPrice()->getTaxRates() );
		$this->assertEquals( $list['order.product.taxvalue'], $item->getPrice()->getTaxValue() );
		$this->assertEquals( $list['order.product.taxflag'], $item->getPrice()->getTaxFlag() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( $this->object->getId(), $arrayObject['order.product.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['order.product.siteid'] );
		$this->assertEquals( $this->object->getParentId(), $arrayObject['order.product.parentid'] );
		$this->assertEquals( $this->object->getOrderProductId(), $arrayObject['order.product.orderproductid'] );
		$this->assertEquals( $this->object->getOrderAddressId(), $arrayObject['order.product.orderaddressid'] );
		$this->assertEquals( $this->object->getStockType(), $arrayObject['order.product.stocktype'] );
		$this->assertEquals( $this->object->getVendor(), $arrayObject['order.product.vendor'] );
		$this->assertEquals( $this->object->getParentProductId(), $arrayObject['order.product.parentproductid'] );
		$this->assertEquals( $this->object->getProductId(), $arrayObject['order.product.productid'] );
		$this->assertEquals( $this->object->getProductCode(), $arrayObject['order.product.prodcode'] );
		$this->assertEquals( $this->object->getName(), $arrayObject['order.product.name'] );
		$this->assertEquals( $this->object->getDescription(), $arrayObject['order.product.description'] );
		$this->assertEquals( $this->object->getMediaUrl(), $arrayObject['order.product.mediaurl'] );
		$this->assertEquals( $this->object->getTimeFrame(), $arrayObject['order.product.timeframe'] );
		$this->assertEquals( $this->object->getTarget(), $arrayObject['order.product.target'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['order.product.position'] );
		$this->assertEquals( $this->object->getPrice()->getCurrencyId(), $arrayObject['order.product.currencyid'] );
		$this->assertEquals( $this->object->getPrice()->getValue(), $arrayObject['order.product.price'] );
		$this->assertEquals( $this->object->getPrice()->getCosts(), $arrayObject['order.product.costs'] );
		$this->assertEquals( $this->object->getPrice()->getRebate(), $arrayObject['order.product.rebate'] );
		$this->assertEquals( $this->object->getPrice()->getTaxRate(), $arrayObject['order.product.taxrate'] );
		$this->assertEquals( $this->object->getPrice()->getTaxRates(), $arrayObject['order.product.taxrates'] );
		$this->assertEquals( $this->object->getPrice()->getTaxValue(), $arrayObject['order.product.taxvalue'] );
		$this->assertEquals( $this->object->getPrice()->getTaxFlag(), $arrayObject['order.product.taxflag'] );
		$this->assertEquals( $this->object->getQuantityOpen(), $arrayObject['order.product.qtyopen'] );
		$this->assertEquals( $this->object->getQuantity(), $arrayObject['order.product.quantity'] );
		$this->assertEquals( $this->object->getScale(), $arrayObject['order.product.scale'] );
		$this->assertEquals( $this->object->getStatusPayment(), $arrayObject['order.product.statuspayment'] );
		$this->assertEquals( $this->object->getStatusDelivery(), $arrayObject['order.product.statusdelivery'] );
		$this->assertEquals( $this->object->getFlags(), $arrayObject['order.product.flags'] );
		$this->assertEquals( $this->object->getNotes(), $arrayObject['order.product.notes'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['order.product.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['order.product.mtime'] );
		$this->assertEquals( $this->object->editor(), $arrayObject['order.product.editor'] );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testCopyFrom()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'product' );
		$product = $manager->find( 'CNE', ['text'] );

		$productCopy = new \Aimeos\MShop\Order\Item\Product\Standard( $this->price );
		$return = $productCopy->copyFrom( $product->set( 'customprop', 'abc' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $return );
		$this->assertEquals( 'default', $productCopy->getType() );
		$this->assertEquals( 'CNE', $productCopy->getProductCode() );
		$this->assertEquals( 'Cafe Noire Expresso', $productCopy->getName() );
		$this->assertEquals( 'Cafe Noire Expresso for basket', $productCopy->getDescription() );
		$this->assertEquals( $product->getId(), $productCopy->getProductId() );
		$this->assertEquals( -1, $productCopy->getStatusDelivery() );
		$this->assertEquals( 0.1, $productCopy->getScale() );
		$this->assertEquals( '', $productCopy->getVendor() );
		$this->assertEquals( '', $productCopy->getTarget() );
		$this->assertEquals( '', $productCopy->getMediaUrl() );
		$this->assertEquals( 'abc', $productCopy->get( 'customprop' ) );

		$this->assertTrue( $productCopy->isModified() );
	}
}
