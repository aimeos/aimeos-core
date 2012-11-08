<?php

/**
 * Test class for MW_Common_Criteria_Expression_Sort_Lucene.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Common_Criteria_Expression_Sort_LuceneTest extends MW_Unittest_Testcase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
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
		$expected = array( '+', '-' );
		$actual = MW_Common_Criteria_Expression_Sort_Lucene::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new MW_Common_Criteria_Expression_Sort_Lucene( '+', 'test' );
		$this->assertEquals( '+', $expr->getOperator() );
	}

	public function testGetName()
	{
		$expr = new MW_Common_Criteria_Expression_Sort_Lucene( '-', 'test' );
		$this->assertEquals( 'test', $expr->getName() );
	}

	public function testToString()
	{
		$types = array(
			'test' => SORT_STRING,
		);

		$types = array(
			'test' => SORT_STRING,
			'test()' => SORT_NUMERIC,
		);

		$translations = array(
			'test()' => 'abc($1,$2)',
		);

		$object = new MW_Common_Criteria_Expression_Sort_Lucene( '-', 'test' );
		$this->assertEquals( ', "test",2,3', $object->toString( $types ) );

		$object = new MW_Common_Criteria_Expression_Sort_Lucene( '+', 'test("123", 1.23)' );
		$this->assertEquals( ', "abc(123,1.23)",1,4', $object->toString( $types, $translations ) );
	}
}
