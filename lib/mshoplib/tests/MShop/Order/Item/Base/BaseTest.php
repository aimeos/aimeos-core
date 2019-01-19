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

		$priceManager = \Aimeos\MShop\Price\Manager\Factory::create( $context );
		$locale = \Aimeos\MShop\Locale\Manager\Factory::create( $context )->createItem();

		$this->object = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->createItem(), $locale, [] );


		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $context );

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


		$this->products = [$prod1, $prod2];
		$this->coupons = ['OPQR' => [$prod1]];

		$this->addresses = array(
			'payment' => $orderAddressManager->createItem()->setType( 'payment' )->setId( null ),
			'delivery' => $orderAddressManager->createItem()->setType( 'delivery' )->setId( null ),
		);

		$this->services = array(
			'payment' => [
				1 => $orderServiceManager->createItem()->setCode( 'testpay' )->setServiceId( 1 )
			],
			'delivery' => [
				2 => $orderServiceManager->createItem()->setCode( 'testship' )->setServiceId( 2 )
			],
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


	public function testAddProductAppend()
	{
		$this->object->setProducts( $this->products );

		$products = $this->object->getProducts();
		$product = $this->createProduct( 'prodid3' );
		$products[] = $product;

		$result = $this->object->addProduct( $product );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertSame( $products, $this->object->getProducts() );
		$this->assertSame( $product, $this->object->getProduct( 2 ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddProductInsert()
	{
		$this->object->setProducts( $this->products );

		$products = $this->object->getProducts();
		$products[1] = $this->createProduct( 'prodid3' );

		$result = $this->object->addProduct( $products[1], 1 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertSame( $products[1], $this->object->getProduct( 1 ) );
		$this->assertEquals( $products, $this->object->getProducts() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddProductInsertEnd()
	{
		$this->object->setProducts( $this->products );

		$products = $this->object->getProducts();
		$product = $this->createProduct( 'prodid3' );
		$products[] = $product;

		$result = $this->object->addProduct( $product, 2 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $products, $this->object->getProducts() );
		$this->assertSame( $product, $this->object->getProduct( 2 ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddProductSame()
	{
		$product = $this->createProduct( 'prodid3' )->setQuantity( 5 );

		$this->object->addProduct( $product );
		$this->object->addProduct( $product );

		$this->assertEquals( 10, $this->object->getProduct( 0 )->getQuantity() );
		$this->assertEquals( [0 => $product], $this->object->getProducts() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddProductStablePosition()
	{
		$this->object->setProducts( $this->products );

		$product = $this->createProduct( 'prodid3' )->setQuantity( 5 );
		$this->object->addProduct( $product );

		$testProduct = $this->object->getProduct( 1 );
		$this->object->deleteProduct( 0 );
		$this->object->deleteProduct( 1 );
		$result = $this->object->addProduct( $testProduct, 1 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( [1 => $testProduct, 2 => $product], $this->object->getProducts() );
	}


	public function testDeleteProduct()
	{
		$this->object->addProduct( $this->products[0] );
		$result = $this->object->deleteProduct( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertSame( [], $this->object->getProducts() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetProducts()
	{
		$this->object->setProducts( $this->products );

		$this->assertSame( $this->products, $this->object->getProducts() );
		$this->assertSame( $this->products[1], $this->object->getProduct( 1 ) );
	}


	public function testSetProducts()
	{
		$result = $this->object->setProducts( $this->products );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertSame( $this->products, $this->object->getProducts() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetAddress()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		$this->object->setAddress( $this->addresses[$type], $type );

		$address = $this->object->getAddress( $type );
		$this->assertEquals( $this->addresses[$type], $address );
	}


	public function testDeleteAddress()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		$this->object->setAddress( $this->addresses[$type], $type );
		$result = $this->object->deleteAddress( $type );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertTrue( $this->object->isModified() );

		$this->setExpectedException( \Aimeos\MShop\Order\Exception::class );
		$this->object->getAddress( $type );
	}


	public function testSetAddress()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		$result = $this->object->setAddress( $this->addresses[$type], $type );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $this->addresses[$type], $this->object->getAddress( $type ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetAddresses()
	{
		$result = $this->object->setAddresses( $this->addresses );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $this->addresses, $this->object->getAddresses() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddService()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$result = $this->object->addService( $this->services['payment'][1], $type );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( 1, count( $this->object->getService( $type ) ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testDeleteService()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$this->object->setServices( $this->services );

		$result = $this->object->deleteService( $type );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( [], $this->object->getService( $type ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetService()
	{
		$this->object->setServices( $this->services );

		$payments = $this->object->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );
		$deliveries = $this->object->getService( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY );

		$this->assertEquals( 2, count( $this->object->getServices() ) );
		$this->assertEquals( 1, count( $payments ) );
		$this->assertEquals( 1, count( $deliveries ) );

		$this->assertEquals( $this->services['payment'], $payments );
		$this->assertEquals( $this->services['delivery'], $deliveries );
	}


	public function testGetServiceSingle()
	{
		$this->object->setServices( $this->services );

		$service = $this->object->getService( 'payment', 'testpay' );
		$this->assertEquals( 'testpay', $service->getCode() );

		$this->setExpectedException( \Aimeos\MShop\Order\Exception::class );
		$this->object->getService( 'payment', 'invalid' );
	}


	public function testSetServices()
	{
		$result = $this->object->setServices( $this->services );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $this->services, $this->object->getServices() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddCoupon()
	{
		$result = $this->object->addCoupon( 'OPQR' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( ['OPQR' => []], $this->object->getCoupons() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testDeleteCoupon()
	{
		$this->object->setCoupons( $this->coupons );
		$result = $this->object->deleteCoupon( 'OPQR' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( [], $this->object->getCoupons() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCoupons()
	{
		$this->object->setCoupons( $this->coupons );
		$this->assertEquals( $this->coupons, $this->object->getCoupons() );
	}


	public function testSetCoupon()
	{
		$result = $this->object->setCoupon( 'OPQR', $this->coupons['OPQR'] );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( ['OPQR' => $this->coupons['OPQR']], $this->object->getCoupons() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetCoupons()
	{
		$result = $this->object->setCoupons( $this->coupons );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $this->coupons, $this->object->getCoupons() );
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

		foreach( $this->services as $type => $services )
		{
			foreach( $services as $service ) {
				$this->object->addService( $service, $type );
			}
		}

		$this->object->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
	}


	public function testCheckInvalid()
	{
		$this->setExpectedException( \Aimeos\MShop\Order\Exception::class );
		$this->object->check( -1 );
	}


	public function testCheckAllFailure()
	{
		$this->setExpectedException( \Aimeos\MShop\Order\Exception::class );
		$this->object->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
	}


	public function testCheckProductsFailure()
	{
		$this->setExpectedException( \Aimeos\MShop\Order\Exception::class );
		$this->object->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
	}


	/**
	 * @param string $code
	 */
	protected function createProduct( $code )
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() );
		$orderProductManager = $orderManager->getSubManager( 'base' )->getSubManager( 'product' );
		$product = $orderProductManager->createItem();

		$price = \Aimeos\MShop\Price\Manager\Factory::create( \TestHelperMShop::getContext() )->createItem();
		$price->setValue( '2.99' );

		$product->setPrice( $price );
		$product->setProductCode( $code );

		return $product;
	}
}