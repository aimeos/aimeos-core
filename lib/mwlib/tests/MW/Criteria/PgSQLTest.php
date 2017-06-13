<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2017
 */

namespace Aimeos\MW\Criteria;


class PgSQLTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		if( \TestHelperMw::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}

		$dbm = \TestHelperMw::getDBManager();

		$conn = $dbm->acquire();
		$this->object = new \Aimeos\MW\Criteria\PgSQL( $conn );
		$dbm->release( $conn );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCreateFunction()
	{
		$params = array( 'listtype', 'langid', 'test string' );

		$str = $this->object->createFunction( 'index.text.relevance', $params );
		$this->assertEquals( 'index.text.relevance("listtype","langid","test:* & string:*")', $str );

		$str = $this->object->createFunction( 'sort:index.text.relevance', $params );
		$this->assertEquals( 'sort:index.text.relevance("listtype","langid","test:* & string:*")', $str );
	}

}
