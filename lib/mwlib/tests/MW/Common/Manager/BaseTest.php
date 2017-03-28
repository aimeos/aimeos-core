<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\Common\Manager;


class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = $this->getMockForAbstractClass( '\Aimeos\MW\Common\Manager\Base' );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetCriteriaKeyList()
	{
		$criteria = new \Aimeos\MW\Criteria\PHP();

		$expr = array(
			$criteria->compare( '==', 'product.id', 1 ),
			$criteria->compare( '==', 'product.type', 'test' ),
		);
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$criteria->setSortations( array( $criteria->sort( '+', 'sort:list(\'key\')' ) ) );


		$class = new \ReflectionClass( '\Aimeos\MW\Common\Manager\Base' );
		$method = $class->getMethod( 'getCriteriaKeyList' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( $criteria, array( 'product.id' ) ) );

		$this->assertEquals( array( 'list', 'product', 'product.id' ), $result );
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

		$class = new \ReflectionClass( '\Aimeos\MW\Common\Manager\Base' );
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

		$class = new \ReflectionClass( '\Aimeos\MW\Common\Manager\Base' );
		$method = $class->getMethod( 'getSearchTranslations' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( array( $attr ) ) );
		$this->assertEquals( array( 'product.datestart' => 'mspro."start"' ), $result );
	}


	public function testGetSearchTranslationsException()
	{
		$class = new \ReflectionClass( '\Aimeos\MW\Common\Manager\Base' );
		$method = $class->getMethod( 'getSearchTranslations' );
		$method->setAccessible( true );

		$this->setExpectedException( '\Aimeos\Mw\Exception' );
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

		$class = new \ReflectionClass( '\Aimeos\MW\Common\Manager\Base' );
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

		$class = new \ReflectionClass( '\Aimeos\MW\Common\Manager\Base' );
		$method = $class->getMethod( 'getSearchTypes' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( array( $attr ) ) );
		$this->assertEquals( array( 'product.datestart' => 'string' ), $result );
	}


	public function testGetSearchTypesException()
	{
		$class = new \ReflectionClass( '\Aimeos\MW\Common\Manager\Base' );
		$method = $class->getMethod( 'getSearchTypes' );
		$method->setAccessible( true );

		$this->setExpectedException( '\Aimeos\Mw\Exception' );
		$method->invokeArgs( $this->object, array( array( [] ) ) );
	}
}
