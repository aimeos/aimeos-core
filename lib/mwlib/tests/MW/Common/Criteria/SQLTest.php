<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Test plugin class
 */
class Criteria_Plugin_SQLTest implements MW_Common_Criteria_Plugin_Interface
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


/**
 * Test class for MW_Common_Criteria_SQL.
 */
class MW_Common_Criteria_SQLTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( TestHelper::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$dbm = TestHelper::getDBManager();

		$conn = $dbm->acquire();
		$this->_object = new MW_Common_Criteria_SQL( $conn );
		$dbm->release( $conn );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testCreateFunction()
	{
		$func = $this->_object->createFunction( 'test', array( 1, 2, 3 ) );
		$this->assertEquals( 'test(1,2,3)', $func );
	}


	public function testGetOperators()
	{
		$expected = array(
			'combine' => array( '&&', '||', '!' ),
			'compare' => array( '==', '!=', '~=', '>=', '<=', '>', '<', '&', '|', '=~' ),
			'sort' => array( '+', '-' ),
		);
		$actual = $this->_object->getOperators();
		$this->assertEquals( $expected, $actual );
	}


	public function testCombine()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Combine_SQL', $this->_object->combine( '||', array() ) );
	}


	public function testCompare()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Compare_SQL', $this->_object->compare( '!=', 'name', 'value' ) );
	}


	public function testSort()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Sort_SQL', $this->_object->sort( '+', 'name' ) );
	}


	public function testGetConditionString()
	{
		$types = array( 'int_column' => MW_DB_Statement_Abstract::PARAM_INT, 'str_column' => MW_DB_Statement_Abstract::PARAM_STR );
		$translations = array( 'int_column' => 'int_col', 'str_column' => 'str_col' );
		$plugins = array( 'int_column' => new Criteria_Plugin_SQLTest() );

		$this->assertEquals( "1 = 1", $this->_object->getConditionString( $types, $translations ) );

		$expr = array( $this->_object->compare( '==', 'int_column', 'a' ), $this->_object->compare( '==', 'str_column', 'test' ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col = 10 AND str_col = 'test' )", $this->_object->getConditionString( $types, $translations, $plugins ) );

		$expr = array( $this->_object->compare( '==', 'int_column', array( 1, 2, 4, 8 ) ), $this->_object->compare( '==', 'str_column', 'test' ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col IN (1,2,4,8) AND str_col = 'test' )", $this->_object->getConditionString( $types, $translations ) );

		$expr = array( $this->_object->compare( '==', 'int_column', 1 ), $this->_object->compare( '~=', 'str_column', array( 't1', 't2', 't3' ) ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col = 1 AND (str_col LIKE '%t1%' OR str_col LIKE '%t2%' OR str_col LIKE '%t3%') )", $this->_object->getConditionString( $types, $translations ) );

		$expr = array( $this->_object->compare( '==', 'int_column', 1 ), $this->_object->compare( '!=', 'int_column', 2 ) );
		$this->_object->setConditions( $this->_object->combine( '!', array( $this->_object->combine( '&&', $expr ) ) ) );
		$this->assertEquals( " NOT ( int_col = 1 AND int_col <> 2 )", $this->_object->getConditionString( $types, $translations ) );

		$expr = array( $this->_object->compare( '==', 'int_column', null ), $this->_object->compare( '!=', 'str_column', null ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col IS NULL AND str_col IS NOT NULL )", $this->_object->getConditionString( $types, $translations ) );

		$expr = array( $this->_object->compare( '==', 'int_column', 1 ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col = 1 )", $this->_object->getConditionString( $types, $translations ) );

		$expr = array( $this->_object->compare( '==', 'str_column', 'test' ) );
		$expr = array( $this->_object->compare( '==', 'int_column', 1 ), $this->_object->combine( '&&', $expr ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$this->assertEquals( "( int_col = 1 AND ( str_col = 'test' ) )", $this->_object->getConditionString( $types, $translations ) );

		$types = array( 'column' => MW_DB_Statement_Abstract::PARAM_BOOL);
		$this->_object->setConditions( $this->_object->compare( '==', 'column', 1 ) );
		$this->assertEquals( "column = 1", $this->_object->getConditionString( $types ) );
	}


	public function testGetConditionStringInvalidOperatorForNull()
	{
		// test exception in _createTerm:  'null value not allowed for operator'
		$types = array( 'str_column' => MW_DB_Statement_Abstract::PARAM_STR );

		$this->_object->setConditions( $this->_object->compare( '~=', 'str_column', null ) );

		$this->setExpectedException('MW_Common_Exception');
		$this->_object->getConditionString( $types );
	}


	public function testGetConditionStringInvalidName()
	{
		$types = array( 'int_column' => MW_DB_Statement_Abstract::PARAM_INT );

		$this->_object->setConditions( $this->_object->compare( '==', 'icol', 10 ) );
		$this->setExpectedException('MW_Common_Exception');
		$this->_object->getConditionString( $types );
	}


	public function testGetConditionStringInvalidOperator()
	{
		$this->setExpectedException('MW_Common_Exception');
		$this->_object->setConditions( $this->_object->compare( '?', 'int_column', 10 ) );
	}


	public function testGetConditions()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Compare_SQL', $this->_object->getConditions() );

		$conditions = $this->_object->compare( '==', 'int_column', 10 );
		$this->_object->setConditions( $conditions );
		$this->assertEquals( $conditions, $this->_object->getConditions() );
	}


	public function testGetSortationString()
	{
		$types = array( 'asc_column' => MW_DB_Statement_Abstract::PARAM_INT, 'desc_column' => MW_DB_Statement_Abstract::PARAM_STR );
		$translations = array( 'asc_column' => 'asc_int_col', 'desc_column' => 'desc_str_col' );

		$sortations = array();
		$sortations[] = $this->_object->sort( '+', 'asc_column' );
		$sortations[] = $this->_object->sort( '-', 'desc_column' );
		$this->_object->setSortations( $sortations );
		$this->assertEquals( 'asc_int_col ASC, desc_str_col DESC', $this->_object->getSortationString( $types, $translations ) );
	}


	public function testGetSortationStringInvalidName()
	{
		$types = array( 'asc_column' => MW_DB_Statement_Abstract::PARAM_INT );
		$translations = array( 'asc_column' => 'asc_int_col' );

		$this->_object->setSortations( array( $this->_object->sort( '+', 'asc_col' ) ) );
		$this->setExpectedException('MW_Common_Exception');
		$this->_object->getSortationString( $types, $translations );
	}


	public function testGetSortationStringInvalidDirection()
	{
		$this->setExpectedException('MW_Common_Exception');
		$this->_object->setSortations( array( $this->_object->sort( '/', 'asc_column' ) ) );
	}


	public function testGetSortationNoSortation()
	{
		$types = array( 'asc_column' => MW_DB_Statement_Abstract::PARAM_INT, 'desc_column' => MW_DB_Statement_Abstract::PARAM_STR );

		$this->assertEquals('asc_column ASC', $this->_object->getSortationString( $types ) );

		$translations = array( 'asc_column' => 'asc_int_col', 'desc_column' => 'desc_str_col' );
		$this->assertEquals('asc_int_col ASC', $this->_object->getSortationString( $types, $translations ));
	}


	public function testGetSortations()
	{
		$this->assertEquals( array(), $this->_object->getSortations() );

		$sortations = array( $this->_object->sort( '+', 'asc_column' ) );
		$this->_object->setSortations( $sortations );
		$this->assertEquals( $sortations, $this->_object->getSortations() );
	}


	public function testSlice()
	{
		$this->assertEquals( 0, $this->_object->getSliceStart() );
		$this->assertEquals( 100, $this->_object->getSliceSize() );

		$this->_object->setSlice( 10, 20 );

		$this->assertEquals( 10, $this->_object->getSliceStart() );
		$this->assertEquals( 20, $this->_object->getSliceSize() );
	}


	public function testToConditionsEmptyArray()
	{
		$condition = $this->_object->toConditions( array() );
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Compare_SQL', $condition );
	}


	public function testToConditionsInvalidOperator()
	{
		$this->setExpectedException( 'MW_Common_Exception' );
		$this->_object->toConditions( array( '><' => array( 'name', 'value' ) ) );
	}


	public function testToConditionsCompare()
	{
		$array = array(
			'==' => array( 'name' => 'value' ),
		);

		$condition = $this->_object->toConditions( $array );

		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Compare_Interface', $condition );
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

		$condition = $this->_object->toConditions( $array );
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Combine_Interface', $condition );
		$this->assertEquals( '&&', $condition->getOperator() );
		$this->assertEquals( 2, count( $condition->getExpressions() ) );

		foreach( $condition->getExpressions() as $expr )
		{
			$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Compare_Interface', $expr );
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

		$sortations = $this->_object->toSortations( $array );
		$this->assertEquals( 2, count( $sortations ) );

		foreach( $sortations as $sort ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Sort_Interface', $sort );
		}

		$this->assertEquals( '+', $sortations[0]->getOperator() );
		$this->assertEquals( 'name1', $sortations[0]->getName() );
		$this->assertEquals( '-', $sortations[1]->getOperator() );
		$this->assertEquals( 'name2', $sortations[1]->getName() );
	}
}
