<?php

namespace Aimeos\MW\Criteria\Expression\Sort;


/**
 * Test class for \Aimeos\MW\Criteria\Expression\Sort\SQL.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class SQLTest extends \PHPUnit_Framework_TestCase
{
	private $conn = null;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( \TestHelperMw::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$dbm = \TestHelperMw::getDBManager();
		$this->conn = $dbm->acquire();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$dbm = \TestHelperMw::getDBManager();
		$dbm->release( $this->conn );
	}

	public function testGetOperators()
	{
		$expected = array( '+', '-' );
		$actual = \Aimeos\MW\Criteria\Expression\Sort\SQL::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Sort\SQL( $this->conn, '+', 'test' );
		$this->assertEquals( '+', $expr->getOperator() );
	}

	public function testGetName()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Sort\SQL( $this->conn, '-', 'test' );
		$this->assertEquals( 'test', $expr->getName() );
	}

	public function testToString()
	{
		$types = array(
			'test' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'test()' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		);

		$translations = array(
			'test()' => 'testfunc($1,$2)',
		);

		$object = new \Aimeos\MW\Criteria\Expression\Sort\SQL( $this->conn, '-', 'test' );
		$this->assertEquals( 'test DESC', $object->toString( $types ) );

		$object = new \Aimeos\MW\Criteria\Expression\Sort\SQL( $this->conn, '+', 'test(1,2.1)' );
		$this->assertEquals( 'testfunc(1,2.1) ASC', $object->toString( $types, $translations ) );

		$object = new \Aimeos\MW\Criteria\Expression\Sort\SQL( $this->conn, '-', 'test("a",2)' );
		$this->assertEquals( 'testfunc(\'a\',2) DESC', $object->toString( $types, $translations ) );
	}
}
