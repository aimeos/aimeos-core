<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\MShop\Catalog\Manager\Decorator;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $stub;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->stub = $this->getMockBuilder( \Aimeos\MShop\Catalog\Manager\Standard::class )
			->disableOriginalConstructor()
			->getMock();

		$this->object = $this->getMockBuilder( \Aimeos\MShop\Catalog\Manager\Decorator\Base::class )
			->setConstructorArgs( [$this->stub, $this->context] )
			->getMockForAbstractClass();
	}


	protected function tearDown() : void
	{
		unset( $this->context, $this->object, $this->stub );
	}


	public function testCall()
	{
		$stub = $this->getMockBuilder( \Aimeos\MShop\Catalog\Manager\Standard::class )
			->disableOriginalConstructor()
			->getMock();

		$object = $this->getMockBuilder( \Aimeos\MShop\Common\Manager\Decorator\Base::class )
			->setConstructorArgs( [$stub, $this->context] )
			->getMockForAbstractClass();

		$stub->expects( $this->once() )->method( '__call' )->will( $this->returnValue( true ) );

		$this->assertTrue( $object->invalid() );
	}


	public function testCreateListItem()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->createListItem();

		$this->stub->expects( $this->once() )->method( 'createListItem' )
			->will( $this->returnValue( $item ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $this->object->createListItem( [] ) );
	}


	public function testFind()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->create();

		$this->stub->expects( $this->once() )->method( 'find' )
			->will( $this->returnValue( $item ) );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $this->object->find( -1 ) );
	}


	public function testGetPath()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->create();

		$this->stub->expects( $this->once() )->method( 'getPath' )
			->will( $this->returnValue( map( $item ) ) );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $this->object->getPath( -1 )->first() );
	}


	public function testGetTree()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->create();

		$this->stub->expects( $this->once() )->method( 'getTree' )
			->will( $this->returnValue( $item ) );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $this->object->getTree( -1 ) );
	}


	public function testInsert()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->create();

		$this->stub->expects( $this->once() )->method( 'insert' )
			->will( $this->returnValue( $item ) );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $this->object->insert( $item ) );
	}


	public function testMove()
	{
		$this->stub->expects( $this->once() )->method( 'move' )
			->will( $this->returnSelf() );

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Manager\Iface::class, $this->object->move( -1 ) );
	}
}
