<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

namespace Aimeos\MW\Criteria;


class PgSQLTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire();

		$this->object = new \Aimeos\MW\Criteria\PgSQL( $conn );

		$dbm->release( $conn );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreateFunction()
	{
		$params = array( 'listtype', 'langid', 'test string' );

		$str = $this->object->createFunction( 'index.text.relevance', $params );
		$this->assertEquals( 'index.text.relevance("listtype","langid","test & string")', $str );

		$str = $this->object->createFunction( 'sort:index.text.relevance', $params );
		$this->assertEquals( 'sort:index.text.relevance("listtype","langid","test & string")', $str );
	}

}
