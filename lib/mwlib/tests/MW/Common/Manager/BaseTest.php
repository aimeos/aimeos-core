<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


namespace Aimeos\MW\Common\Manager;


interface Test1
{
}

interface Test2
{
}

class Test implements Test1, Test2
{
}


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = $this->getMockForAbstractClass( \Aimeos\MW\Common\Manager\Base::class );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testFilter()
	{
		$item = new \stdClass();
		$this->assertSame( $item, $this->access( 'applyFilter' )->invokeArgs( $this->object, [$item] ) );
	}


	public function testFilterTrue()
	{
		$this->object->addFilter( \stdClass::class, function( $item ) {
			return $item;
		} );

		$item = new \stdClass();
		$this->assertSame( $item, $this->access( 'applyFilter' )->invokeArgs( $this->object, [$item] ) );
	}


	public function testFilterFalse()
	{
		$this->object->addFilter( \stdClass::class, function( $item ) {
			return null;
		} );

		$this->assertNull( $this->access( 'applyFilter' )->invokeArgs( $this->object, [new \stdClass()] ) );
	}


	public function testFilterMultipleSameTrue()
	{
		$this->object->addFilter( Test1::class, function( $item ) {
			return $item;
		} );
		$this->object->addFilter( Test2::class, function( $item ) {
			return $item;
		} );

		$item = new Test();
		$this->assertSame( $item, $this->access( 'applyFilter' )->invokeArgs( $this->object, [$item] ) );
	}


	public function testFilterMultipleSameFalse()
	{
		$this->object->addFilter( Test1::class, function( $item ) {
			return $item;
		} );
		$this->object->addFilter( Test2::class, function( $item ) {
			return null;
		} );

		$this->assertNull( $this->access( 'applyFilter' )->invokeArgs( $this->object, [new Test()] ) );
	}


	public function testFilterMultipleIfaceTrue()
	{
		$this->object->addFilter( Test1::class, function( $item ) {
			return $item;
		} );
		$this->object->addFilter( Test2::class, function( $item ) {
			return $item;
		} );

		$item = new Test();
		$this->assertSame( $item, $this->access( 'applyFilter' )->invokeArgs( $this->object, [$item] ) );
	}


	public function testFilterMultipleIfaceFalse()
	{
		$this->object->addFilter( Test1::class, function( $item ) {
			return $item;
		} );
		$this->object->addFilter( Test2::class, function( $item ) {
			return null;
		} );

		$this->assertNull( $this->access( 'applyFilter' )->invokeArgs( $this->object, [new Test()] ) );
	}


	public function testGetCriteriaKeyList()
	{
		$criteria = new \Aimeos\MW\Criteria\PHP();

		$expr = array(
			$criteria->compare( '==', 'product.id', 1 ),
			$criteria->compare( '==', 'product.type', 'test' ),
		);
		$criteria->setConditions( $criteria->and( $expr ) );
		$criteria->setSortations( array( $criteria->sort( '+', 'sort:list(\'key\')' ) ) );


		$class = new \ReflectionClass( \Aimeos\MW\Common\Manager\Base::class );
		$method = $class->getMethod( 'getCriteriaKeyList' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( $criteria, array( 'product.id' ) ) );

		$this->assertEquals( array( 'list', 'product', 'product.id' ), $result );
	}


	public function testGetSearchFunctionsArray()
	{
		$func = function() {};

		$args = array(
			'code' => 'product.datestart',
			'internalcode' => 'mspro."start"',
			'internaltype' => 'string',
			'type' => 'datetime',
			'label' => 'test',
			'function' => $func,
		);

		$class = new \ReflectionClass( \Aimeos\MW\Common\Manager\Base::class );
		$method = $class->getMethod( 'getSearchFunctions' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( array( $args ) ) );
		$this->assertEquals( array( 'product.datestart' => function() {} ), $result );
	}


	public function testGetSearchTranslationsArray()
	{
		$args = array(
			'code' => 'product.datestart',
			'internalcode' => 'mspro."start"',
			'internaltype' => 'string',
			'type' => 'datetime',
			'label' => 'test',
		);

		$class = new \ReflectionClass( \Aimeos\MW\Common\Manager\Base::class );
		$method = $class->getMethod( 'getSearchTranslations' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( array( $args ) ) );
		$this->assertEquals( array( 'product.datestart' => 'mspro."start"' ), $result );
	}


	public function testGetSearchTranslationsAttribute()
	{
		$args = array(
			'code' => 'product.datestart',
			'internalcode' => 'mspro."start"',
			'internaltype' => 'string',
			'type' => 'datetime',
			'label' => 'test',
		);
		$attr = new \Aimeos\MW\Criteria\Attribute\Standard( $args );

		$class = new \ReflectionClass( \Aimeos\MW\Common\Manager\Base::class );
		$method = $class->getMethod( 'getSearchTranslations' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( array( $attr ) ) );
		$this->assertEquals( array( 'product.datestart' => 'mspro."start"' ), $result );
	}


	public function testGetSearchTranslationsException()
	{
		$class = new \ReflectionClass( \Aimeos\MW\Common\Manager\Base::class );
		$method = $class->getMethod( 'getSearchTranslations' );
		$method->setAccessible( true );

		$this->expectException( \Aimeos\MW\Exception::class );
		$method->invokeArgs( $this->object, array( array( [] ) ) );
	}


	public function testGetSearchTypesArray()
	{
		$args = array(
			'code' => 'product.datestart',
			'internalcode' => 'mspro."start"',
			'internaltype' => 'string',
			'type' => 'datetime',
			'label' => 'test',
		);

		$class = new \ReflectionClass( \Aimeos\MW\Common\Manager\Base::class );
		$method = $class->getMethod( 'getSearchTypes' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( array( $args ) ) );
		$this->assertEquals( array( 'product.datestart' => 'string' ), $result );
	}


	public function testGetSearchTypesAttribute()
	{
		$args = array(
			'code' => 'product.datestart',
			'internalcode' => 'mspro."start"',
			'internaltype' => 'string',
			'type' => 'datetime',
			'label' => 'test',
		);
		$attr = new \Aimeos\MW\Criteria\Attribute\Standard( $args );

		$class = new \ReflectionClass( \Aimeos\MW\Common\Manager\Base::class );
		$method = $class->getMethod( 'getSearchTypes' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( array( $attr ) ) );
		$this->assertEquals( array( 'product.datestart' => 'string' ), $result );
	}


	public function testGetSearchTypesException()
	{
		$class = new \ReflectionClass( \Aimeos\MW\Common\Manager\Base::class );
		$method = $class->getMethod( 'getSearchTypes' );
		$method->setAccessible( true );

		$this->expectException( \Aimeos\MW\Exception::class );
		$method->invokeArgs( $this->object, array( array( [] ) ) );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\MW\Common\Manager\Base::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
