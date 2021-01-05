<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Criteria\Expression\Combine;


class SQLTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		if( \TestHelperMw::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}
	}


	public function testGetOperators()
	{
		$expected = array( '&&', '||', '!' );
		$actual = \Aimeos\MW\Criteria\Expression\Combine\SQL::getOperators();
		$this->assertEquals( $expected, $actual );
	}


	public function testGetOperator()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '||', [] );
		$this->assertEquals( '||', $expr->getOperator() );
	}


	public function testGetExpressions()
	{
		$expr = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '||', [] );
		$this->assertEquals( [], $expr->getExpressions() );
	}


	public function testToString()
	{
		$dbm = \TestHelperMw::getDBManager();
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

		$expr1 = [];
		$expr1[] = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '==', 'list', array( 'a', 'b', 'c' ) );
		$expr1[] = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '~=', 'string', 'value' );

		$expr2 = [];
		$expr2[] = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '<', 'float', 0.1 );
		$expr2[] = new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '>', 'int', 10 );

		$objects = [];
		$objects[] = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '&&', $expr1 );
		$objects[] = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '&&', $expr2 );

		$object = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '||', $objects );
		$test = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '!', array( $object ) );

		$expected = " NOT ( ( ( list IN ('a','b','c') AND string LIKE '%value%' ESCAPE '#' ) OR ( float < 0.1 AND int > 10 ) ) )";
		$this->assertEquals( $expected, $test->toSource( $types ) );

		$obj = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '&&', [] );
		$this->assertEquals( '', $obj->toSource( $types ) );

		$this->expectException( \Aimeos\MW\Common\Exception::class );
		new \Aimeos\MW\Criteria\Expression\Combine\SQL( '', [] );
	}


	public function testToArray()
	{
		$dbm = \TestHelperMw::getDBManager();
		$conn = $dbm->acquire();
		$dbm->release( $conn );

		$expected = [
			'&&' => [
				['==' => ['stringvar' => 'value']],
				['>' => ['intvar' => 10]],
			]
		];

		$expr = [
			new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '==', 'stringvar', 'value' ),
			new \Aimeos\MW\Criteria\Expression\Compare\SQL( $conn, '>', 'intvar', 10 ),
		];

		$object = new \Aimeos\MW\Criteria\Expression\Combine\SQL( '&&', $expr );

		$this->assertEquals( $expected, $object->__toArray() );
	}
}
