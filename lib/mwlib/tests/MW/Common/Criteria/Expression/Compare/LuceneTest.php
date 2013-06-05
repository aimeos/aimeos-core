<?php

/**
 * Test class for MW_Common_Criteria_Expression_Compare_Lucene.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Common_Criteria_Expression_Compare_LuceneTest extends MW_Unittest_Testcase
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


	public function testCreateFunction()
	{
		$func = MW_Common_Criteria_Expression_Compare_Abstract::createFunction( 'test', array( true, 1, 0.1, 'string', array( 2, 3 ) ) );
		$this->assertEquals( 'test(1,1,0.1,"string",[2,3])', $func );
	}


	public function testGetOperators()
	{
		$expected = array( '==', '!=', '~=', '>=', '<=', '>', '<' );
		$actual = MW_Common_Criteria_Expression_Compare_Lucene::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new MW_Common_Criteria_Expression_Compare_Lucene( '==', 'name', 'value' );
		$this->assertEquals( '==', $expr->getOperator() );
	}

	public function testGetName()
	{
		$expr = new MW_Common_Criteria_Expression_Compare_Lucene( '==', 'name', 'value' );
		$this->assertEquals( 'name', $expr->getName() );
	}

	public function testGetValue()
	{
		$expr = new MW_Common_Criteria_Expression_Compare_Lucene( '==', 'name', 'value' );
		$this->assertEquals( 'value', $expr->getValue() );
	}

	public function testToString()
	{
		if( !class_exists( 'Zend_Search_Lucene_Search_Query_MultiTerm' ) ) {
			$this->markTestSkipped( 'Zend_Search_Lucene_Search_Query_MultiTerm is not available' );
		}

		$types = array(
			'list' => SORT_STRING,
			'string' => SORT_STRING,
			'float' => SORT_NUMERIC,
			'int' => SORT_NUMERIC,
			'undefined' => SORT_NUMERIC,
			'bool' => SORT_NUMERIC,
			'test()' => SORT_NUMERIC,
			'test2()' => SORT_STRING,
		);

		$translations = array(
			'list' => 't.list',
			'string' => 't.string',
			'float' => 't.float',
			'int' => 't.int',
			'undefined' => 't.undefined',
			'bool' => 't.bool',
			'test()' => 'abc($1,$2)',
			'test2()' => 'cdf($1,$2,$3)',
		);

		$expr = new MW_Common_Criteria_Expression_Compare_Lucene( '==', 'list', array('a', 'b', 'c') );
		$this->assertEquals( "t.list:a t.list:b t.list:c", $expr->toString( $types, $translations )->__toString() );

		$expr = new MW_Common_Criteria_Expression_Compare_Lucene( '!=', 'list', array('a', 'b', 'c') );
		$this->assertEquals( "-t.list:a -t.list:b -t.list:c", $expr->toString( $types, $translations )->__toString() );

		$expr = new MW_Common_Criteria_Expression_Compare_Lucene( '==', 'test("123","345")', array(456, 789) );
		$this->assertEquals( "abc(123,345):456 abc(123,345):789", $expr->toString( $types, $translations )->__toString() );

		$expr = new MW_Common_Criteria_Expression_Compare_Lucene( '!=', 'test(123,345)', array(456, 789) );
		$this->assertEquals( "-abc(123,345):456 -abc(123,345):789", $expr->toString( $types, $translations )->__toString() );

		$expr = new MW_Common_Criteria_Expression_Compare_Lucene( '==', 'test(12.3,4)', 4.56);
		$this->assertEquals( "abc(12.3,4):4.56", $expr->toString( $types, $translations )->__toString() );

		$expr = new MW_Common_Criteria_Expression_Compare_Lucene( '~=', 'string', 'value' );
		$this->assertEquals( "(t.string:value*)", $expr->toString( $types, $translations )->__toString() );

		$expr = new MW_Common_Criteria_Expression_Compare_Lucene( '<', 'float', 0.1 );
		$this->assertEquals( "t.float:{null TO 0.1}", $expr->toString( $types, $translations )->__toString() );

		$expr= new MW_Common_Criteria_Expression_Compare_Lucene( '>', 'int', 10 );
		$this->assertEquals( "t.int:{10 TO null}", $expr->toString( $types, $translations )->__toString() );

		$expr= new MW_Common_Criteria_Expression_Compare_Lucene( '!=', 'undefined', null );
		$this->assertEquals( "-t.undefined:(empty)", $expr->toString( $types, $translations )->__toString() );

		$expr= new MW_Common_Criteria_Expression_Compare_Lucene( '==', 'bool', true );
		$this->assertEquals( "t.bool:1", $expr->toString( $types, $translations )->__toString() );
	}
}
