<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Criteria\Expression\Compare;


class PHPTest extends \PHPUnit\Framework\TestCase
{
	public function testGetOperators()
	{
		$expected = ['>', '>=', '<', '<=', '==', '!=', '-'];
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


	public function testToSource()
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

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'listitem', array( 'a', 'b', 'c' ) );
		$this->assertEquals( "( \$listitem == 'a' || \$listitem == 'b' || \$listitem == 'c' )", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '!=', 'listitem', array( 'a', 'b' ) );
		$this->assertEquals( "( \$listitem != 'a' && \$listitem != 'b' )", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'stringvar', 'value' );
		$this->assertEquals( "\$stringvar == 'value'", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '<', 'floatvar', 0.1 );
		$this->assertEquals( '$floatvar < 0.1', $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '>', 'intvar', 10 );
		$this->assertEquals( '$intvar > 10', $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'boolvar', true );
		$this->assertEquals( '$boolvar == 1', $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '!=', 'undefined', null );
		$this->assertEquals( '$undefined !== null', $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'undefined', null );
		$this->assertEquals( '$undefined === null', $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '-', 'intvar', '10 - 20' );
		$this->assertEquals( '$intvar >= 10 && $intvar < 20', $expr->toSource( $types, $translations ) );
	}


	public function testToSourceExcept1()
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

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '>=', 'listitem', array( 'a', 'b' ) );
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$expr->toSource( $types, $translations );
	}


	public function testToSourceExcept2()
	{
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		new \Aimeos\MW\Criteria\Expression\Compare\PHP( '=', 'undefined', null );
	}


	public function testToSourceFunction()
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
		$this->assertEquals( "strlen('string') == 6", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'position("abcde", "c")', 2 );
		$this->assertEquals( "strpos('abcde','c') == 2", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'substring("hello world", 0, 5)', 'hello' );
		$this->assertEquals( "substr('hello world',0,5) == 'hello'", $expr->toSource( $types, $translations ) );
	}


	public function testToArray()
	{
		$expected = ['==' => ['stringvar' => 'value']];
		$object = new \Aimeos\MW\Criteria\Expression\Compare\PHP( '==', 'stringvar', 'value' );

		$this->assertEquals( $expected, $object->__toArray() );
	}
}
