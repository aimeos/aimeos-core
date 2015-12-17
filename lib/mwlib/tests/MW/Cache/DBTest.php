<?php

namespace Aimeos\MW\Cache;


/**
 * Test class for \Aimeos\MW\Cache\DB.
 *
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class DBTest extends \PHPUnit_Framework_TestCase
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
		if( \TestHelperMw::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
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
				INSERT INTO "mw_cache_test" ( "id", "siteid", "expire", "value" ) VALUES ( ?, ?, ?, ? )
			',
			'settag' => '
				INSERT INTO "mw_cache_tag_test" ( "tid", "tsiteid", "tname" ) VALUES ( ?, ?, ? )
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
				CONSTRAINT PRIMARY KEY ("id", "siteid")
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

		$sql = 'INSERT INTO "mw_cache_test" ("id", "siteid", "expire", "value") VALUES (\'t:2\', 1, \'2000-01-01 00:00:00\', \'test 2\')';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_cache_tag_test" ("tid", "tsiteid", "tname") VALUES (\'t:1\', 1, \'tag:1\')';
		$conn->create( $sql )->execute()->finish();


		$this->dbm->release( $conn );


		$this->object = new \Aimeos\MW\Cache\DB( $this->config, $this->dbm );
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


	public function testConstructorNoConfig()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		new \Aimeos\MW\Cache\DB( array(), $this->dbm );
	}


	public function testConstructorNoSql()
	{
		$config = $this->config;
		unset( $config['sql'] );

		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		new \Aimeos\MW\Cache\DB( $config, $this->dbm );
	}


	public function testConstructorNoSearch()
	{
		$config = $this->config;
		unset( $config['search'] );

		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		new \Aimeos\MW\Cache\DB( $config, $this->dbm );
	}


	public function testConstructorIncompleteSql()
	{
		$config = $this->config;
		unset( $config['sql']['delete'] );

		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		new \Aimeos\MW\Cache\DB( $config, $this->dbm );
	}


	public function testConstructorIncompleteSearch()
	{
		$config = $this->config;
		unset( $config['search']['cache.id'] );

		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		new \Aimeos\MW\Cache\DB( $config, $this->dbm );
	}


	public function testCleanup()
	{
		$this->object->cleanup();


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute();
		$this->dbm->release( $conn );

		$this->assertEquals( array( 'id' => 't:1' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testDelete()
	{
		$this->object->delete( 't:1' );

		$conn = $this->dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_tag_test"' )->execute()->fetch();
		$this->dbm->release( $conn );

		$this->assertFalse( $row );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute();
		$this->dbm->release( $conn );

		$this->assertEquals( array( 'id' => 't:2' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testDeleteList()
	{
		$this->object->deleteList( array( 't:1', 't:2' ) );

		$conn = $this->dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_test"' )->execute()->fetch();
		$this->dbm->release( $conn );

		$this->assertFalse( $row );
	}


	public function testDeleteByTags()
	{
		$this->object->deleteByTags( array( 'tag:1' ) );

		$conn = $this->dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_tag_test"' )->execute()->fetch();
		$this->dbm->release( $conn );

		$this->assertFalse( $row );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute();
		$this->dbm->release( $conn );

		$this->assertEquals( array( 'id' => 't:2' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testFlush()
	{
		$this->object->flush();

		$conn = $this->dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_tag_test"' )->execute()->fetch();
		$this->dbm->release( $conn );

		$this->assertFalse( $row );


		$conn = $this->dbm->acquire();
		$row = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute()->fetch();
		$this->dbm->release( $conn );

		$this->assertFalse( $row );
	}


	public function testGet()
	{
		$this->assertEquals( 'test 1', $this->object->get( 't:1' ) );
	}


	public function testGetExpired()
	{
		$this->assertEquals( null, $this->object->get( 't:2' ) );
	}


	public function testGetList()
	{
		$this->assertEquals( array( 't:1' => 'test 1' ), $this->object->getList( array( 't:1', 't:2' ) ) );
	}


	public function testGetListByTags()
	{
		$this->assertEquals( array( 't:1' => 'test 1' ), $this->object->getListByTags( array( 'tag:1' ) ) );
	}


	public function testSet()
	{
		$this->object->set( 't:3', 'test 3', array( 'tag:2', 'tag:3' ), '2100-00-00 00:00:00' );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT "tname" FROM "mw_cache_tag_test" WHERE "tid" = \'t:3\' ORDER BY "tname"' )->execute();
		$this->dbm->release( $conn );

		$this->assertEquals( array( 'tname' => 'tag:2' ), $result->fetch() );
		$this->assertEquals( array( 'tname' => 'tag:3' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:3\'' )->execute();
		$this->dbm->release( $conn );

		$expected = array(
			'expire' => '2100-00-00 00:00:00',
			'id' => 't:3',
			'siteid' => 1,
			'value' => 'test 3',
		);
		$this->assertEquals( $expected, $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testSetList()
	{
		$pairs = array( 't:3' => 'test 3', 't:2' => 'test 4' );
		$tags = array( 't:3' => array( 'tag:2', 'tag:3' ), 't:2' => array( 'tag:4' ) );
		$expires = array( 't:3' => '2100-00-00 00:00:00' );

		$this->object->setList( $pairs, $tags, $expires );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT "tname" FROM "mw_cache_tag_test" WHERE "tid" = \'t:3\' ORDER BY "tname"' )->execute();
		$this->dbm->release( $conn );

		$this->assertEquals( array( 'tname' => 'tag:2' ), $result->fetch() );
		$this->assertEquals( array( 'tname' => 'tag:3' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT "tname" FROM "mw_cache_tag_test" WHERE "tid" = \'t:2\'' )->execute();
		$this->dbm->release( $conn );

		$this->assertEquals( array( 'tname' => 'tag:4' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:3\'' )->execute();
		$this->dbm->release( $conn );

		$expected = array(
			'expire' => '2100-00-00 00:00:00',
			'id' => 't:3',
			'siteid' => 1,
			'value' => 'test 3',
		);
		$this->assertEquals( $expected, $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = $this->dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:2\'' )->execute();
		$this->dbm->release( $conn );

		$expected = array(
			'expire' => null,
			'id' => 't:2',
			'siteid' => 1,
			'value' => 'test 4',
		);
		$this->assertEquals( $expected, $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testSetException()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		$this->object->set( array(), '' );
	}

}
