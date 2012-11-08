<?php

/**
 * Test class for MW_Common_Criteria_Expression_Combine_SQL.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Common_Criteria_Expression_Combine_SQLTest extends MW_Unittest_Testcase
{
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
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}

	public function testGetOperators()
	{
		$expected = array( '&&', '||', '!' );
		$actual = MW_Common_Criteria_Expression_Combine_SQL::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new MW_Common_Criteria_Expression_Combine_SQL( '||', array() );
		$this->assertEquals( '||', $expr->getOperator() );
	}

	public function testGetExpressions()
	{
		$expr = new MW_Common_Criteria_Expression_Combine_SQL( '||', array() );
		$this->assertEquals( array(), $expr->getExpressions() );
	}

	public function testToString()
	{
		$dbm = MW_TestHelper::getDBManager();
		$conn = $dbm->acquire();
		$dbm->release( $conn );

		$types = array(
			'list' => MW_DB_Statement_Abstract::PARAM_STR,
			'string' => MW_DB_Statement_Abstract::PARAM_STR,
			'float' => MW_DB_Statement_Abstract::PARAM_FLOAT,
			'int' => MW_DB_Statement_Abstract::PARAM_INT,
			'undefined' => MW_DB_Statement_Abstract::PARAM_INT,
			'bool' => MW_DB_Statement_Abstract::PARAM_BOOL,
		);

		$expr1 = array();
		$expr1[] = new MW_Common_Criteria_Expression_Compare_SQL( $conn, '==', 'list', array('a', 'b', 'c') );
		$expr1[] = new MW_Common_Criteria_Expression_Compare_SQL( $conn, '~=', 'string', 'value' );

		$expr2 = array();
		$expr2[] = new MW_Common_Criteria_Expression_Compare_SQL( $conn, '<', 'float', 0.1 );
		$expr2[] = new MW_Common_Criteria_Expression_Compare_SQL( $conn, '>', 'int', 10 );

		$objects = array();
		$objects[] = new MW_Common_Criteria_Expression_Combine_SQL( '&&', $expr1 );
		$objects[] = new MW_Common_Criteria_Expression_Combine_SQL( '&&', $expr2 );

		$object = new MW_Common_Criteria_Expression_Combine_SQL( '||', $objects );
		$test = new MW_Common_Criteria_Expression_Combine_SQL( '!', array( $object ) );

		$expected = " NOT ( ( list IN ('a','b','c') AND string LIKE '%value%' ) OR ( float < 0.1 AND int > 10 ) )";
		$this->assertEquals( $expected, $test->toString( $types ) );

		$obj = new MW_Common_Criteria_Expression_Combine_SQL('&&', array());
		$this->assertEquals('', $obj->toString($types));

		$this->setExpectedException('MW_Common_Exception');
		$obj = new MW_Common_Criteria_Expression_Combine_SQL('', array());

	}
}
