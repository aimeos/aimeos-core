<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ProductStockTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;
	private $plugin;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->plugin = \Aimeos\MShop::create( $this->context, 'plugin' )->create();
		$this->order = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ProductStock( $this->context, $this->plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->plugin, $this->order, $this->context, $this->object );
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdateNone()
	{
		$this->assertEquals( null, $this->object->update( $this->order, 'check.after' ) );
	}


	public function testUpdateOk()
	{
		$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );
	}


	public function testUpdateOutOfStock()
	{
		$this->order->addProduct( $this->getOrderProduct( 'EFGH' ) );

		try
		{
			$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
			$this->object->update( $this->order, 'check.after', $part );

			throw new \RuntimeException( 'Expected exception not thrown' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertEquals( ['product' => ['0' => 'stock.notenough']], $e->getErrorCodes() );
			$this->assertEquals( [], $this->order->getProducts()->toArray() );
		}
	}


	public function testUpdateNoStockItem()
	{
		$this->order->addProduct( $this->getOrderProduct( 'QRST' ) );

		try
		{
			$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
			$this->object->update( $this->order, 'check.after', $part );

			throw new \RuntimeException( 'Expected exception not thrown' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertEquals( ['product' => ['0' => 'stock.notenough']], $e->getErrorCodes() );
			$this->assertEquals( [], $this->order->getProducts()->toArray() );
		}
	}


	public function testUpdateStockUnlimited()
	{
		$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
		$this->order->addProduct( $this->getOrderProduct( 'MNOP' )->setStockType( 'unitstock' ) );

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );
	}


	/**
	 * Returns an order product item
	 *
	 * @param string $code Unique product code
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item
	 */
	protected function getOrderProduct( $code )
	{
		$productItem = \Aimeos\MShop::create( $this->context, 'product' )->find( $code );

		return \Aimeos\MShop::create( $this->context, 'order/base/product' )
			->create()->copyFrom( $productItem );
	}
}
