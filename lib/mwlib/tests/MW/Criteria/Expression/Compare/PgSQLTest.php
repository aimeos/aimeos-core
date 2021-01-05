<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MW\Criteria\Expression\Compare;


class PgSQLTest extends \PHPUnit\Framework\TestCase
{
	private $conn;


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

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PgSQL( $this->conn, '==', 'list', array( 'a', 'b', 'c' ) );
		$this->assertEquals( "t.list IN ('a','b','c')", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PgSQL( $this->conn, '!=', 'list', array( 'a', 'b', 'c' ) );
		$this->assertEquals( "t.list NOT IN ('a','b','c')", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PgSQL( $this->conn, '~=', 'string', 'value' );
		$this->assertEquals( "t.string LIKE '%value%' ESCAPE '#'", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PgSQL( $this->conn, '<', 'float', 0.1 );
		$this->assertEquals( "t.float < 0.1", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PgSQL( $this->conn, '>', 'int', 10 );
		$this->assertEquals( "t.int > 10", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PgSQL( $this->conn, '!=', 'undefined', null );
		$this->assertEquals( "t.undefined IS NOT NULL", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PgSQL( $this->conn, '==', 'bool', true );
		$this->assertEquals( "t.bool = 't'", $expr->toSource( $types, $translations ) );

		$expr = new \Aimeos\MW\Criteria\Expression\Compare\PgSQL( $this->conn, '==', 'bool', false );
		$this->assertEquals( "t.bool = 'f'", $expr->toSource( $types, $translations ) );
	}
}
