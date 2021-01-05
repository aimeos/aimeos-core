<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class BasketLimitsTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $products;
	private $order;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();
		$this->order = \Aimeos\MShop::create( $context, 'order/base' )->create()->off(); // remove event listeners

		$orderBaseProductManager = \Aimeos\MShop::create( $context, 'order/base/product' );
		$search = $orderBaseProductManager->filter();
		$search->setConditions( $search->and( array(
			$search->compare( '==', 'order.base.product.prodcode', array( 'CNE', 'CNC' ) ),
			$search->compare( '==', 'order.base.product.price', array( '600.00', '36.00' ) )
		) ) );
		$items = $orderBaseProductManager->search( $search )->toArray();

		if( count( $items ) < 2 ) {
			throw new \RuntimeException( 'Please fix the test data in your database.' );
		}

		foreach( $items as $item ) {
			$this->products[$item->getProductCode()] = $item;
		}

		$this->products['CNE']->setQuantity( 2 );
		$this->products['CNC']->setQuantity( 1 );

		$config = array(
			'min-value'=> array( 'EUR' => '75.00' ),
			'max-value'=> array( 'EUR' => '625.00' ),
			'min-products' => '2',
			'max-products' => 5
		);

		$plugin = \Aimeos\MShop::create( $context, 'plugin' )->create()->setConfig( $config );
		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\BasketLimits( $context, $plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->order );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'min-products' => '10',
			'max-products' => '100',
			'min-value' => ['EUR' => '100.00'],
			'max-value' => ['EUR' => '1000.00'],
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertEquals( null, $result['min-products'] );
		$this->assertEquals( null, $result['max-products'] );
		$this->assertEquals( null, $result['min-value'] );
		$this->assertEquals( null, $result['max-value'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 4, count( $list ) );
		$this->assertArrayHasKey( 'min-products', $list );
		$this->assertArrayHasKey( 'max-products', $list );
		$this->assertArrayHasKey( 'min-value', $list );
		$this->assertArrayHasKey( 'max-value', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdate()
	{
		$this->products['CNE']->setQuantity( 4 );
		$this->order->addProduct( $this->products['CNE'] );
		$value = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;

		$this->assertEquals( $value, $this->object->update( $this->order, 'check.after', $value ) );
	}


	public function testUpdateMinProductsFails()
	{
		$this->order->addProduct( $this->products['CNC'] );
		$value = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $value );
	}


	public function testUpdateMaxProductsFails()
	{
		$this->products['CNE']->setQuantity( 6 );
		$this->order->addProduct( $this->products['CNE'] );
		$value = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $value );
	}


	public function testUpdateMinValueFails()
	{
		$this->order->addProduct( $this->products['CNE'] );
		$value = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $value );
	}


	public function testUpdateMaxValueFails()
	{
		$this->products['CNC']->setQuantity( 2 );
		$this->order->addProduct( $this->products['CNC'] );
		$value = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $value );
	}
}
