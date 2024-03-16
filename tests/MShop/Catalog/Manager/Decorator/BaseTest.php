<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2024
 */


namespace Aimeos\MShop\Catalog\Manager\Decorator;


class Example extends Base
{
}


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $mock;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->mock = $this->createMock( \Aimeos\MShop\Catalog\Manager\Standard::class );
		$this->object = new \Aimeos\MShop\Catalog\Manager\Decorator\Example( $this->mock, $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->context, $this->object, $this->mock );
	}


	public function testCall()
	{
		$this->mock->expects( $this->once() )->method( '__call' )->willReturn( true );

		$this->assertTrue( $this->object->invalid() );
	}


	public function testCreateListItem()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->createListItem();

		$this->mock->expects( $this->once() )->method( 'createListItem' )
			->willReturn( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $this->object->createListItem( [] ) );
	}


	public function testFind()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->create();

		$this->mock->expects( $this->once() )->method( 'find' )
			->willReturn( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $this->object->find( -1 ) );
	}


	public function testGetPath()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->create();

		$this->mock->expects( $this->once() )->method( 'getPath' )
			->willReturn( map( $item ) );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $this->object->getPath( -1 )->first() );
	}


	public function testGetTree()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->create();

		$this->mock->expects( $this->once() )->method( 'getTree' )
			->willReturn( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $this->object->getTree( -1 ) );
	}


	public function testInsert()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->create();

		$this->mock->expects( $this->once() )->method( 'insert' )
			->willReturn( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $this->object->insert( $item ) );
	}


	public function testMove()
	{
		$this->mock->expects( $this->once() )->method( 'move' )
			->willReturnSelf();

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Manager\Iface::class, $this->object->move( -1 ) );
	}
}
