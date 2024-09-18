<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2024
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


class Example extends \Aimeos\MShop\Common\Manager\Decorator\Base
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

		$this->mock = $this->createMock( \Aimeos\MShop\Product\Manager\Standard::class );
		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Example( $this->mock, $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->mock, $this->context );
	}


	public function testCall()
	{
		$this->mock->expects( $this->once() )->method( '__call' )->willReturn( true );

		$this->assertTrue( $this->object->invalid() );
	}


	public function testAddFilter()
	{
		$this->object->addFilter( 'object', function() {} );
	}


	public function testClear()
	{
		$this->assertSame( $this->object, $this->object->clear( [-1] ) );
	}


	public function testCreate()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->mock->expects( $this->once() )->method( 'create' )
			->willReturn( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->create( [] ) );
	}


	public function testFilter()
	{
		$filter = \Aimeos\MShop::create( $this->context, 'product' )->filter();

		$this->mock->expects( $this->once() )->method( 'filter' )
			->willReturn( $filter );

		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter() );
	}


	public function testDelete()
	{
		$this->assertSame( $this->object, $this->object->delete( -1 ) );
	}


	public function testGet()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->mock->expects( $this->once() )->method( 'get' )
			->willReturn( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->get( -1 ) );
	}


	public function testGetResourceType()
	{
		$this->mock->expects( $this->once() )->method( 'getResourceType' )
			->willReturn( [] );

		$this->assertEquals( [], $this->object->getResourceType() );
	}


	public function testGetSaveAttributes()
	{
		$this->mock->expects( $this->once() )->method( 'getSaveAttributes' )
			->willReturn( [] );

		$this->assertEquals( [], $this->object->getSaveAttributes() );
	}


	public function testGetSearchAttributes()
	{
		$this->mock->expects( $this->once() )->method( 'getSearchAttributes' )
			->willReturn( [] );

		$this->assertEquals( [], $this->object->getSearchAttributes() );
	}


	public function testGetSubManager()
	{
		$this->mock->expects( $this->once() )->method( 'getSubManager' )
			->willReturn( $this->mock );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( '' ) );
	}


	public function testSave()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->mock->expects( $this->once() )->method( 'save' )
			->willReturn( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->save( $item ) );
	}


	public function testSaveRefs()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->mock->expects( $this->once() )->method( 'saveRefs' )->willReturn( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->saveRefs( $item ) );
	}


	public function testSearch()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$item = $manager->create();
		$total = 0;

		$this->mock->expects( $this->once() )->method( 'search' )
			->willReturn( map( [$item] ) );

		$this->assertEquals( [$item], $this->object->search( $manager->filter(), [], $total )->toArray() );
	}


	public function testSearchRefs()
	{
		$this->mock->expects( $this->once() )->method( 'searchRefs' )->willReturn( [] );

		$this->assertEquals( [], $this->object->searchRefs( [], [] ) );
	}


	public function testSetObject()
	{
		$this->assertSame( $this->object, $this->object->setObject( $this->mock ) );
	}


	public function testBegin()
	{
		$this->mock->expects( $this->once() )->method( 'begin' )
			->willReturn( $this->mock );

		$this->assertSame( $this->object, $this->object->begin() );
	}


	public function testCommit()
	{
		$this->mock->expects( $this->once() )->method( 'commit' )
			->willReturn( $this->mock );

		$this->assertSame( $this->object, $this->object->commit() );
	}


	public function testRollback()
	{
		$this->mock->expects( $this->once() )->method( 'rollback' )
			->willReturn( $this->mock );

		$this->assertSame( $this->object, $this->object->rollback() );
	}


	public function testGetManager()
	{
		$result = $this->access( 'getManager' )->invokeArgs( $this->object, [] );

		$this->assertSame( $this->mock, $result );
	}


	public function testType()
	{
		$this->mock->expects( $this->once() )->method( 'type' )->willReturn( ['product', 'lists'] );

		$this->assertEquals( ['product', 'lists'], $this->object->type() );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Common\Manager\Decorator\Example::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
