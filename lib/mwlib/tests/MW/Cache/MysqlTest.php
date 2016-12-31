<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Cache;


class MysqlTest extends \PHPUnit_Framework_TestCase
{
	private $dbm;
	private $config;
	private $object;


	protected function setUp()
	{
		if( \TestHelperMw::getConfig()->get( 'resource/db/adapter', false ) !== 'mysql' ) {
			$this->markTestSkipped( 'No MySQL database configured' );
		}


		$this->config = array( 'siteid' => 1 );

		$this->config['search'] = array(
			'cache.id' => array( 'label' => 'Cache ID', 'code' => 'cache.id', 'internalcode' => 'id', 'type' => 'string', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR ),
			'cache.siteid' => array( 'label' => 'Cache site ID', 'code' => 'cache.siteid', 'internalcode' => 'siteid', 'type' => 'integer', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT ),
			'cache.value' => array( 'label' => 'Cached value', 'code' => 'cache.value', 'internalcode' => 'value', 'type' => 'string', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR ),
			'cache.expire' => array( 'label' => 'Cache expiration date', 'code' => 'cache.expire', 'internalcode' => 'expire', 'type' => 'datetime', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR ),
			'cache.tag.name' => array( 'label' => 'Cache tag name', 'code' => 'cache.tag.name', 'internalcode' => 'tname', 'type' => 'string', 'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR ),
		);

		$this->config['sql'] = array(
			'delete' => '
				DELETE FROM "mw_cache_test" WHERE "siteid" = ? AND :cond
			',
			'deletebytag' => '
				DELETE FROM "mw_cache_test" WHERE "siteid" = ? AND id IN (
					SELECT "tid" FROM "mw_cache_tag_test" WHERE "tsiteid" = ? AND :cond
				)
			',
			'get' => '
				SELECT "id", "value", "expire" FROM "mw_cache_test" WHERE "siteid" = ? AND :cond
			',
			'getbytag' => '
				SELECT "id", "value", "expire" FROM "mw_cache_test"
				JOIN "mw_cache_tag_test" ON "tid" = "id"
				WHERE siteid = ? AND tsiteid = ? AND :cond
			',
			'set' => '
				REPLACE INTO "mw_cache_test" ( "id", "siteid", "expire", "value" ) VALUES ( ?, ?, ?, ? )
			',
			'settag' => '
				REPLACE INTO "mw_cache_tag_test" ( "tid", "tsiteid", "tname" ) VALUES  :tuples
			',
		);


		$this->dbm = \TestHelperMw::getDBManager();
		$conn = $this->dbm->acquire();


		$sql = 'DROP TABLE IF EXISTS "mw_cache_tag_test"';
		$conn->create( $sql )->execute()->finish();

		$sql = 'DROP TABLE IF EXISTS "mw_cache_test"';
		$conn->create( $sql )->execute()->finish();

		$sql = '
			CREATE TABLE IF NOT EXISTS "mw_cache_test" (
				"id" VARCHAR(255) NOT NULL,
				"siteid" INTEGER NULL,
				"expire" DATETIME NULL,
				"value" MEDIUMTEXT NOT NULL,
				KEY ("expire"),
				CONSTRAINT UNIQUE KEY ("id", "siteid")
			);
		';
		$conn->create( $sql )->execute()->finish();

		$sql = '
			CREATE TABLE IF NOT EXISTS "mw_cache_tag_test" (
				"tid" VARCHAR(255) NOT NULL,
				"tsiteid" INTEGER NULL,
				"tname" VARCHAR(255) NOT NULL,
				CONSTRAINT UNIQUE ("tid", "tsiteid", "tname"),
				CONSTRAINT FOREIGN KEY ("tid") REFERENCES "mw_cache_test" ("id") ON DELETE CASCADE
			);
		';
		$conn->create( $sql )->execute()->finish();


		$sql = 'INSERT INTO "mw_cache_test" ("id", "siteid", "expire", "value") VALUES (\'t:1\', 1, NULL, \'test 1\')';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_cache_tag_test" ("tid", "tsiteid", "tname") VALUES (\'t:1\', 1, \'tag:1\')';
		$conn->create( $sql )->execute()->finish();


		$this->dbm->release( $conn );


		$this->object = new \Aimeos\MW\Cache\Mysql( $this->config, $this->dbm );
	}


	protected function tearDown()
	{
		if( \TestHelperMw::getConfig()->get( 'resource/db/adapter', false ) === 'mysql' )
		{
			$this->dbm = \TestHelperMw::getDBManager();
			$conn = $this->dbm->acquire();

			$conn->create( 'DROP TABLE "mw_cache_tag_test"' )->execute()->finish();
			$conn->create( 'DROP TABLE "mw_cache_test"' )->execute()->finish();

			$this->dbm->release( $conn );
		}
	}


	public function testSetMultiple()
	{
		$pairs = array( 't:1' => 'test 1', 't:2' => 'test 2' );
		$tags = array( 't:1' => array( 'tag:1', 'tag:2', 'tag:3' ) );
		$expires = array( 't:1' => '2100-00-00 00:00:00', 't:2' => 300 );

		$this->object->setMultiple( $pairs, $expires, $tags );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT "tname" FROM "mw_cache_tag_test" WHERE "tid" = \'t:1\' ORDER BY "tname"' )->execute();
		$this->dbm->release( $conn );

		$this->assertEquals( array( 'tname' => 'tag:1' ), $result->fetch() );
		$this->assertEquals( array( 'tname' => 'tag:2' ), $result->fetch() );
		$this->assertEquals( array( 'tname' => 'tag:3' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:1\'' )->execute();
		$this->dbm->release( $conn );

		$expected = array(
			'expire' => '2100-00-00 00:00:00',
			'id' => 't:1',
			'siteid' => 1,
			'value' => 'test 1',
		);
		$this->assertEquals( $expected, $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:2\'' )->execute();
		$this->dbm->release( $conn );

		$actual = $result->fetch();

		$this->assertFalse( $result->fetch() );
		$this->assertEquals( 't:2', $actual['id'] );
		$this->assertEquals( 1, $actual['siteid'] );
		$this->assertEquals( 'test 2', $actual['value'] );
		$this->assertGreaterThan( date( 'Y-m-d H:i:s' ), $actual['expire'] );
	}
}
