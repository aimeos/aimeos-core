<?php

/**
 * Test class for MW_Common_Criteria_Expression_Compare_SQL.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Common_Criteria_Expression_Compare_SQLTest extends MW_Unittest_Testcase
{
	private $_conn = null;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( MW_TestHelper::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$dbm = MW_TestHelper::getDBManager();
		$this->_conn = $dbm->acquire();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$dbm = MW_TestHelper::getDBManager();
		$dbm->release( $this->_conn );
	}


	public function testCreateFunction()
	{
		$func = MW_Common_Criteria_Expression_Compare_Abstract::createFunction( 'test', array( true, 1, 0.1, 'string', array( 2, 3 ) ) );
		$this->assertEquals( 'test(1,1,0.1,"string",[2,3])', $func );
	}


	public function testGetOperators()
	{
		$expected = array( '==', '!=', '~=', '>=', '<=', '>', '<', '&', '|', '=~');
		$actual = MW_Common_Criteria_Expression_Compare_SQL::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '==', 'name', 'value' );
		$this->assertEquals( '==', $expr->getOperator() );
	}

	public function testGetName()
	{
		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '==', 'name', 'value' );
		$this->assertEquals( 'name', $expr->getName() );
	}

	public function testGetValue()
	{
		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '==', 'name', 'value' );
		$this->assertEquals( 'value', $expr->getValue() );
	}

	public function testToString()
	{
		$types = array(
			'list' => MW_DB_Statement_Abstract::PARAM_STR,
			'string' => MW_DB_Statement_Abstract::PARAM_STR,
			'float' => MW_DB_Statement_Abstract::PARAM_FLOAT,
			'int' => MW_DB_Statement_Abstract::PARAM_INT,
			'undefined' => MW_DB_Statement_Abstract::PARAM_INT,
			'bool' => MW_DB_Statement_Abstract::PARAM_BOOL,
		);

		$translations = array(
			'list' => 't.list',
			'string' => 't.string',
			'float' => 't.float',
			'int' => 't.int',
			'undefined' => 't.undefined',
			'bool' => 't.bool',
		);

		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '==', 'list', array('a', 'b', 'c') );
		$this->assertEquals( "t.list IN ('a','b','c')", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '!=', 'list', array('a', 'b', 'c') );
		$this->assertEquals( "t.list NOT IN ('a','b','c')", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '~=', 'string', 'value' );
		$this->assertEquals( "t.string LIKE '%value%'", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '<', 'float', 0.1 );
		$this->assertEquals( "t.float < 0.1", $expr->toString( $types, $translations ) );

		$expr= new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '>', 'int', 10 );
		$this->assertEquals( "t.int > 10", $expr->toString( $types, $translations ) );

		$expr= new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '!=', 'undefined', null );
		$this->assertEquals( "t.undefined IS NOT NULL", $expr->toString( $types, $translations ) );

		$expr= new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '==', 'bool', true );
		$this->assertEquals( "t.bool = 1", $expr->toString( $types, $translations ) );

		$expr= new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '&', 'int', 0x2 );
		$this->assertEquals( "t.int & 2", $expr->toString( $types, $translations ) );

		$expr= new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '|', 'int', 0x4 );
		$this->assertEquals( "t.int | 4", $expr->toString( $types, $translations ) );
	}

	public function testToStringFunction()
	{
		$types = array(
			'pcounter()' => MW_DB_Statement_Abstract::PARAM_INT,
			'strconcat()' => MW_DB_Statement_Abstract::PARAM_STR,
			'lcounter()' => MW_DB_Statement_Abstract::PARAM_INT,
		);

		$translations = array(
			'pcounter()' => 'count($1,$2,$3)',
			'strconcat()' => 'concat($1,$2)',
			'lcounter()' => 'count(name IN ($1))',
		);

		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '==', 'pcounter("name",10,0.1)', 3 );
		$this->assertEquals( "count('name',10,0.1) = 3", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '~=', 'strconcat("hello","world")', 'low' );
		$this->assertEquals( "concat('hello','world') LIKE '%low%'", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '==', 'lcounter(["a","b","c","\'d"])', 4 );
		$this->assertEquals( "count(name IN ('a','b','c','''d')) = 4", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_SQL( $this->_conn, '==', 'lcounter([])', 0 );
		$this->assertEquals( "count(name IN ()) = 0", $expr->toString( $types, $translations ) );
	}
}
