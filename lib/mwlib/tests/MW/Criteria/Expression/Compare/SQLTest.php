<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Criteria\Expression\Compare;


class SQLTest extends \PHPUnit\Framework\TestCase
{
	private $conn = null;


	protected function setUp() : void
	{
		if( \TestHelperMw::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$dbm = \TestHelperMw::getDBManager();
		$this->conn = $dbm->acquire();
	}


	protected function tearDown() : void
	{
		$dbm = \TestHelperMw::getDBManager();
		$dbm->release( $this->conn );
	}


	public function testGetOperators()
	{
		$expected = ['=~', '~=', '==', '!=', '>', '>=', '<', '<=', '-'];
		$actual = \Aimeos\MW\Criteria\Expression\Compare\SQL::getOperators();
		$this->assertEquals( $expected, $actual );
	}


	public function testGetOperator()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '==', 'name', 'value' );
		$this->assertEquals( '==', $expr->getOperator() );
	}


	public function testGetName()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '==', 'name', 'value' );
		$this->assertEquals( 'name', $expr->getName() );
	}


	public function testGetValue()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '==', 'name', 'value' );
		$this->assertEquals( 'value', $expr->getValue() );
	}


	public function testToSource()
	{
		$types = array(
			'list' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'string' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'float' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'int' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'undefined' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'bool' => \Aimeos\MW\DB\Statement\Base::PARAM_BOOL,
		);

		$translations = array(
			'list' => 't.list',
			'string' => 't.string',
			'float' => 't.float',
			'int' => 't.int',
			'undefined' => 't.undefined',
			'bool' => 't.bool',
		);

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '==', 'list', array( 'a', 'b', 'c' ) );
		$this->assertEquals( "t.list IN ('a','b','c')", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '!=', 'list', array( 'a', 'b', 'c' ) );
		$this->assertEquals( "t.list NOT IN ('a','b','c')", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '~=', 'string', 'value' );
		$this->assertEquals( "t.string LIKE '%value%' ESCAPE '#'", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '<', 'float', 0.1 );
		$this->assertEquals( "t.float < 0.1", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '>', 'int', 10 );
		$this->assertEquals( "t.int > 10", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '!=', 'undefined', null );
		$this->assertEquals( "t.undefined IS NOT NULL", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '==', 'bool', true );
		$this->assertEquals( "t.bool = 1", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '-', 'int', '10 - 20' );
		$this->assertEquals( "t.int >= 10 AND t.int <= 20", $expr->toSource( $types, $translations ) );
	}


	public function testToSourceFunction()
	{
		$types = array(
			'pcounter()' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'strconcat()' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'lcounter()' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'isnull()' => \Aimeos\MW\DB\Statement\Base::PARAM_NULL,
		);

		$translations = array(
			'pcounter()' => 'count($1,$2,$3)',
			'strconcat()' => 'concat($1,$2)',
			'lcounter()' => 'count(name IN ($1))',
			'isnull()' => '($1 IS NULL)',
		);

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '==', 'pcounter("name",10,0.1)', 3 );
		$this->assertEquals( "count('name',10,0.1) = 3", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '~=', 'strconcat("hello","world")', 'low' );
		$this->assertEquals( "concat('hello','world') LIKE '%low%' ESCAPE '#'", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '==', 'lcounter(["a","b","c","\'d"])', 4 );
		$this->assertRegexp( "/^count\(name IN \('a','b','c','('|\\\\)'d'\)\) = 4$/", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '==', 'lcounter([])', 0 );
		$this->assertEquals( "count(name IN ()) = 0", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $this->conn, '!=', 'isnull(null)', null );
		$this->assertEquals( "(null IS NULL) IS NOT NULL", $expr->toSource( $types, $translations ) );
	}


	public function testToArray()
	{
		$dbm = \TestHelperMw::getDBManager();
		$conn = $dbm->acquire();
		$dbm->release( $conn );

		$expected = ['==' => ['stringvar' => 'value']];
		$object = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '==', 'stringvar', 'value' );

		$this->assertEquals( $expected, $object->__toArray() );
	}
}
