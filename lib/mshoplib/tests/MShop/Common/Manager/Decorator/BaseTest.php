<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\Controller\Frontend\Attribute\Decorator;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $stub;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();

		$this->stub = $this->getMockBuilder( \Aimeos\MShop\Product\Manager\Standard::class )
			->disableOriginalConstructor()
			->getMock();

		$this->object = $this->getMockBuilder( \Aimeos\MShop\Common\Manager\Decorator\Base::class )
			->setConstructorArgs( [$this->stub, $this->context] )
			->getMockForAbstractClass();
	}


	protected function tearDown() : void
	{
		unset( $this->context, $this->object, $this->stub );
	}


	public function testCall()
	{
		$stub = $this->getMockBuilder( \Aimeos\MShop\Product\Manager\Standard::class )
			->disableOriginalConstructor()
			->setMethods( ['invalid'] )
			->getMock();

		$object = $this->getMockBuilder( \Aimeos\MShop\Common\Manager\Decorator\Base::class )
			->setConstructorArgs( [$stub, $this->context] )
			->getMockForAbstractClass();

		$stub->expects( $this->once() )->method( 'invalid' )->will( $this->returnValue( true ) );

		$this->assertTrue( $object->invalid() );
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

		$this->stub->expects( $this->once() )->method( 'create' )
			->will( $this->returnValue( $item ) );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->create( [] ) );
	}


	public function testCreateItem()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->stub->expects( $this->once() )->method( 'create' )
			->will( $this->returnValue( $item ) );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->create( [] ) );
	}


	public function testCreateSearch()
	{
		$filter = \Aimeos\MShop::create( $this->context, 'product' )->filter();

		$this->stub->expects( $this->once() )->method( 'filter' )
			->will( $this->returnValue( $filter ) );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter() );
	}


	public function testFilter()
	{
		$filter = \Aimeos\MShop::create( $this->context, 'product' )->filter();

		$this->stub->expects( $this->once() )->method( 'filter' )
			->will( $this->returnValue( $filter ) );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter() );
	}


	public function testDelete()
	{
		$this->assertSame( $this->object, $this->object->delete( -1 ) );
	}


	public function testDeleteItem()
	{
		$this->assertSame( $this->object, $this->object->delete( -1 ) );
	}


	public function testDeleteItems()
	{
		$this->assertSame( $this->object, $this->object->delete( [-1] ) );
	}


	public function testGet()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->stub->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $item ) );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->get( -1 ) );
	}


	public function testGetItem()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->stub->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $item ) );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->get( -1 ) );
	}


	public function testGetResourceType()
	{
		$this->stub->expects( $this->once() )->method( 'getResourceType' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getResourceType() );
	}


	public function testGetSaveAttributes()
	{
		$this->stub->expects( $this->once() )->method( 'getSaveAttributes' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getSaveAttributes() );
	}


	public function testGetSearchAttributes()
	{
		$this->stub->expects( $this->once() )->method( 'getSearchAttributes' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getSearchAttributes() );
	}


	public function testGetSubManager()
	{
		$this->stub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $this->stub ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( '' ) );
	}


	public function testSave()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->stub->expects( $this->once() )->method( 'save' )
			->will( $this->returnValue( $item ) );

		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->save( $item ) );
	}


	public function testSearch()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$item = $manager->create();
		$total = 0;

		$this->stub->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( [$item] ) ) );

		$this->assertEquals( [$item], $this->object->search( $manager->filter(), [], $total )->toArray() );
	}


	public function testSearchItems()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$item = $manager->create();
		$total = 0;

		$this->stub->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( [$item] ) ) );

		$this->assertEquals( [$item], $this->object->search( $manager->filter(), [], $total )->toArray() );
	}


	public function testSetObject()
	{
		$this->assertSame( $this->object, $this->object->setObject( $this->stub ) );
	}


	public function testBegin()
	{
		$this->stub->expects( $this->once() )->method( 'begin' )
			->will( $this->returnValue( $this->stub ) );

		$this->assertSame( $this->object, $this->object->begin() );
	}


	public function testCommit()
	{
		$this->stub->expects( $this->once() )->method( 'commit' )
			->will( $this->returnValue( $this->stub ) );

		$this->assertSame( $this->object, $this->object->commit() );
	}


	public function testRollback()
	{
		$this->stub->expects( $this->once() )->method( 'rollback' )
			->will( $this->returnValue( $this->stub ) );

		$this->assertSame( $this->object, $this->object->rollback() );
	}


	public function testGetManager()
	{
		$result = $this->access( 'getManager' )->invokeArgs( $this->object, [] );

		$this->assertSame( $this->stub, $result );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Common\Manager\Decorator\Base::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
