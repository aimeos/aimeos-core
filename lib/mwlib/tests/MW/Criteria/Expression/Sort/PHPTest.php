<?php

namespace Aimeos\MW\Criteria\Expression\Sort;


/**
 * Test class for \Aimeos\MW\Criteria\Expression\Sort\PHP.
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
		$this->assertEquals( 'arsort(test);', $object->toString( $types ) );

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'test' );
		$this->assertEquals( 'asort(test);', $object->toString( $types ) );

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'test(1,2)' );
		$this->assertEquals( 'asort(testfunc(1,2));', $object->toString( $types, $translations ) );

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'test(1.2,2.1)' );
		$this->assertEquals( 'asort(testfunc(1.2,2.1));', $object->toString( $types, $translations ) );

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'test("1","2")' );
		$this->assertEquals( 'asort(testfunc(1,2));', $object->toString( $types, $translations ) );


	}

	public function testException1()
	{
		$this->setExpectedException('\\Aimeos\\MW\\Common\\Exception');
		new \Aimeos\MW\Criteria\Expression\Sort\PHP( '/', 'test(1,2)' );
	}

	public function testException2()
	{
		$types = array(
			'test' => 'array',
			'test()' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		);

		$translations = array(
			'test()' => 'testfunc($1,$2)',
		);

		$object = new \Aimeos\MW\Criteria\Expression\Sort\PHP( '+', 'wrongType' );

		$this->setExpectedException('\\Aimeos\\MW\\Common\\Exception');
		$object->toString( $types, $translations );
	}

}