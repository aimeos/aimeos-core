<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Criteria\Expression\Sort;


class PHPTest extends \PHPUnit\Framework\TestCase
{
	public function testGetOperators()
	{
		$expected = array( '+', '-' );
		$actual = \Aimeos\MW\Criteria\Expression\Sort\PHP::getOperators();
		$this->assertEquals( $expected, $actual );
	}


	public function testGetOperator()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'test' );
		$this->assertEquals( '+', $expr->getOperator() );
	}


	public function testGetName()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '-', 'test' );
		$this->assertEquals( 'test', $expr->getName() );
	}


	public function testToString()
	{
		$types = array(
			'test' => 'array',
			'test()' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		);
		$translations = array(
			'test()' => 'testfunc($1,$2)',
		);

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '-', 'test' );
		$this->assertEquals( 'arsort(test);', $object->toSource( $types ) );

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'test' );
		$this->assertEquals( 'asort(test);', $object->toSource( $types ) );

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'test(1,2)' );
		$this->assertEquals( 'asort(testfunc(1,2));', $object->toSource( $types, $translations ) );

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'test(1.2,2.1)' );
		$this->assertEquals( 'asort(testfunc(1.2,2.1));', $object->toSource( $types, $translations ) );

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'test("1","2")' );
		$this->assertEquals( 'asort(testfunc(1,2));', $object->toSource( $types, $translations ) );
	}


	public function testExceptionWrongOperator()
	{
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		new \Aimeos\MW\Criteria\Expression\Sort\PHP( '/', 'test(1,2)' );
	}


	public function testExceptionWrongType()
	{
		$types = array(
			'test' => 'array',
			'test()' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		);

		$translations = array(
			'test()' => 'testfunc($1,$2)',
		);

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'wrongType' );

		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$object->toSource( $types, $translations );
	}


	public function testToArray()
	{
		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'stringvar' );

		$this->assertEquals( ['stringvar' => '+'], $object->__toArray() );
	}
}
