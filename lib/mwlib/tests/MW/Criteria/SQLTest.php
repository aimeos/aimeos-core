<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2017
 */


namespace Aimeos\MW\Criteria;


class SQLTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		if( \TestHelperMw::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$dbm = \TestHelperMw::getDBManager();

		$conn = $dbm->acquire();
		$this->object = new \Aimeos\MW\Criteria\SQL( $conn );
		$dbm->release( $conn );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCreateFunction()
	{
		$func = $this->object->createFunction( 'test', array( 1, 2, 3 ) );
		$this->assertEquals( 'test(1,2,3)', $func );
	}


	public function testGetOperators()
	{
		$expected = array(
			'combine' => array( '&&', '||', '!' ),
			'compare' => array( '=~', '~=', '==', '!=', '>', '>=', '<', '<=', '&', '|' ),
			'sort' => array( '+', '-' ),
		);
		$actual = $this->object->getOperators();
		$this->assertEquals( $expected, $actual );
	}


	public function testCombine()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Combine\\SQL', $this->object->combine( '||', [] ) );
	}


	public function testCompare()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Compare\\SQL', $this->object->compare( '!=', 'name', 'value' ) );
	}


	public function testSort()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Sort\\SQL', $this->object->sort( '+', 'name' ) );
	}


	public function testGetColumnString()
	{
		$translations = array( 'int_column' => 'int_col', 'str_column' => 'str_col' );

		$this->assertEquals( "str_col", $this->object->getColumnString( array( $this->object->sort( '+', 'str_column' ) ), $translations ) );
		$this->assertEquals( "str_col", $this->object->getColumnString( array( $this->object->compare( '==', 'str_column', 1 ) ), $translations ) );
		$this->assertEquals( "", $this->object->getColumnString( array( $this->object->combine( '&&', [] ) ), $translations ) );
	}


	public function testGetConditionString()
	{
		$types = array( 'int_column' => \Aimeos\MW\DB\Statement\Base::PARAM_INT, 'str_column' => \Aimeos\MW\DB\Statement\Base::PARAM_STR );
		$translations = array( 'int_column' => 'int_col', 'str_column' => 'str_col' );
		$plugins = array( 'int_column' => new TestSQL() );

		$this->assertEquals( "1 = 1", $this->object->getConditionString( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'int_column', 'a' ), $this->object->compare( '==', 'str_column', 'test' ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col = 10 AND str_col = 'test' )", $this->object->getConditionString( $types, $translations, $plugins ) );

		$expr = array( $this->object->compare( '==', 'int_column', array( 1, 2, 4, 8 ) ), $this->object->compare( '==', 'str_column', 'test' ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col IN (1,2,4,8) AND str_col = 'test' )", $this->object->getConditionString( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'int_column', 1 ), $this->object->compare( '~=', 'str_column', array( 't1', 't2', 't3' ) ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col = 1 AND (str_col LIKE '%t1%' ESCAPE '#' OR str_col LIKE '%t2%' ESCAPE '#' OR str_col LIKE '%t3%' ESCAPE '#') )", $this->object->getConditionString( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'int_column', 1 ), $this->object->compare( '!=', 'int_column', 2 ) );
		$this->object->setConditions( $this->object->combine( '!', array( $this->object->combine( '&&', $expr ) ) ) );
		$this->assertEquals( " NOT ( int_col = 1 AND int_col <> 2 )", $this->object->getConditionString( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'int_column', null ), $this->object->compare( '!=', 'str_column', null ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col IS NULL AND str_col IS NOT NULL )", $this->object->getConditionString( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'int_column', 1 ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col = 1 )", $this->object->getConditionString( $types, $translations ) );

		$expr = array( $this->object->compare( '==', 'str_column', 'test' ) );
		$expr = array( $this->object->compare( '==', 'int_column', 1 ), $this->object->combine( '&&', $expr ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col = 1 AND ( str_col = 'test' ) )", $this->object->getConditionString( $types, $translations ) );

		$types = array( 'column' => \Aimeos\MW\DB\Statement\Base::PARAM_BOOL);
		$this->object->setConditions( $this->object->compare( '==', 'column', 1 ) );
		$this->assertEquals( "column = 1", $this->object->getConditionString( $types ) );
	}


	public function testGetConditionStringInvalidName()
	{
		$types = array( 'int_column' => \Aimeos\MW\DB\Statement\Base::PARAM_INT );

		$this->object->setConditions( $this->object->compare( '==', 'icol', 10 ) );
		$this->setExpectedException('\\Aimeos\\MW\\Common\\Exception');
		$this->object->getConditionString( $types );
	}


	public function testGetConditionStringInvalidOperator()
	{
		$this->setExpectedException('\\Aimeos\\MW\\Common\\Exception');
		$this->object->setConditions( $this->object->compare( '?', 'int_column', 10 ) );
	}


	public function testGetConditions()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Compare\\SQL', $this->object->getConditions() );

		$conditions = $this->object->compare( '==', 'int_column', 10 );
		$this->object->setConditions( $conditions );
		$this->assertEquals( $conditions, $this->object->getConditions() );
	}


	public function testGetSortationString()
	{
		$types = array( 'asc_column' => \Aimeos\MW\DB\Statement\Base::PARAM_INT, 'desc_column' => \Aimeos\MW\DB\Statement\Base::PARAM_STR );
		$translations = array( 'asc_column' => 'asc_int_col', 'desc_column' => 'desc_str_col' );

		$sortations = [];
		$sortations[] = $this->object->sort( '+', 'asc_column' );
		$sortations[] = $this->object->sort( '-', 'desc_column' );
		$this->object->setSortations( $sortations );
		$this->assertEquals( 'asc_int_col ASC, desc_str_col DESC', $this->object->getSortationString( $types, $translations ) );
	}


	public function testGetSortationStringInvalidName()
	{
		$types = array( 'asc_column' => \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$translations = array( 'asc_column' => 'asc_int_col' );

		$this->object->setSortations( array( $this->object->sort( '+', 'asc_col' ) ) );
		$this->setExpectedException('\\Aimeos\\MW\\Common\\Exception');
		$this->object->getSortationString( $types, $translations );
	}


	public function testGetSortationStringInvalidDirection()
	{
		$this->setExpectedException('\\Aimeos\\MW\\Common\\Exception');
		$this->object->setSortations( array( $this->object->sort( '/', 'asc_column' ) ) );
	}


	public function testGetSortationNoSortation()
	{
		$types = array( 'asc_column' => \Aimeos\MW\DB\Statement\Base::PARAM_INT, 'desc_column' => \Aimeos\MW\DB\Statement\Base::PARAM_STR );

		$this->assertEquals('asc_column ASC', $this->object->getSortationString( $types ) );

		$translations = array( 'asc_column' => 'asc_int_col', 'desc_column' => 'desc_str_col' );
		$this->assertEquals('asc_int_col ASC', $this->object->getSortationString( $types, $translations ));
	}


	public function testGetSortations()
	{
		$this->assertEquals( [], $this->object->getSortations() );

		$sortations = array( $this->object->sort( '+', 'asc_column' ) );
		$this->object->setSortations( $sortations );
		$this->assertEquals( $sortations, $this->object->getSortations() );
	}


	public function testSlice()
	{
		$this->assertEquals( 0, $this->object->getSliceStart() );
		$this->assertEquals( 100, $this->object->getSliceSize() );

		$this->object->setSlice( 10, 20 );

		$this->assertEquals( 10, $this->object->getSliceStart() );
		$this->assertEquals( 20, $this->object->getSliceSize() );
	}


	public function testToConditionsEmptyArray()
	{
		$condition = $this->object->toConditions( [] );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Compare\\SQL', $condition );
	}


	public function testToConditionsInvalidOperator()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Common\\Exception' );
		$this->object->toConditions( array( '><' => array( 'name', 'value' ) ) );
	}


	public function testToConditionsCompare()
	{
		$array = array(
			'==' => array( 'name' => 'value' ),
		);

		$condition = $this->object->toConditions( $array );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Compare\\Iface', $condition );
		$this->assertEquals( '==', $condition->getOperator() );
		$this->assertEquals( 'name', $condition->getName() );
		$this->assertEquals( 'value', $condition->getValue() );
	}


	public function testToConditionsCombine()
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

		$condition = $this->object->toConditions( $array );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Combine\\Iface', $condition );
		$this->assertEquals( '&&', $condition->getOperator() );
		$this->assertEquals( 2, count( $condition->getExpressions() ) );

		foreach( $condition->getExpressions() as $expr )
		{
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Compare\\Iface', $expr );
			$this->assertEquals( '==', $expr->getOperator() );
			$this->assertEquals( 'name', $expr->getName() );
			$this->assertEquals( 'value', $expr->getValue() );
		}
	}


	public function testToSortations()
	{
		$array = array(
			'name1' => '+',
			'name2' => '-',
		);

		$sortations = $this->object->toSortations( $array );
		$this->assertEquals( 2, count( $sortations ) );

		foreach( $sortations as $sort ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Sort\\Iface', $sort );
		}

		$this->assertEquals( '+', $sortations[0]->getOperator() );
		$this->assertEquals( 'name1', $sortations[0]->getName() );
		$this->assertEquals( '-', $sortations[1]->getOperator() );
		$this->assertEquals( 'name2', $sortations[1]->getName() );
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
