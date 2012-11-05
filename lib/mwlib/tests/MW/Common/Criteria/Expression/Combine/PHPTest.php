<?php

/**
 * Test class for MW_Common_Criteria_Expression_Combine_PHP.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Common_Criteria_Expression_Combine_PHPTest extends MW_Unittest_Testcase
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
		$actual = MW_Common_Criteria_Expression_Combine_PHP::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new MW_Common_Criteria_Expression_Combine_PHP( '||', array() );
		$this->assertEquals( '||', $expr->getOperator() );
	}

	public function testGetExpressions()
	{
		$expr = new MW_Common_Criteria_Expression_Combine_PHP( '||', array() );
		$this->assertEquals( array(), $expr->getExpressions() );
	}

	public function testToString()
	{
		$types = array(
			'listitem' => 'string',
			'stringvar' => 'string',
			'floatvar' => 'float',
			'intvar' => 'int',
			'boolvar' => 'bool',
			'undefined' => 'int',
		);

		$translations = array(
			'listitem' => '$listitem',
			'stringvar' => '$stringvar',
			'floatvar' => '$floatvar',
			'intvar' => '$intvar',
			'boolvar' => '$boolvar',
			'undefined' => '$undefined',
		);

		$expr1 = array();
		$expr1[] = new MW_Common_Criteria_Expression_Compare_PHP( '==', 'listitem', array('a', 'b', 'c') );
		$expr1[] = new MW_Common_Criteria_Expression_Compare_PHP( '==', 'stringvar', 'value' );


		$expr2 = array();
		$expr2[] = new MW_Common_Criteria_Expression_Compare_PHP( '<', 'floatvar', 0.1 );
		$expr2[] = new MW_Common_Criteria_Expression_Compare_PHP( '>', 'intvar', 10 );

		$objects = array();
		$objects[] = new MW_Common_Criteria_Expression_Combine_PHP( '&&', $expr1 );
		$objects[] = new MW_Common_Criteria_Expression_Combine_PHP( '&&', $expr2 );

		$object = new MW_Common_Criteria_Expression_Combine_PHP( '||', $objects );
		$test = new MW_Common_Criteria_Expression_Combine_PHP( '!', array( $object ) );

		$expected = " ! ( ( ( \$listitem == 'a' || \$listitem == 'b' || \$listitem == 'c' ) && \$stringvar == 'value' ) || ( \$floatvar < 0.1 && \$intvar > 10 ) )";
		$this->assertEquals( $expected, $test->toString( $types, $translations ) );
	}
}
