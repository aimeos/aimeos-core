<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Criteria;


class PHPTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\Criteria\PHP();
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testCombine()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\PHP::class, $this->object->combine( '&&', [] ) );
	}


	public function testCompare()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Compare\PHP::class, $this->object->compare( '!=', 'name', 'value' ) );
	}


	public function testSort()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Sort\PHP::class, $this->object->sort( '+', 'name' ) );
	}


	public function testGetOperators()
	{
		$expected = array(
			'combine' => ['&&', '||', '!'],
			'compare' => ['>', '>=', '<', '<=', '==', '!=', '-'],
			'sort' => ['+', '-'],
		);
		$actual = $this->object->getOperators();
		$this->assertEquals( $expected, $actual );
	}


	public function testTranslate()
	{
		$translations = array( 'int_value' => '$intval', 'str_value' => '$strval' );

		$this->assertEquals( ["\$strval"], $this->object->translate( array( $this->object->sort( '+', 'str_value' ) ), $translations ) );
		$this->assertEquals( ["\$strval"], $this->object->translate( array( $this->object->compare( '==', 'str_value', 1 ) ), $translations ) );
		$this->assertEquals( [], $this->object->translate( array( $this->object->and( [] ) ), $translations ) );
	}


	public function testGetConditionSource()
	{
		$intval = 1;
		$strval = 'test';

		$types = array( 'int_value' => 'int', 'str_value' => 'string' );
		$translations = array( 'int_value' => '$intval', 'str_value' => '$strval' );
		$plugins = array( 'int_value' => new TestPlugin() );

		$result = $this->object->getConditionSource( $types, $translations );
		$this->assertEquals( "1 == 1", $result );
		$this->assertEquals( true, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'int_value', 'a' ), $this->object->compare( '==', 'str_value', 'test' ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$result = $this->object->getConditionSource( $types, $translations, $plugins );
		$this->assertEquals( "( \$intval == 10 && \$strval == 'test' )", $result );
		$this->assertEquals( false, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'int_value', array( 1, 2, 4, 8 ) ), $this->object->compare( '==', 'str_value', 'test' ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$result = $this->object->getConditionSource( $types, $translations );
		$this->assertEquals( "( ( \$intval == 1 || \$intval == 2 || \$intval == 4 || \$intval == 8 ) && \$strval == 'test' )", $result );
		$this->assertEquals( true, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'int_value', 1 ), $this->object->compare( '!=', 'int_value', 2 ) );
		$this->object->setConditions( $this->object->combine( '!', array( $this->object->and( $expr ) ) ) );
		$result = $this->object->getConditionSource( $types, $translations );
		$this->assertEquals( " ! ( \$intval == 1 && \$intval != 2 )", $result );
		$this->assertEquals( false, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'int_value', null ), $this->object->compare( '!=', 'str_value', null ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$result = $this->object->getConditionSource( $types, $translations );
		$this->assertEquals( "( \$intval === null && \$strval !== null )", $result );
		$this->assertEquals( false, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'int_value', 1 ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$result = $this->object->getConditionSource( $types, $translations );
		$this->assertEquals( "( \$intval == 1 )", $result );
		$this->assertEquals( true, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'str_value', 'test' ) );
		$expr = array( $this->object->compare( '==', 'int_value', 1 ), $this->object->and( $expr ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$result = $this->object->getConditionSource( $types, $translations );
		$this->assertEquals( "( \$intval == 1 && ( \$strval == 'test' ) )", $result );
		$this->assertEquals( true, eval( 'return ' . $result . ';' ) );

		$types = array( 'column' => 'bool' );
		$this->object->setConditions( $this->object->compare( '==', 'column', 1 ) );
		$this->assertEquals( "column == 1", $this->object->getConditionSource( $types ) );
	}


	public function testGetConditionSourceInvalidName()
	{
		$types = array( 'int_value' => \Aimeos\MW\DB\Statement\Base::PARAM_INT );

		$this->object->setConditions( $this->object->compare( '==', 'ival', 10 ) );
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->getConditionSource( $types );
	}


	public function testGetConditionSourceInvalidOperator()
	{
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->setConditions( $this->object->compare( '?', 'int_value', 10 ) );
	}


	public function testGetConditions()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Compare\PHP::class, $this->object->getConditions() );

		$conditions = $this->object->compare( '==', 'int_value', 10 );
		$this->object->setConditions( $conditions );
		$this->assertEquals( $conditions, $this->object->getConditions() );
	}


	public function testGetSortationSource()
	{
		$ascIntList = array( 1, 9, 5 );
		$descStrList = array( 'abc', 'xyz', 'mno' );

		$types = array( 'asc_array' => 'int', 'desc_array' => 'string' );
		$translations = array( 'asc_array' => '$ascIntList', 'desc_array' => '$descStrList' );

		$sortations = [];
		$sortations[] = $this->object->sort( '+', 'asc_array' );
		$sortations[] = $this->object->sort( '-', 'desc_array' );
		$this->object->setSortations( $sortations );

		$result = $this->object->getSortationSource( $types, $translations );
		$this->assertEquals( 'asort($ascIntList); arsort($descStrList);', $result );
		$this->assertEquals( array( 0 => 1, 2 => 5, 1 => 9 ), $ascIntList );
		$this->assertEquals( array( 1 => 'xyz', 2 => 'mno', 0 => 'abc' ), $descStrList );
	}


	public function testGetSortationSourceInvalidName()
	{
		$types = array( 'asc_array' => 'int' );
		$translations = array( 'asc_array' => 'asc_int_list' );

		$this->object->setSortations( array( $this->object->sort( '+', 'asc_col' ) ) );
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->getSortationSource( $types, $translations );
	}


	public function testGetSortationSourceInvalidDirection()
	{
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->setSortations( array( $this->object->sort( '/', 'asc_array' ) ) );
	}


	public function testGetSortationNoSortation()
	{
		$ascIntList = array( 1, 9, 5 );

		$types = array( 'asc_array' => 'int', 'desc_array' => 'string' );
		$translations = array( 'asc_array' => '$ascIntList', 'desc_array' => '$descStrList' );

		$result = $this->object->getSortationSource( $types, $translations );
		$this->assertEquals( 'asort($ascIntList);', $result );
		$this->assertEquals( array( 0 => 1, 2 => 5, 1 => 9 ), $ascIntList );
	}


	public function testGetSortations()
	{
		$this->assertEquals( [], $this->object->getSortations() );

		$sortations = array( $this->object->sort( '+', 'asc_array' ) );
		$this->object->setSortations( $sortations );
		$this->assertEquals( $sortations, $this->object->getSortations() );
	}


	public function testSlice()
	{
		$this->assertEquals( 0, $this->object->getOffset() );
		$this->assertEquals( 100, $this->object->getLimit() );

		$this->object->slice( 10, 20 );

		$this->assertEquals( 10, $this->object->getOffset() );
		$this->assertEquals( 20, $this->object->getLimit() );
	}


	public function testToArray()
	{
		$array = [
			'&&' => [
				['==' => ['stringvar' => 'value']],
				['>' => ['intvar' => 10]],
			]
		];
		$this->object->setConditions( $this->object->parse( $array ) );

		$this->assertEquals( $array, $this->object->__toArray() );
	}
}


/**
 * Test plugin class
 */
class TestPlugin implements \Aimeos\MW\Criteria\Plugin\Iface
{
	public function translate( $value )
	{
		switch( $value )
		{
			case 'a': return 10;
			default: return $value;
		}
	}

	public function reverse( $value )
	{
		switch( $value )
		{
			case 10: return 'a';
			default: return $value;
		}
	}
}
