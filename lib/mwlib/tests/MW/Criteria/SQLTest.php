<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Criteria;


class SQLTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		if( \TestHelperMw::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$dbm = \TestHelperMw::getDBManager();

		$conn = $dbm->acquire();
		$this->object = new \Aimeos\MW\Criteria\SQL( $conn );
		$dbm->release( $conn );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testAdd()
	{
		$expected = $this->object->and( [
			$this->object->is( 'test', '==', 'value' ),
			$this->object->getConditions(),
		] );
		$result = $this->object->add( 'test', '==', 'value' );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $result );
		$this->assertEquals( $expected, $result->getConditions() );
	}


	public function testAddArray()
	{
		$expected = $this->object->and( [
			$this->object->and( [
				$this->object->is( 'test', '=~', 'value' ),
				$this->object->is( 'key', '=~', 'val' )
			] ),
			$this->object->getConditions()
		] );
		$result = $this->object->add( ['test' => 'value', 'key' => 'val'], '=~' );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $result );
		$this->assertEquals( $expected, $result->getConditions() );
	}


	public function testAddExpression()
	{
		$expected = $this->object->and( [
			$this->object->is( 'test', '==', 'value' ),
			$this->object->getConditions(),
		] );
		$result = $this->object->add( $this->object->is( 'test', '==', 'value' ) );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $result );
		$this->assertEquals( $expected, $result->getConditions() );
	}


	public function testMake()
	{
		$func = $this->object->make( 'test', [1, null, 2] );
		$this->assertEquals( 'test(1,null,2)', $func );
	}


	public function testGetOperators()
	{
		$expected = array(
			'combine' => ['&&', '||', '!'],
			'compare' => ['=~', '~=', '==', '!=', '>', '>=', '<', '<=', '-'],
			'sort' => ['+', '-'],
		);
		$actual = $this->object->getOperators();
		$this->assertEquals( $expected, $actual );
	}


	public function testIs()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Compare\SQL::class, $this->object->is( 'name', '!=', 'value' ) );
	}


	public function testCompare()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Compare\SQL::class, $this->object->compare( '!=', 'name', 'value' ) );
	}


	public function testAnd()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\SQL::class, $this->object->and( [] ) );
	}


	public function testOr()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\SQL::class, $this->object->or( [] ) );
	}


	public function testNot()
	{
		$expr = $this->object->is( 'name', '==', 'value' );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\SQL::class, $this->object->not( $expr ) );
	}


	public function testCombine()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\SQL::class, $this->object->combine( '&&', [] ) );
	}


	public function testSort()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Sort\SQL::class, $this->object->sort( '+', 'name' ) );
	}


	public function testTranslate()
	{
		$translations = array( 'int_column' => 'int_col', 'str_column' => 'str_col' );

		$this->assertEquals( ["str_col"], $this->object->translate( array( $this->object->sort( '+', 'str_column' ) ), $translations ) );
		$this->assertEquals( ["str_col"], $this->object->translate( array( $this->object->compare( '==', 'str_column', 1 ) ), $translations ) );
		$this->assertEquals( [], $this->object->translate( array( $this->object->and( [] ) ), $translations ) );
	}


	public function testGetConditionSource()
	{
		$types = array( 'int_column' => \Aimeos\MW\DB\Statement\Base::PARAM_INT, 'str_column' => \Aimeos\MW\DB\Statement\Base::PARAM_STR );
		$translations = array( 'int_column' => 'int_col', 'str_column' => 'str_col' );
		$plugins = array( 'int_column' => new TestSQL() );

		$this->assertEquals( "1 = 1", $this->object->getConditionSource( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'int_column', 'a' ), $this->object->compare( '==', 'str_column', 'test' ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$this->assertEquals( "( int_col = 10 AND str_col = 'test' )", $this->object->getConditionSource( $types, $translations, $plugins ) );

		$expr = array( $this->object->compare( '==', 'int_column', array( 1, 2, 4, 8 ) ), $this->object->compare( '==', 'str_column', 'test' ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$this->assertEquals( "( int_col IN (1,2,4,8) AND str_col = 'test' )", $this->object->getConditionSource( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'int_column', 1 ), $this->object->compare( '~=', 'str_column', array( 't1', 't2', 't3' ) ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$this->assertEquals( "( int_col = 1 AND (str_col LIKE '%t1%' ESCAPE '#' OR str_col LIKE '%t2%' ESCAPE '#' OR str_col LIKE '%t3%' ESCAPE '#') )", $this->object->getConditionSource( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'int_column', 1 ), $this->object->compare( '!=', 'int_column', 2 ) );
		$this->object->setConditions( $this->object->combine( '!', array( $this->object->and( $expr ) ) ) );
		$this->assertEquals( " NOT ( ( int_col = 1 AND int_col <> 2 ) )", $this->object->getConditionSource( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'int_column', null ), $this->object->compare( '!=', 'str_column', null ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$this->assertEquals( "( int_col IS NULL AND str_col IS NOT NULL )", $this->object->getConditionSource( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'int_column', 1 ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$this->assertEquals( "( int_col = 1 )", $this->object->getConditionSource( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'str_column', 'test' ) );
		$expr = array( $this->object->compare( '==', 'int_column', 1 ), $this->object->and( $expr ) );
		$this->object->setConditions( $this->object->and( $expr ) );
		$this->assertEquals( "( int_col = 1 AND ( str_col = 'test' ) )", $this->object->getConditionSource( $types, $translations ) );

		$types = array( 'column' => \Aimeos\MW\DB\Statement\Base::PARAM_BOOL );
		$this->object->setConditions( $this->object->compare( '==', 'column', 1 ) );
		$this->assertEquals( "column = 1", $this->object->getConditionSource( $types ) );
	}


	public function testGetConditionSourceInvalidName()
	{
		$types = array( 'int_column' => \Aimeos\MW\DB\Statement\Base::PARAM_INT );

		$this->object->setConditions( $this->object->compare( '==', 'icol', 10 ) );
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->getConditionSource( $types );
	}


	public function testGetConditionSourceInvalidOperator()
	{
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->setConditions( $this->object->compare( '?', 'int_column', 10 ) );
	}


	public function testGetConditions()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Compare\SQL::class, $this->object->getConditions() );

		$conditions = $this->object->compare( '==', 'int_column', 10 );
		$this->object->setConditions( $conditions );
		$this->assertEquals( $conditions, $this->object->getConditions() );
	}


	public function testGetSortationSource()
	{
		$types = array( 'asc_column' => \Aimeos\MW\DB\Statement\Base::PARAM_INT, 'desc_column' => \Aimeos\MW\DB\Statement\Base::PARAM_STR );
		$translations = array( 'asc_column' => 'asc_int_col', 'desc_column' => 'desc_str_col' );

		$sortations = [];
		$sortations[] = $this->object->sort( '+', 'asc_column' );
		$sortations[] = $this->object->sort( '-', 'desc_column' );
		$this->object->setSortations( $sortations );
		$this->assertEquals( 'asc_int_col ASC, desc_str_col DESC', $this->object->getSortationSource( $types, $translations ) );
	}


	public function testGetSortationSourceInvalidName()
	{
		$types = array( 'asc_column' => \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$translations = array( 'asc_column' => 'asc_int_col' );

		$this->object->setSortations( array( $this->object->sort( '+', 'asc_col' ) ) );
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->getSortationSource( $types, $translations );
	}


	public function testGetSortationSourceInvalidDirection()
	{
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->setSortations( array( $this->object->sort( '/', 'asc_column' ) ) );
	}


	public function testGetSortationNoSortation()
	{
		$types = array( 'asc_column' => \Aimeos\MW\DB\Statement\Base::PARAM_INT, 'desc_column' => \Aimeos\MW\DB\Statement\Base::PARAM_STR );

		$this->assertEquals( 'asc_column ASC', $this->object->getSortationSource( $types ) );

		$translations = array( 'asc_column' => 'asc_int_col', 'desc_column' => 'desc_str_col' );
		$this->assertEquals( 'asc_int_col ASC', $this->object->getSortationSource( $types, $translations ) );
	}


	public function testOrder()
	{
		$this->assertEquals( [], $this->object->getSortations() );

		$sortations = [$this->object->sort( '+', 'asc_column' )];
		$result = $this->object->order( 'asc_column' );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $result );
		$this->assertEquals( $sortations, $this->object->getSortations() );
	}


	public function testOrderDesc()
	{
		$this->assertEquals( [], $this->object->getSortations() );

		$sortations = [$this->object->sort( '-', 'desc_column' )];
		$result = $this->object->order( '-desc_column' );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $result );
		$this->assertEquals( $sortations, $this->object->getSortations() );
	}


	public function testOrderMultiple()
	{
		$this->assertEquals( [], $this->object->getSortations() );

		$sortations = [$this->object->sort( '+', 'asc_column' ), $this->object->sort( '-', 'desc_column' )];
		$result = $this->object->order( ['asc_column', '-desc_column'] );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $result );
		$this->assertEquals( $sortations, $this->object->getSortations() );
	}


	public function testGetSortations()
	{
		$this->assertEquals( [], $this->object->getSortations() );

		$sortations = array( $this->object->sort( '+', 'asc_column' ) );
		$this->object->setSortations( $sortations );
		$this->assertEquals( $sortations, $this->object->getSortations() );
	}


	public function testSliceOffsetLimit()
	{
		$this->assertEquals( 0, $this->object->getOffset() );
		$this->assertEquals( 100, $this->object->getLimit() );

		$this->object->slice( 10, 20 );

		$this->assertEquals( 10, $this->object->getOffset() );
		$this->assertEquals( 20, $this->object->getLimit() );
	}


	public function testParseEmptyArray()
	{
		$this->assertNull( $this->object->parse( [] ) );
	}


	public function testParseInvalid()
	{
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->parse( ['=][attribute.id]=15'] );
	}


	public function testParseInvalidOperator()
	{
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->parse( ['><' => ['name', 'value']] );
	}


	public function testParseInvalidStructure()
	{
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->parse( ['&&' => ['name', 'value']] );
	}


	public function testParseCompare()
	{
		$condition = $this->object->parse( ['==' => ['name' => 'value']] );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Compare\Iface::class, $condition );
		$this->assertEquals( '==', $condition->getOperator() );
		$this->assertEquals( 'name', $condition->getName() );
		$this->assertEquals( 'value', $condition->getValue() );
	}


	public function testParseCombine()
	{
		$array = array(
			'&&' => array(
				0 => array(
					'==' => array( 'name' => 'value' ),
				),
				1 => array(
					'==' => array( 'name' => 'value' ),
				),
			),
		);

		$condition = $this->object->parse( $array );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\Iface::class, $condition );
		$this->assertEquals( '&&', $condition->getOperator() );
		$this->assertEquals( 2, count( $condition->getExpressions() ) );

		foreach( $condition->getExpressions() as $expr )
		{
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Compare\Iface::class, $expr );
			$this->assertEquals( '==', $expr->getOperator() );
			$this->assertEquals( 'name', $expr->getName() );
			$this->assertEquals( 'value', $expr->getValue() );
		}
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
class TestSQL implements \Aimeos\MW\Criteria\Plugin\Iface
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
