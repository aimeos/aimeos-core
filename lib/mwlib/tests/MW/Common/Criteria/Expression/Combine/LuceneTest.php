<?php

/**
 * Test class for MW_Common_Criteria_Expression_Combine_Lucene.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Common_Criteria_Expression_Combine_LuceneTest extends MW_Unittest_Testcase
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
		$expected = array( '&&', '||', '!' );
		$actual = MW_Common_Criteria_Expression_Combine_Lucene::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new MW_Common_Criteria_Expression_Combine_Lucene( '||', array() );
		$this->assertEquals( '||', $expr->getOperator() );
	}

	public function testGetExpressions()
	{
		$expr = new MW_Common_Criteria_Expression_Combine_Lucene( '||', array() );
		$this->assertEquals( array(), $expr->getExpressions() );
	}

	public function testToString()
	{
		if( !class_exists( 'Zend_Search_Lucene_Search_Query_Boolean' ) ) {
			$this->markTestIncomplete( 'Zend_Search_Lucene_Search_Query_Boolean is not available' );
		}

		$types = array(
			'list' => SORT_STRING,
			'string' => SORT_STRING,
			'float' => SORT_NUMERIC,
			'int' => SORT_NUMERIC,
			'undefined' => SORT_NUMERIC,
			'bool' => SORT_NUMERIC,
		);

		$expr1 = array();
		$expr1[] = new MW_Common_Criteria_Expression_Compare_Lucene( '==', 'list', array('a', 'b', 'c') );
		$expr1[] = new MW_Common_Criteria_Expression_Compare_Lucene( '~=', 'string', 'value' );

		$expr2 = array();
		$expr2[] = new MW_Common_Criteria_Expression_Compare_Lucene( '<', 'float', 0.1 );
		$expr2[] = new MW_Common_Criteria_Expression_Compare_Lucene( '>', 'int', 10 );

		$objects = array();
		$objects[] = new MW_Common_Criteria_Expression_Combine_Lucene( '&&', $expr1 );
		$objects[] = new MW_Common_Criteria_Expression_Combine_Lucene( '&&', $expr2 );

		$object = new MW_Common_Criteria_Expression_Combine_Lucene( '||', $objects );
		$test = new MW_Common_Criteria_Expression_Combine_Lucene( '!', array( $object ) );

		$actual = $test->toString( $types );
		$expected = "-((+(list:a list:b list:c) +((string:value*))) (+(float:{null TO 0.1}) +(int:{10 TO null})))";

		$this->assertInstanceOf( 'Zend_Search_Lucene_Search_Query', $actual );
		$this->assertEquals( $expected, $actual->__toString() );
	}
}
