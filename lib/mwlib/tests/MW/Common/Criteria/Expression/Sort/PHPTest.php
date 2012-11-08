<?php

/**
 * Test class for MW_Common_Criteria_Expression_Sort_PHP.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Common_Criteria_Expression_Sort_PHPTest extends MW_Unittest_Testcase
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
		$actual = MW_Common_Criteria_Expression_Sort_PHP::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new MW_Common_Criteria_Expression_Sort_PHP( '+', 'test' );
		$this->assertEquals( '+', $expr->getOperator() );
	}

	public function testGetName()
	{
		$expr = new MW_Common_Criteria_Expression_Sort_PHP( '-', 'test' );
		$this->assertEquals( 'test', $expr->getName() );
	}

	public function testToString()
	{
		$types = array(
			'test' => 'array',
			'test()' => MW_DB_Statement_Abstract::PARAM_STR,
		);
		$translations = array(
			'test()' => 'testfunc($1,$2)',
		);

		$object = new MW_Common_Criteria_Expression_Sort_PHP( '-', 'test' );
		$this->assertEquals( 'arsort(test);', $object->toString( $types ) );

		$object = new MW_Common_Criteria_Expression_Sort_PHP( '+', 'test' );
		$this->assertEquals( 'asort(test);', $object->toString( $types ) );

		$object = new MW_Common_Criteria_Expression_Sort_PHP( '+', 'test(1,2)' );
		$this->assertEquals( 'asort(testfunc(1,2));', $object->toString( $types, $translations ) );

		$object = new MW_Common_Criteria_Expression_Sort_PHP( '+', 'test(1.2,2.1)' );
		$this->assertEquals( 'asort(testfunc(1.2,2.1));', $object->toString( $types, $translations ) );

		$object = new MW_Common_Criteria_Expression_Sort_PHP( '+', 'test("1","2")' );
		$this->assertEquals( 'asort(testfunc(1,2));', $object->toString( $types, $translations ) );


	}

	public function testException1()
	{
		$this->setExpectedException('MW_Common_Exception');
		$object = new MW_Common_Criteria_Expression_Sort_PHP( '/', 'test(1,2)' );
	}

	public function testException2()
	{
		$types = array(
			'test' => 'array',
			'test()' => MW_DB_Statement_Abstract::PARAM_STR,
		);

		$translations = array(
			'test()' => 'testfunc($1,$2)',
		);

		$object = new MW_Common_Criteria_Expression_Sort_PHP( '+', 'wrongType' );

		$this->setExpectedException('MW_Common_Exception');
		$object->toString( $types, $translations );
	}

}