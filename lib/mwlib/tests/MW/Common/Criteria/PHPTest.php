<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Test plugin class
 */
class Criteria_Plugin_PHPTest implements MW_Common_Criteria_Plugin_Iface
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
 * Test class for MW_Common_Criteria_PHP.
 */
class MW_Common_Criteria_PHPTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new MW_Common_Criteria_PHP();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCombine()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Combine_PHP', $this->object->combine( '||', array() ) );
	}


	public function testCompare()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Compare_PHP', $this->object->compare( '!=', 'name', 'value' ) );
	}


	public function testSort()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Sort_PHP', $this->object->sort( '+', 'name' ) );
	}


	public function testGetOperators()
	{
		$expected = array(
			'combine' => array( '&&', '||', '!' ),
			'compare' => array( '==', '!=', '>=', '<=', '>', '<' ),
			'sort' => array( '+', '-' ),
		);
		$actual = $this->object->getOperators();
		$this->assertEquals( $expected, $actual );
	}


	public function testGetConditionString()
	{
		$intval = 1;
		$strval = 'test';

		$types = array( 'int_value' => 'int', 'str_value' => 'string' );
		$translations = array( 'int_value' => '$intval', 'str_value' => '$strval' );
		$plugins = array( 'int_value' => new Criteria_Plugin_PHPTest() );

		$result = $this->object->getConditionString( $types, $translations );
		$this->assertEquals( "1 == 1", $result );
		$this->assertEquals( true, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'int_value', 'a' ), $this->object->compare( '==', 'str_value', 'test' ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$result = $this->object->getConditionString( $types, $translations, $plugins );
		$this->assertEquals( "( \$intval == 10 && \$strval == 'test' )", $result );
		$this->assertEquals( false, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'int_value', array( 1, 2, 4, 8 ) ), $this->object->compare( '==', 'str_value', 'test' ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$result = $this->object->getConditionString( $types, $translations );
		$this->assertEquals( "( ( \$intval == 1 || \$intval == 2 || \$intval == 4 || \$intval == 8 ) && \$strval == 'test' )", $result );
		$this->assertEquals( true, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'int_value', 1 ), $this->object->compare( '!=', 'int_value', 2 ) );
		$this->object->setConditions( $this->object->combine( '!', array( $this->object->combine( '&&', $expr ) ) ) );
		$result = $this->object->getConditionString( $types, $translations );
		$this->assertEquals( " ! ( \$intval == 1 && \$intval != 2 )", $result );
		$this->assertEquals( false, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'int_value', null ), $this->object->compare( '!=', 'str_value', null ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$result = $this->object->getConditionString( $types, $translations );
		$this->assertEquals( "( \$intval === null && \$strval !== null )", $result );
		$this->assertEquals( false, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'int_value', 1 ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$result = $this->object->getConditionString( $types, $translations );
		$this->assertEquals( "( \$intval == 1 )", $result );
		$this->assertEquals( true, eval( 'return ' . $result . ';' ) );

		$expr = array( $this->object->compare( '==', 'str_value', 'test' ) );
		$expr = array( $this->object->compare( '==', 'int_value', 1 ), $this->object->combine( '&&', $expr ) );
		$this->object->setConditions( $this->object->combine( '&&', $expr ) );
		$result = $this->object->getConditionString( $types, $translations );
		$this->assertEquals( "( \$intval == 1 && ( \$strval == 'test' ) )", $result );
		$this->assertEquals( true, eval( 'return ' . $result . ';' ) );

		$types = array( 'column' => 'bool' );
		$this->object->setConditions( $this->object->compare( '==', 'column', 1 ) );
		$this->assertEquals( "column == 1", $this->object->getConditionString( $types ) );
	}


	public function testGetConditionStringInvalidOperatorForNull()
	{
		// test exception in _createTerm:  'NULL value not allowed for operator'
		$types = array( 'str_value' => 'string' );

		$this->object->setConditions( $this->object->compare( '>', 'str_value', null ) );

		$this->setExpectedException('MW_Common_Exception');
		$this->object->getConditionString( $types );
	}


	public function testGetConditionStringInvalidName()
	{
		$types = array( 'int_value' => MW_DB_Statement_Base::PARAM_INT );

		$this->object->setConditions( $this->object->compare( '==', 'ival', 10 ) );
		$this->setExpectedException('MW_Common_Exception');
		$this->object->getConditionString( $types );
	}


	public function testGetConditionStringInvalidOperator()
	{
		$this->setExpectedException('MW_Common_Exception');
		$this->object->setConditions( $this->object->compare( '?', 'int_value', 10 ) );
	}


	public function testGetConditions()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Compare_PHP', $this->object->getConditions() );

		$conditions = $this->object->compare( '==', 'int_value', 10 );
		$this->object->setConditions( $conditions );
		$this->assertEquals( $conditions, $this->object->getConditions() );
	}


	public function testGetSortationString()
	{
		$ascIntList = array( 1, 9, 5 );
		$descStrList = array( 'abc', 'xyz', 'mno' );

		$types = array( 'asc_array' => 'int', 'desc_array' => 'string' );
		$translations = array( 'asc_array' => '$ascIntList', 'desc_array' => '$descStrList' );

		$sortations = array();
		$sortations[] = $this->object->sort( '+', 'asc_array' );
		$sortations[] = $this->object->sort( '-', 'desc_array' );
		$this->object->setSortations( $sortations );

		$result = $this->object->getSortationString( $types, $translations );
		$this->assertEquals( 'asort($ascIntList); arsort($descStrList);', $result );
		$this->assertEquals( array( 0 => 1, 2 => 5, 1 => 9 ), $ascIntList );
		$this->assertEquals( array( 1 => 'xyz', 2 => 'mno', 0 => 'abc' ), $descStrList );
	}


	public function testGetSortationStringInvalidName()
	{
		$types = array( 'asc_array' => 'int' );
		$translations = array( 'asc_array' => 'asc_int_list' );

		$this->object->setSortations( array( $this->object->sort( '+', 'asc_col' ) ) );
		$this->setExpectedException('MW_Common_Exception');
		$this->object->getSortationString( $types, $translations );
	}


	public function testGetSortationStringInvalidDirection()
	{
		$this->setExpectedException('MW_Common_Exception');
		$this->object->setSortations( array( $this->object->sort( '/', 'asc_array' ) ) );
	}


	public function testGetSortationNoSortation()
	{
		$ascIntList = array( 1, 9, 5 );

		$types = array( 'asc_array' => 'int', 'desc_array' => 'string' );
		$translations = array( 'asc_array' => '$ascIntList', 'desc_array' => '$descStrList' );

		$result = $this->object->getSortationString( $types, $translations );
		$this->assertEquals('asort($ascIntList);', $result);
		$this->assertEquals( array( 0 => 1, 2 => 5, 1 => 9 ), $ascIntList );
	}


	public function testGetSortations()
	{
		$this->assertEquals( array(), $this->object->getSortations() );

		$sortations = array( $this->object->sort( '+', 'asc_array' ) );
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
}
