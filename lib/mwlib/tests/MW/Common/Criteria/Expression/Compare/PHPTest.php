<?php

/**
 * Test class for MW_Common_Criteria_Expression_Compare_SQL.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Common_Criteria_Expression_Compare_PHPTest extends MW_Unittest_Testcase
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
		$expected = array( '==', '!=', '>=', '<=', '>', '<' );
		$actual = MW_Common_Criteria_Expression_Compare_PHP::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '==', 'name', 'value' );
		$this->assertEquals( '==', $expr->getOperator() );
	}

	public function testGetName()
	{
		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '==', 'name', 'value' );
		$this->assertEquals( 'name', $expr->getName() );
	}

	public function testGetValue()
	{
		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '==', 'name', 'value' );
		$this->assertEquals( 'value', $expr->getValue() );
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

		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '==', 'listitem', array('a', 'b', 'c') );
		$this->assertEquals( "( \$listitem == 'a' || \$listitem == 'b' || \$listitem == 'c' )", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '!=', 'listitem', array('a', 'b') );
		$this->assertEquals( "( \$listitem != 'a' && \$listitem != 'b' )", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '==', 'stringvar', 'value' );
		$this->assertEquals( "\$stringvar == 'value'", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '<', 'floatvar', 0.1 );
		$this->assertEquals( '$floatvar < 0.1', $expr->toString( $types, $translations ) );

		$expr= new MW_Common_Criteria_Expression_Compare_PHP( '>', 'intvar', 10 );
		$this->assertEquals( '$intvar > 10', $expr->toString( $types, $translations ) );

		$expr= new MW_Common_Criteria_Expression_Compare_PHP( '==', 'boolvar', true );
		$this->assertEquals( '$boolvar == 1', $expr->toString( $types, $translations ) );

		$expr= new MW_Common_Criteria_Expression_Compare_PHP( '!=', 'undefined', null );
		$this->assertEquals( '$undefined !== null', $expr->toString( $types, $translations ) );

		$expr= new MW_Common_Criteria_Expression_Compare_PHP( '==', 'undefined', null );
		$this->assertEquals( '$undefined === null', $expr->toString( $types, $translations ) );


	}

	public function testToStringExcept1()
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

		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '>=', 'listitem', array('a', 'b') );
		$this->setExpectedException('MW_Common_Exception');
		$expr->toString( $types, $translations );
	}

	public function testToStringExcept2()
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

		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '>=', 'undefined', null );
		$this->setExpectedException('MW_Common_Exception');
		$expr->toString( $types, $translations );
	}

	public function testToStringExcept3()
	{
		$this->setExpectedException('MW_Common_Exception');
		$obj = new MW_Common_Criteria_Expression_Compare_PHP('=', 'undefined', null);
	}

	public function testToStringFunction()
	{
		$types = array(
			'length()' => 'int',
			'position()' => 'int',
			'substring()' => 'string',
		);

		$translations = array(
			'length()'    => 'strlen($1)',
			'position()'  => 'strpos($1,$2)',
			'substring()' => 'substr($1,$2,$3)',
		);

		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '==', 'length("string")', 6 );
		$this->assertEquals( "strlen('string') == 6", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '==', 'position("abcde", "c")', 2 );
		$this->assertEquals( "strpos('abcde','c') == 2", $expr->toString( $types, $translations ) );

		$expr = new MW_Common_Criteria_Expression_Compare_PHP( '==', 'substring("hello world", 0, 5)', 'hello' );
		$this->assertEquals( "substr('hello world',0,5) == 'hello'", $expr->toString( $types, $translations ) );
	}

}

