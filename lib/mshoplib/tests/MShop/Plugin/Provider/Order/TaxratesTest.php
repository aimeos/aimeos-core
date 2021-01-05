<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class TaxratesTest extends \PHPUnit\Framework\TestCase
{
	private $address;
	private $basket;
	private $context;
	private $object;
	private $plugin;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();

		$this->address = \Aimeos\MShop::create( $this->context, 'order/base/address' )->create()->setCountryId( 'US' );
		$this->basket = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off();
		$this->plugin = \Aimeos\MShop::create( $this->context, 'plugin' )->create();

		$this->plugin->setConfig( ['country-taxrates' => ['US' => '5'], 'state-taxrates' => ['CA' => '6.25']] );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\Taxrates( $this->context, $this->plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->plugin, $this->basket, $this->address, $this->context );
	}


	public function testCheckConfigBE()
	{
		$attributes = ['country-taxrates' => ['US' => '5'], 'state-taxrates' => ['CA' => '6.25']];

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( null, $result['country-taxrates'] );
		$this->assertEquals( null, $result['state-taxrates'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'country-taxrates', $list );
		$this->assertArrayHasKey( 'state-taxrates', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->basket ) );
	}


	public function testUpdateProduct()
	{
		$orderProduct = $this->getOrderProductItem();
		$orderProduct->getPrice()->setTaxrate( '20.00' );

		$this->basket->addAddress( $this->address, 'payment' );

		$result = $this->object->update( $this->basket, 'addProduct.after', $orderProduct );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $result );
		$this->assertEquals( '5.00', $result->getPrice()->getTaxRate() );
	}


	public function testUpdateProductState()
	{
		$orderProduct = $this->getOrderProductItem();
		$orderProduct->getPrice()->setTaxrate( '20.00' );

		$this->basket->addAddress( $this->address->setState( 'CA' ), 'payment' );

		$result = $this->object->update( $this->basket, 'addProduct.after', $orderProduct );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $result );
		$this->assertEquals( '6.25', $result->getPrice()->getTaxRate() );
	}


	public function testUpdateAll()
	{
		$orderProduct = $this->getOrderProductItem();
		$orderProduct->getPrice()->setTaxrate( '20.00' );

		$this->basket->addProduct( $this->getOrderProductItem() );
		$this->basket->addAddress( $this->address, 'delivery' );

		$result = $this->object->update( $this->basket, 'addAddress.after', $this->address );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $result );

		foreach( $this->basket->getProducts() as $item ) {
			$this->assertEquals( '5.00', $item->getPrice()->getTaxRate() );
		}
	}


	public function testUpdateAllState()
	{
		$orderProduct = $this->getOrderProductItem();
		$orderProduct->getPrice()->setTaxrate( '20.00' );

		$this->basket->addProduct( $this->getOrderProductItem() );
		$this->basket->addAddress( $this->address->setState( 'CA' ), 'delivery' );

		$result = $this->object->update( $this->basket, 'addAddress.after', $this->address );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $result );

		foreach( $this->basket->getProducts() as $item ) {
			$this->assertEquals( '6.25', $item->getPrice()->getTaxRate() );
		}
	}


	/**
	 * Returns two ordered product item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item
	 * @throws \Exception If product item isn't found
	 */
	protected function getOrderProductItem()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order/base/product' );

		$search = $manager->filter();
		$expr = array(
			$search->compare( '==', 'order.base.product.prodcode', 'CNE' ),
			$search->compare( '==', 'order.base.product.price', '36.00' ),
		);
		$search->setConditions( $search->and( $expr ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( 'No ordered product found' );
		}

		$item->getPrice()->setTaxFlag( false );
		return $item;
	}
}
