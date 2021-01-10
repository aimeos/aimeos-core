<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Item\Base;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $products;
	private $addresses;
	private $services;
	private $coupons;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();

		$priceManager = \Aimeos\MShop\Price\Manager\Factory::create( $context );
		$locale = \Aimeos\MShop\Locale\Manager\Factory::create( $context )->create();

		$this->object = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->create(), $locale, [] );


		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $context );

		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderAddressManager = $orderBaseManager->getSubManager( 'address' );
		$orderProductManager = $orderBaseManager->getSubManager( 'product' );
		$orderServiceManager = $orderBaseManager->getSubManager( 'service' );


		$price = $priceManager->create();
		$price->setRebate( '3.01' );
		$price->setValue( '43.12' );
		$price->setCosts( '1.11' );
		$price->setTaxRate( '0.00' );
		$price->setCurrencyId( 'EUR' );

		$prod1 = $orderProductManager->create();
		$prod1->setProductCode( 'prod1' );
		$prod1->setPrice( $price );

		$price = $priceManager->create();
		$price->setRebate( '4.00' );
		$price->setValue( '20.00' );
		$price->setCosts( '2.00' );
		$price->setTaxRate( '0.50' );
		$price->setCurrencyId( 'EUR' );

		$prod2 = $orderProductManager->create();
		$prod2->setProductCode( 'prod2' );
		$prod2->setPrice( $price );


		$this->products = [$prod1, $prod2];
		$this->coupons = map( ['OPQR' => [$prod1]] );

		$this->addresses = array(
			'payment' => [0 => $orderAddressManager->create()->setType( 'payment' )->setId( null )],
			'delivery' => [0 => $orderAddressManager->create()->setType( 'delivery' )->setId( null )],
		);

		$this->services = array(
			'payment' => [0 => $orderServiceManager->create()->setType( 'payment' )->setCode( 'testpay' )->setId( null )],
			'delivery' => [1 => $orderServiceManager->create()->setType( 'delivery' )->setCode( 'testship' )->setId( null )],
		);
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->products, $this->addresses, $this->services, $this->coupons );
	}


	public function testArrayMethods()
	{
		$this->assertFalse( isset( $this->object['test'] ) );
		$this->assertEquals( null, $this->object['test'] );

		$this->object['test'] = 'value';

		$this->assertTrue( isset( $this->object['test'] ) );
		$this->assertEquals( 'value', $this->object['test'] );

		$this->expectException( \LogicException::class );
		unset( $this->object['test'] );
	}


	public function testMagicMethods()
	{
		$this->assertFalse( isset( $this->object->test ) );
		$this->assertEquals( null, $this->object->test );

		$this->object->test = 'value';

		$this->assertTrue( isset( $this->object->test ) );
		$this->assertEquals( 'value', $this->object->test );
	}


	public function testGetSet()
	{
		$this->assertEquals( false, $this->object->get( 'test', false ) );

		$this->object->set( 'test', 'value' );

		$this->assertEquals( 'value', $this->object->get( 'test', false ) );
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
		$this->assertEquals( $products, $this->object->getProducts() );
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
		$this->assertEquals( [0 => $product], $this->object->getProducts()->toArray() );
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
		$this->assertEquals( [1 => $testProduct, 2 => $product], $this->object->getProducts()->toArray() );
	}


	public function testDeleteProduct()
	{
		$this->object->addProduct( $this->products[0] );
		$result = $this->object->deleteProduct( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertSame( [], $this->object->getProducts()->toArray() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetProducts()
	{
		$this->object->setProducts( $this->products );

		$this->assertSame( $this->products, $this->object->getProducts()->toArray() );
		$this->assertSame( $this->products[1], $this->object->getProduct( 1 ) );
	}


	public function testSetProducts()
	{
		$result = $this->object->setProducts( $this->products );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertSame( $this->products, $this->object->getProducts()->toArray() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddAddress()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		$result = $this->object->addAddress( $this->addresses[$type][0], $type );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $this->addresses[$type], $this->object->getAddress( $type ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddAddressMultiple()
	{
		$this->object->addAddress( $this->addresses['payment'][0], 'payment' );
		$result = $this->object->addAddress( $this->addresses['payment'][0], 'payment' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( 2, count( $this->object->getAddress( 'payment' ) ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddAddressPosition()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;

		$this->object->addAddress( $this->addresses[$type][0], $type );
		$result = $this->object->addAddress( $this->addresses[$type][0], $type, 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $this->addresses[$type], $this->object->getAddress( $type ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testDeleteAddress()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		$this->object->setAddresses( $this->addresses );
		$result = $this->object->deleteAddress( $type );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( [], $this->object->getAddress( $type ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testDeleteAddressPosition()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		$this->object->setAddresses( $this->addresses );

		$result = $this->object->deleteAddress( $type, 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( [], $this->object->getAddress( $type ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testDeleteAddressPositionInvalid()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		$this->object->setAddresses( $this->addresses );

		$result = $this->object->deleteAddress( $type, 1 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $this->addresses[$type], $this->object->getAddress( $type ) );
	}


	public function testGetAddress()
	{
		$this->object->setAddresses( $this->addresses );
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;

		$this->assertEquals( $this->addresses[$type], $this->object->getAddress( $type ) );
	}


	public function testGetAddressSingle()
	{
		$this->object->setAddresses( $this->addresses );
		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;

		$this->assertEquals( $this->addresses[$type][0], $this->object->getAddress( $type, 0 ) );
	}


	public function testGetAddressException()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->getAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, 0 );
	}


	public function testSetAddresses()
	{
		$result = $this->object->setAddresses( $this->addresses );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $this->addresses, $this->object->getAddresses()->toArray() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddService()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$result = $this->object->addService( $this->services[$type][0], $type );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( 1, count( $this->object->getService( $type ) ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddServicePosition()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;

		$this->object->addService( $this->services[$type][0], $type );
		$result = $this->object->addService( $this->services[$type][0], $type, 0 );

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


	public function testDeleteServicePosition()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$this->object->setServices( $this->services );

		$result = $this->object->deleteService( $type, 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( [], $this->object->getService( $type ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testDeleteServicePositionInvalid()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$this->object->setServices( $this->services );

		$result = $this->object->deleteService( $type, 1 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $this->services[$type], $this->object->getService( $type ) );
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

		$service = $this->object->getService( 'payment', 0 );
		$this->assertEquals( 'testpay', $service->getCode() );
	}


	public function testGetServiceException()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->getService( 'payment', 100 );
	}


	public function testSetServices()
	{
		$result = $this->object->setServices( $this->services );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( $this->services, $this->object->getServices()->toArray() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testAddCoupon()
	{
		$result = $this->object->addCoupon( 'OPQR' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( ['OPQR' => []], $this->object->getCoupons()->toArray() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testDeleteCoupon()
	{
		$this->object->setCoupons( $this->coupons );
		$result = $this->object->deleteCoupon( 'OPQR' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
		$this->assertEquals( [], $this->object->getCoupons()->toArray() );
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
		$this->assertEquals( map( ['OPQR' => $this->coupons['OPQR']] ), $this->object->getCoupons() );
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

		foreach( $this->addresses as $type => $addresses )
		{
			foreach( $addresses as $address ) {
				$this->object->addAddress( $address, $type );
			}
		}

		foreach( $this->services as $type => $services )
		{
			foreach( $services as $service ) {
				$this->object->addService( $service, $type );
			}
		}

		$result = $this->object->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result );
	}


	public function testCheckInvalid()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->check( -1 );
	}


	public function testCheckAllFailure()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
	}


	public function testCheckProductsFailure()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->check( \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
	}


	/**
	 * @param string $code
	 */
	protected function createProduct( $code )
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() );
		$orderProductManager = $orderManager->getSubManager( 'base' )->getSubManager( 'product' );
		$product = $orderProductManager->create();

		$price = \Aimeos\MShop\Price\Manager\Factory::create( \TestHelperMShop::getContext() )->create();
		$price->setValue( '2.99' );

		$product->setPrice( $price );
		$product->setProductCode( $code );

		return $product;
	}
}
