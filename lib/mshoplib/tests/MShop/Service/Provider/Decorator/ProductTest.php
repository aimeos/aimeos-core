<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class ProductTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop::create( $this->context, 'service' );
		$this->servItem = $servManager->create();

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Product::class )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )
			->getSubManager( 'base' )->create();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Product( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'getConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'product.include', $result );
		$this->assertArrayHasKey( 'product.exclude', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'product.include' => 'test',
			'product.exclude' => 'test2',
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['product.include'] );
		$this->assertNull( $result['product.exclude'] );
	}


	public function testCheckConfigBENoConfig()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['product.include'] );
		$this->assertNull( $result['product.exclude'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'product.include' => [],
			'product.exclude' => [],
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['product.include'] );
		$this->assertIsString( $result['product.exclude'] );
	}


	public function testIsAvailableNoConfig()
	{
		$this->servItem->setConfig( [] );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoInclude()
	{
		$this->servItem->setConfig( array( 'product.include' => '' ) );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExclude()
	{
		$this->servItem->setConfig( array( 'product.exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExclude()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'product.exclude' => 'CNC' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );
		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeMultiple()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'product.exclude' => 'CNE,CNC' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );
		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableInclude()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'product.include' => 'CNC' ) );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeMultiple()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'product.include' => 'CNC,CNE' ) );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	protected function getOrderProduct( $code )
	{
		$productManager = \Aimeos\MShop::create( $this->context, 'product' );
		$product = $productManager->find( $code );

		$orderProductManager = \Aimeos\MShop::create( $this->context, 'order/base/product' );
		$orderProduct = $orderProductManager->create()->copyFrom( $product )->setStockType( 'default' );

		return $orderProduct;
	}
}
