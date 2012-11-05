<?php

/**
 * Test class for MW_Common_Criteria_Expression_Sort_SQL.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Common_Criteria_Expression_Sort_SQLTest extends MW_Unittest_Testcase
{
	protected $_conn = null;


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

	public function testGetOperators()
	{
		$expected = array( '+', '-' );
		$actual = MW_Common_Criteria_Expression_Sort_SQL::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new MW_Common_Criteria_Expression_Sort_SQL( $this->_conn, '+', 'test' );
		$this->assertEquals( '+', $expr->getOperator() );
	}

	public function testGetName()
	{
		$expr = new MW_Common_Criteria_Expression_Sort_SQL( $this->_conn, '-', 'test' );
		$this->assertEquals( 'test', $expr->getName() );
	}

	public function testToString()
	{
		$types = array(
			'test' => MW_DB_Statement_Abstract::PARAM_STR,
			'test()' => MW_DB_Statement_Abstract::PARAM_STR,
		);

		$translations = array(
			'test()' => 'testfunc($1,$2)',
		);

		$object = new MW_Common_Criteria_Expression_Sort_SQL( $this->_conn, '-', 'test' );
		$this->assertEquals( 'test DESC', $object->toString( $types ) );

		$object = new MW_Common_Criteria_Expression_Sort_SQL( $this->_conn, '+', 'test(1,2.1)' );
		$this->assertEquals( 'testfunc(1,2.1) ASC', $object->toString( $types, $translations ) );

		$object = new MW_Common_Criteria_Expression_Sort_SQL( $this->_conn, '-', 'test("a",2)' );
		$this->assertEquals( 'testfunc(\'a\',2) DESC', $object->toString( $types, $translations ) );
	}
}
