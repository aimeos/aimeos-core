<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class CategoryTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Factory::createManager( $this->context, 'service' );
		$this->servItem = $servManager->createItem();

		$this->mockProvider = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Decorator\\Category' )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )
			->getSubManager( 'base' )->createItem();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Category( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'getConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'category.include', $result );
		$this->assertArrayHasKey( 'category.exclude', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'category.include' => 'test',
			'category.exclude' => 'test2',
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'null', $result['category.include'] );
		$this->assertInternalType( 'null', $result['category.exclude'] );
	}


	public function testCheckConfigBENoConfig()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'null', $result['category.include'] );
		$this->assertInternalType( 'null', $result['category.exclude'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'category.include' => [],
			'category.exclude' => 1,
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'string', $result['category.include'] );
		$this->assertInternalType( 'string', $result['category.exclude'] );
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
		$this->servItem->setConfig( array( 'category.include' => '' ) );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoExclude()
	{
		$this->servItem->setConfig( array( 'category.exclude' => '' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExclude()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'category.exclude' => 'new' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );
		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeMultiple()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'category.exclude' => 'cafe,new' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );
		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeNoPromotion()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'category.exclude' => 'cafe' ) );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableExcludeTree()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'category.exclude' => 'group' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' );
		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableInclude()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'category.include' => 'new' ) );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeMultiple()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'category.include' => 'cafe,new' ) );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeNoPromotion()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'category.include' => 'cafe' ) );

		$this->mockProvider->expects( $this->never() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableIncludeTree()
	{
		$this->basket->addProduct( $this->getOrderProduct( 'CNC' ) );
		$this->servItem->setConfig( array( 'category.include' => 'group' ) );

		$this->mockProvider->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );
		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	protected function getOrderProduct( $code )
	{
		$productManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );
		$product = $productManager->findItem( $code );

		$orderProductManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product' );
		$orderProduct = $orderProductManager->createItem()->copyFrom( $product );

		return $orderProduct;
	}
}