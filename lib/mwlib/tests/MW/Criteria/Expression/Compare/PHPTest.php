<?php

namespace Aimeos\MW\Criteria\Expression\Compare;


/**
 * Test class for \Aimeos\MW\Criteria\Expression\Compare\SQL.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class PHPTest extends \PHPUnit_Framework_TestCase
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
		$expected = array( '>', '>=', '<', '<=', '==', '!=' );
		$actual = \Aimeos\MW\Criteria\Expression\Compare\PHP::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'name', 'value' );
		$this->assertEquals( '==', $expr->getOperator() );
	}

	public function testGetName()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'name', 'value' );
		$this->assertEquals( 'name', $expr->getName() );
	}

	public function testGetValue()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'name', 'value' );
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

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'listitem', array('a', 'b', 'c') );
		$this->assertEquals( "( \$listitem == 'a' || \$listitem == 'b' || \$listitem == 'c' )", $expr->toString( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '!=', 'listitem', array('a', 'b') );
		$this->assertEquals( "( \$listitem != 'a' && \$listitem != 'b' )", $expr->toString( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'stringvar', 'value' );
		$this->assertEquals( "\$stringvar == 'value'", $expr->toString( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '<', 'floatvar', 0.1 );
		$this->assertEquals( '$floatvar < 0.1', $expr->toString( $types, $translations ) );

		$expr= new \Aimeos\MW\Criteria\Expression\Compare\PHP( '>', 'intvar', 10 );
		$this->assertEquals( '$intvar > 10', $expr->toString( $types, $translations ) );

		$expr= new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'boolvar', true );
		$this->assertEquals( '$boolvar == 1', $expr->toString( $types, $translations ) );

		$expr= new \Aimeos\MW\Criteria\Expression\Compare\PHP( '!=', 'undefined', null );
		$this->assertEquals( '$undefined !== null', $expr->toString( $types, $translations ) );

		$expr= new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'undefined', null );
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

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '>=', 'listitem', array('a', 'b') );
		$this->setExpectedException('\\Aimeos\\MW\\Common\\Exception');
		$expr->toString( $types, $translations );
	}

	public function testToStringExcept2()
	{
		$this->setExpectedException('\\Aimeos\\MW\\Common\\Exception');
		new \Aimeos\MW\Criteria\Expression\Compare\PHP('=', 'undefined', null);
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

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'length("string")', 6 );
		$this->assertEquals( "strlen('string') == 6", $expr->toString( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'position("abcde", "c")', 2 );
		$this->assertEquals( "strpos('abcde','c') == 2", $expr->toString( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'substring("hello world", 0, 5)', 'hello' );
		$this->assertEquals( "substr('hello world',0,5) == 'hello'", $expr->toString( $types, $translations ) );
	}

}

