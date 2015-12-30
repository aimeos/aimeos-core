<?php

namespace Aimeos\MW\Cache;


/**
 * Test class for \Aimeos\MW\Cache\Mysql.
 *
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class MysqlTest extends \PHPUnit_Framework_TestCase
{
	private $dbm;
	private $config;
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$adapter = \TestHelperMw::getConfig()->get( 'resource/db/adapter', false );

		if( $adapter === false || $adapter !== 'mysql' ) {
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


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->dbm = \TestHelperMw::getDBManager();
		$conn = $this->dbm->acquire();

		$conn->create( 'DROP TABLE "mw_cache_tag_test"' )->execute()->finish();
		$conn->create( 'DROP TABLE "mw_cache_test"' )->execute()->finish();

		$this->dbm->release( $conn );
	}


	public function testSetList()
	{
		$pairs = array( 't:1' => 'test 2' );
		$tags = array( 't:1' => array( 'tag:1', 'tag:2', 'tag:3' ) );
		$expires = array( 't:1' => '2100-00-00 00:00:00' );

		$this->object->setList( $pairs, $tags, $expires );


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
			'value' => 'test 2',
		);
		$this->assertEquals( $expected, $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}
}
