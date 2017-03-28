<?php

namespace Aimeos\MW\Criteria\Expression\Combine;


/**
 * Test class for \Aimeos\MW\Criteria\Expression\Combine\PHP.
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
		$expected = array( '&&', '||', '!' );
		$actual = \Aimeos\MW\Criteria\Expression\Combine\PHP::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Combine\PHP( '||', [] );
		$this->assertEquals( '||', $expr->getOperator() );
	}

	public function testGetExpressions()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Combine\PHP( '||', [] );
		$this->assertEquals( [], $expr->getExpressions() );
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

		$expr1 = [];
		$expr1[] = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'listitem', array('a', 'b', 'c') );
		$expr1[] = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'stringvar', 'value' );


		$expr2 = [];
		$expr2[] = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '<', 'floatvar', 0.1 );
		$expr2[] = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '>', 'intvar', 10 );

		$objects = [];
		$objects[] = new \Aimeos\MW\Criteria\Expression\Combine\PHP( '&&', $expr1 );
		$objects[] = new \Aimeos\MW\Criteria\Expression\Combine\PHP( '&&', $expr2 );

		$object = new \Aimeos\MW\Criteria\Expression\Combine\PHP( '||', $objects );
		$test = new \Aimeos\MW\Criteria\Expression\Combine\PHP( '!', array( $object ) );

		$expected = " ! ( ( ( \$listitem == 'a' || \$listitem == 'b' || \$listitem == 'c' ) && \$stringvar == 'value' ) || ( \$floatvar < 0.1 && \$intvar > 10 ) )";
		$this->assertEquals( $expected, $test->toString( $types, $translations ) );
	}
}
