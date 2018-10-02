<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Order\Item\Base;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $products;
	private $addresses;
	private $services;
	private $coupons;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $context );
		$locale = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context )->createItem();

		$this->object = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->createItem(), $locale, [] );


		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $context );

		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderAddressManager = $orderBaseManager->getSubManager( 'address' );
		$orderProductManager = $orderBaseManager->getSubManager( 'product' );
		$orderServiceManager = $orderBaseManager->getSubManager( 'service' );


		$price = $priceManager->createItem();
		$price->setRebate( '3.01' );
		$price->setValue( '43.12' );
		$price->setCosts( '1.11' );
		$price->setTaxRate( '0.00' );
		$price->setCurrencyId( 'EUR' );

		$prod1 = $orderProductManager->createItem();
		$prod1->setProductCode( 'prod1' );
		$prod1->setPrice( $price );

		$price = $priceManager->createItem();
		$price->setRebate( '4.00' );
		$price->setValue( '20.00' );
		$price->setCosts( '2.00' );
		$price->setTaxRate( '0.50' );
		$price->setCurrencyId( 'EUR' );

		$prod2 = $orderProductManager->createItem();
		$prod2->setProductCode( 'prod2' );
		$prod2->setPrice( $price );


		$this->products = array( $prod1, $prod2 );
		$this->coupons = array( 'OPQR' => array( $prod1 ) );

		$this->addresses = array(
			\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT => $orderAddressManager->createItem(),
			\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY => $orderAddressManager->createItem(),
		);

		$this->services = array(
			'payment' => $orderServiceManager->createItem(),
			'delivery' => $orderServiceManager->createItem(),
		);
	}


	protected function tearDown()
	{
		unset( $this->object, $this->products, $this->addresses, $this->services, $this->coupons );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetProducts()
	{
		foreach( $this->products as $product ) {
			$this->object->addProduct( $product );
		}

		$this->assertSame( $this->products, $this->object->getProducts() );
		$this->assertSame( $this->products[1], $this->object->getProduct( 1 ) );
	}


	public function testAddProductAppend()
	{
		foreach( $this->products as $product ) {
			$this->object->addProduct( $product );
		}

		$products = $this->object->getProducts();
		$product = $this->createProduct( 'prodid3' );
		$products[] = $product;

		$this->object->addProduct( $product );

		$this->assertSame( $products, $this->object->getProducts() );
		$this->assertSame( $product, $this->object->getProduct( 2 ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddProductInsert()
	{
		foreach( $this->products as $product ) {
			$this->object->addProduct( $product );
		}

		$products = $this->object->getProducts();
		$product = $this->createProduct( 'prodid3' );
		array_splice( $products, 1, 0, array( $product ) );

		$this->object->addProduct( $product, 1 );

		$this->assertEquals( $products, $this->object->getProducts() );
		$this->assertSame( $product, $this->object->getProduct( 1 ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddProductInsertEnd()
	{
		foreach( $this->products as $product ) {
			$this->object->addProduct( $product );
		}

		$products = $this->object->getProducts();
		$product = $this->createProduct( 'prodid3' );
		array_splice( $products, 2, 0, array( $product ) );

		$this->object->addProduct( $product, 2 );

		$this->assertEquals( $products, $this->object->getProducts() );
		$this->assertSame( $product, $this->object->getProduct( 2 ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddProductSame()
	{
		foreach( $this->products as $product ) {
			$this->object->addProduct( $product );
		}

		$products = $this->object->getProducts();
		$product = $this->createProduct( 'prodid3' );
		$product->setQuantity( 5 );
		$products[] = $product;

		$this->object->addProduct( $product );
		$this->object->addProduct( $product );

		$this->assertEquals( $products, $this->object->getProducts() );
		$this->assertEquals( 10, $this->object->getProduct( 2 )->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddProductStablePosition()
	{
		foreach( $this->products as $product ) {
			$this->object->addProduct( $product );
		}

		$product = $this->createProduct( 'prodid3' );
		$product->setQuantity( 5 );
		$this->object->addProduct( $product );

		$this->object->deleteProduct( 0 );
		$testProduct = $this->object->getProduct( 1 );

		$this->object->deleteProduct( 1 );
		$this->object->addProduct( $testProduct, 1 );

		$expected = array( 1 => $testProduct, 2 => $product );
		$this->assertEquals( $expected, $this->object->getProducts() );
	}


	public function testEditProduct()
	{
		$product = $this->createProduct( 'prodid3' );
		$product->setQuantity( 5 );

		$this->object->addProduct( $product );
		$product->setQuantity( 10 );
		$this->object->editProduct( $product, 0 );

		$this->assertEquals( [$product], $this->object->getProducts() );
		$this->assertEquals( 10, $this->object->getProduct( 0 )->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testEditProductSame()
	{
		foreach( $this->products as $product ) {
			$this->object->addProduct( $product );
		}

		$product = clone $this->object->getProduct( 0 );
		$product->setQuantity( 10 );
		$this->object->editProduct( $product, 1 );

		$this->assertEquals( 2, count( $this->object->getProducts() ) );
		$this->assertEquals( 10, $this->object->getProduct( 0 )->getQuantity() );
		$this->assertEquals( 1, $this->object->getProduct( 1 )->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testDeleteProduct()
	{
		foreach( $this->products as $product ) {
			$this->object->addProduct( $product );
		}

		unset( $this->products[1] );
		$this->object->deleteProduct( 1 );
		$this->assertSame( $this->products, $this->object->getProducts() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetAddress()
	{
		foreach( $this->addresses as $type => $address )
		{
			$address->setId( null );
			$address->setType( $type );
			$this->object->setAddress( $address, $type );
		}

		$this->assertEquals( $this->addresses, $this->object->getAddresses() );

		$address = $this->object->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->assertEquals( $this->addresses[\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT], $address );
	}


	public function testSetAddress()
	{
		foreach( $this->addresses as $type => $address ) {
			$this->object->setAddress( $address, $type );
		}

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$orderAddressManager = $orderManager->getSubManager( 'base' )->getSubManager( 'address' );
		$address = $orderAddressManager->createItem();

		$this->object->setAddress( $address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$item = $this->object->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, $item->getType() );
		$this->assertTrue( $item->isModified() );
		$this->assertNull( $item->getId() );
	}


	public function testDeleteAddress()
	{
		foreach( $this->addresses as $type => $address ) {
			$this->object->setAddress( $address, $type );
		}

		$this->object->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->object->deleteAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->assertTrue( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
	}


	public function testGetService()
	{
		foreach( $this->services as $type => $service )
		{
			$service->setId( null );
			$service->setType( $type );
			$this->object->addService( $service, $type );
		}

		$payments = $this->object->getService( 'payment' );
		$deliveries = $this->object->getService( 'delivery' );

		$this->assertEquals( 2, count( $this->object->getServices() ) );
		$this->assertEquals( 1, count( $payments ) );
		$this->assertEquals( 1, count( $deliveries ) );

		$this->assertEquals( $this->services['payment'], reset( $payments ) );
		$this->assertEquals( $this->services['delivery'], reset( $deliveries ) );
	}


	public function testGetServiceSingle()
	{
		$service = $this->services['payment']->setCode( 'test' );
		$this->object->addService( $service, 'payment' );

		$result = $this->object->getService( 'payment', 'test' );

		$this->assertEquals( $this->services['payment']->getCode(), $result->getCode() );

		$this->setExpectedException( '\Aimeos\MShop\Order\Exception' );
		$this->object->getService( 'payment', 'invalid' );
	}


	public function testAddService()
	{
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'order/base/service' );
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
		$service = $manager->createItem()->setServiceId( -1 );

		$this->object->addService( $service, $type );
		$this->object->addService( $service, $type );
		$list = $this->object->getService( $type );
		$item = reset( $list );

		$this->assertEquals( 1, count( $list ) );
		$this->assertEquals( $type, $item->getType() );
		$this->assertTrue( $item->isModified() );
		$this->assertNull( $item->getId() );
	}


	public function testDeleteService()
	{
		foreach( $this->services as $type => $service ) {
			$this->object->addService( $service, $type );
		}

		$this->object->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );
		$this->object->deleteService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testCoupons()
	{
		foreach( $this->coupons as $code => $products ) {
			$this->object->addCoupon( $code, $products );
		}

		foreach( $this->object->getCoupons() as $coupon => $products ) {
			$this->assertEquals( $this->coupons[$coupon], $products );
		}
	}


	public function testDeleteCoupon()
	{
		foreach( $this->coupons as $code => $products ) {
			$this->object->addCoupon( $code, $products );
		}

		$this->object->deleteCoupon( 'OPQR' );

		foreach( $this->object->getCoupons() as $coupon => $products ) {
			$this->assertEquals( [], $products );
		}

		$this->object->deleteCoupon( 'OPQR', true );
		$this->assertEquals( [], $this->object->getCoupons() );

		$this->assertTrue( $this->object->isModified() );
	}


	public function testCheck()
	{
		foreach( $this->products as $product ) {
			$this->object->addProduct( $product );
		}

		foreach( $this->addresses as $type => $address ) {
			$this->object->setAddress( $address, $type );
		}

		foreach( $this->services as $type => $service ) {
			$this->object->addService( $service, $type );
		}

		$this->object->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
	}


	public function testCheckInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->check( -1 );
	}


	public function testCheckAllFailure()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
	}


	public function testCheckProductsFailure()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
	}


	/**
	 * @param string $code
	 */
	protected function createProduct( $code )
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$orderProductManager = $orderManager->getSubManager( 'base' )->getSubManager( 'product' );
		$product = $orderProductManager->createItem();

		$price = \Aimeos\MShop\Price\Manager\Factory::createManager( \TestHelperMShop::getContext() )->createItem();
		$price->setValue( '2.99' );

		$product->setPrice( $price );
		$product->setProductCode( $code );

		return $product;
	}
}