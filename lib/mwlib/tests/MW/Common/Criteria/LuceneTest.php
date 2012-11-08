<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Test plugin class
 */
class Criteria_Plugin_LuceneTest implements MW_Common_Criteria_Plugin_Interface
{
	public function translate( $value )
	{
		switch( $value )
		{
			case 'a': return 10;
			default: return $value;
		}
	}
}


/**
 * Test class for MW_Common_Criteria_Lucene.
 */
class MW_Common_Criteria_LuceneTest extends MW_Unittest_Testcase
{
	protected $_object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new MW_Common_Criteria_Lucene();
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
			'compare' => array( '==', '!=', '~=', '>=', '<=', '>', '<' ),
			'sort' => array( '+', '-' ),
		);
		$actual = $this->_object->getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testCombine()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Combine_Lucene', $this->_object->combine( '||', array() ) );
	}

	public function testCompare()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Compare_Lucene', $this->_object->compare( '!=', 'name', 'value' ) );
	}

	public function testGetConditionString()
	{
		$types = array( 'int_column' => SORT_NUMERIC, 'str_column' => SORT_STRING );
		$translations = array( 'int_column' => 'int_col', 'str_column' => 'str_col' );
		$plugins = array( 'int_column' => new Criteria_Plugin_SQLTest() );

		$this->assertInstanceOf( 'Zend_Search_Lucene_Search_Query', $this->_object->getConditionString( $types, $translations ) );

		$expr = array( $this->_object->compare( '==', 'int_column', 'a' ), $this->_object->compare( '==', 'str_column', 'test' ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$actual = $this->_object->getConditionString( $types, $translations, $plugins )->__toString();
		$this->assertEquals( "+(int_col:10) +(str_col:test)", $actual );

		$expr = array( $this->_object->compare( '==', 'int_column', array( 1, 2, 4, 8 ) ), $this->_object->compare( '==', 'str_column', 'test' ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$actual = $this->_object->getConditionString( $types, $translations )->__toString();
		$this->assertEquals( "+(int_col:1 int_col:2 int_col:4 int_col:8) +(str_col:test)", $actual );

		$expr = array( $this->_object->compare( '==', 'int_column', 1 ), $this->_object->compare( '~=', 'str_column', array( 'ta1', 'ta2', 'ta3' ) ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$actual = $this->_object->getConditionString( $types, $translations )->__toString();
		$this->assertEquals( "+(int_col:1) +((str_col:ta1*) (str_col:ta2*) (str_col:ta3*))", $actual );

		$expr = array( $this->_object->compare( '==', 'int_column', 1 ), $this->_object->compare( '!=', 'int_column', 2 ) );
		$this->_object->setConditions( $this->_object->combine( '!', array( $this->_object->combine( '&&', $expr ) ) ) );
		$actual = $this->_object->getConditionString( $types, $translations )->__toString();
		$this->assertEquals( "-(+(int_col:1) +(-int_col:2))", $actual );

		$expr = array( $this->_object->compare( '==', 'int_column', null ), $this->_object->compare( '!=', 'str_column', null ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$actual = $this->_object->getConditionString( $types, $translations )->__toString();
		$this->assertEquals( "+(int_col:(empty)) +(-str_col:(empty))", $actual );

		$expr = array( $this->_object->compare( '==', 'int_column', 1 ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$actual = $this->_object->getConditionString( $types, $translations )->__toString();
		$this->assertEquals( "+(int_col:1)", $actual );

		$expr = array( $this->_object->compare( '==', 'str_column', 'test' ) );
		$expr = array( $this->_object->compare( '==', 'int_column', 1 ), $this->_object->combine( '&&', $expr ) );
		$this->_object->setConditions( $this->_object->combine( '&&', $expr ) );
		$actual = $this->_object->getConditionString( $types, $translations )->__toString();
		$this->assertEquals( "+(int_col:1) +(+(str_col:test))", $actual );

		$types = array( 'column' => SORT_NUMERIC );
		$this->_object->setConditions( $this->_object->compare( '==', 'column', 1 ) );
		$actual = $this->_object->getConditionString( $types )->__toString();
		$this->assertEquals( "column:1", $actual );
	}


	public function testGetConditionStringInvalidName()
	{
		$types = array( 'int_column' => SORT_NUMERIC );

		$this->_object->setConditions( $this->_object->compare( '==', 'icol', 10 ) );
		$this->setExpectedException('MW_Common_Exception');
		$this->_object->getConditionString( $types );
	}


	public function testGetConditionStringInvalidOperator()
	{
		$types = array( 'int_column' => SORT_NUMERIC );

		$this->setExpectedException('MW_Common_Exception');
		$this->_object->setConditions( $this->_object->compare( '?', 'int_column', 10 ) );
	}


	public function testGetConditions()
	{
		$this->assertEquals( null, $this->_object->getConditions() );

		$conditions = $this->_object->compare( '==', 'int_column', 10 );
		$this->_object->setConditions( $conditions );
		$this->assertEquals( $conditions, $this->_object->getConditions() );
	}

	public function testGetSortationString()
	{
		$types = array( 'asc_column' => SORT_NUMERIC, 'desc_column' => SORT_STRING );
		$translations = array( 'asc_column' => 'asc_int_col', 'desc_column' => 'desc_str_col' );

		$sortations = array();
		$sortations[] = $this->_object->sort( '+', 'asc_column' );
		$sortations[] = $this->_object->sort( '-', 'desc_column' );
		$this->_object->setSortations( $sortations );

		$actual = $this->_object->getSortationString( $types, $translations );
		$this->assertEquals( ', "asc_int_col",1,4, "desc_str_col",2,3', $actual );
	}

	public function testGetSortationStringInvalidName()
	{
		$types = array( 'asc_column' => SORT_NUMERIC );
		$translations = array( 'asc_column' => 'asc_int_col' );

		$this->_object->setSortations( array( $this->_object->sort( '+', 'asc_col' ) ) );
		$this->setExpectedException('MW_Common_Exception');
		$this->_object->getSortationString( $types, $translations );
	}

	public function testGetSortationStringInvalidDirection()
	{
		$types = array( 'asc_column' => SORT_NUMERIC );
		$translations = array( 'asc_column' => 'asc_int_col' );

		$this->setExpectedException('MW_Common_Exception');
		$this->_object->setSortations( array( $this->_object->sort( '/', 'asc_column' ) ) );
	}

	/**
	 * test a feew lines of code regarding sortation. fallback tests for proper work!
	 */
	public function testGetSortationNoSortation()
	{
		$types = array( 'asc_column' => SORT_NUMERIC, 'desc_column' => SORT_STRING );

		$this->assertEquals('', $this->_object->getSortationString( $types ) );

		$translations = array( 'asc_column' => 'asc_int_col', 'desc_column' => 'desc_str_col' );
		$this->assertEquals('', $this->_object->getSortationString( $types, $translations ));
	}


	public function testSortations()
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
}
