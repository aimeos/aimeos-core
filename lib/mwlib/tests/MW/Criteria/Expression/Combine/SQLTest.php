<?php

namespace Aimeos\MW\Criteria\Expression\Combine;


/**
 * Test class for \Aimeos\MW\Criteria\Expression\Combine\SQL.
 *
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class SQLTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( \TestHelper::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}
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
		$actual = \Aimeos\MW\Criteria\Expression\Combine\SQL::getOperators();
		$this->assertEquals( $expected, $actual );
	}

	public function testGetOperator()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '||', array() );
		$this->assertEquals( '||', $expr->getOperator() );
	}

	public function testGetExpressions()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '||', array() );
		$this->assertEquals( array(), $expr->getExpressions() );
	}

	public function testToString()
	{
		$dbm = \TestHelper::getDBManager();
		$conn = $dbm->acquire();
		$dbm->release( $conn );

		$types = array(
			'list' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'string' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'float' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'int' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'undefined' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'bool' => \Aimeos\MW\DB\Statement\Base::PARAM_BOOL,
		);

		$expr1 = array();
		$expr1[] = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '==', 'list', array('a', 'b', 'c') );
		$expr1[] = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '~=', 'string', 'value' );

		$expr2 = array();
		$expr2[] = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '<', 'float', 0.1 );
		$expr2[] = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '>', 'int', 10 );

		$objects = array();
		$objects[] = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '&&', $expr1 );
		$objects[] = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '&&', $expr2 );

		$object = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '||', $objects );
		$test = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '!', array( $object ) );

		$expected = " NOT ( ( list IN ('a','b','c') AND string LIKE '%value%' ) OR ( float < 0.1 AND int > 10 ) )";
		$this->assertEquals( $expected, $test->toString( $types ) );

		$obj = new \Aimeos\MW\Criteria\Expression\Combine\SQL('&&', array());
		$this->assertEquals('', $obj->toString($types));

		$this->setExpectedException('\\Aimeos\\MW\\Common\\Exception');
		new \Aimeos\MW\Criteria\Expression\Combine\SQL('', array());

	}
}
