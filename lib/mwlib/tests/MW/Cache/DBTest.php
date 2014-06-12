<?php

/**
 * Test class for MW_Cache_DB.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Cache_DBTest extends MW_Unittest_Testcase
{
	private $_dbm;
	private $_config;
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( TestHelper::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$this->_config = array();

		$this->_config['search'] = array(
			'id' => array( 'label' => 'Cache ID', 'code' => 'id', 'internalcode' => 'id', 'type' => 'string', 'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
			'value' => array( 'label' => 'Cached value', 'code' => 'value', 'internalcode' => 'value', 'type' => 'string', 'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
			'expire' => array( 'label' => 'Cache expiration date', 'code' => 'expire', 'internalcode' => 'expire', 'type' => 'datetime', 'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
			'tag.name' => array( 'label' => 'Cache tag name', 'code' => 'tag.name', 'internalcode' => 't.name', 'type' => 'string', 'internaltype' => MW_DB_Statement_Abstract::PARAM_STR ),
		);

		$this->_config['sql'] = array(
			'delete' => '
				DELETE FROM "mw_cache_test" WHERE :cond
			',
			'deletebytag' => '
				DELETE FROM "mw_cache_test" WHERE id IN (
					SELECT t."id" FROM "mw_cache_tag_test" AS t WHERE :cond
				)
			',
			'get' => '
				SELECT "id", "value" FROM "mw_cache_test" WHERE :cond
			',
			'getbytag' => '
				SELECT c."id", c."value" FROM "mw_cache_test" c
				JOIN "mw_cache_tag_test" AS t ON t."id" = c."id"
				WHERE :cond
			',
			'set' => '
				INSERT INTO "mw_cache_test" ( "id", "expire", "value" ) VALUES ( ?, ?, ? )
			',
			'settag' => '
				INSERT INTO "mw_cache_tag_test" ( "id", "name" ) VALUES ( ?, ? )
			',
		);


		$this->_dbm = TestHelper::getDBManager();
		$conn = $this->_dbm->acquire();


		$sql = 'DROP TABLE IF EXISTS "mw_cache_tag_test"';
		$conn->create( $sql )->execute()->finish();

		$sql = 'DROP TABLE IF EXISTS "mw_cache_test"';
		$conn->create( $sql )->execute()->finish();

		$sql = '
			CREATE TABLE IF NOT EXISTS "mw_cache_test" (
				"id" VARCHAR(255) NOT NULL PRIMARY KEY,
				"expire" DATETIME NULL,
				"value" MEDIUMTEXT NOT NULL,
				KEY ("expire")
			);
		';
		$conn->create( $sql )->execute()->finish();

		$sql = '
			CREATE TABLE IF NOT EXISTS "mw_cache_tag_test" (
				"id" VARCHAR(255) NOT NULL,
				"name" VARCHAR(255) NOT NULL,
				CONSTRAINT UNIQUE ("id", "name"),
				CONSTRAINT FOREIGN KEY ("id") REFERENCES "mw_cache_test" ("id") ON DELETE CASCADE
			);
		';
		$conn->create( $sql )->execute()->finish();


		$sql = 'INSERT INTO "mw_cache_test" ("id", "expire", "value") VALUES (\'t:1\', NULL, \'test 1\')';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_cache_test" ("id", "expire", "value") VALUES (\'t:2\', \'2000-01-01 00:00:00\', \'test 2\')';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_cache_tag_test" ("id", "name") VALUES (\'t:1\', \'tag:1\')';
		$conn->create( $sql )->execute()->finish();


		$this->_dbm->release( $conn );


		$this->_object = new MW_Cache_DB( $this->_dbm, $this->_config );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_dbm = TestHelper::getDBManager();
		$conn = $this->_dbm->acquire();

		$conn->create( 'DROP TABLE "mw_cache_tag_test"' )->execute()->finish();
		$conn->create( 'DROP TABLE "mw_cache_test"' )->execute()->finish();

		$this->_dbm->release( $conn );
	}


	public function testCleanup()
	{
		$this->_object->cleanup();


		$conn = $this->_dbm->acquire();
		$result = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute();
		$this->_dbm->release( $conn );

		$this->assertEquals( array( 'id' => 't:1' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testDelete()
	{
		$this->_object->delete( 't:1' );

		$conn = $this->_dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_tag_test"' )->execute()->fetch();
		$this->_dbm->release( $conn );

		$this->assertFalse( $row );


		$conn = $this->_dbm->acquire();
		$result = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute();
		$this->_dbm->release( $conn );

		$this->assertEquals( array( 'id' => 't:2' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testDeleteList()
	{
		$this->_object->deleteList( array( 't:1', 't:2' ) );

		$conn = $this->_dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_test"' )->execute()->fetch();
		$this->_dbm->release( $conn );

		$this->assertFalse( $row );
	}


	public function testDeleteByTags()
	{
		$this->_object->deleteByTags( array( 'tag:1' ) );

		$conn = $this->_dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_tag_test"' )->execute()->fetch();
		$this->_dbm->release( $conn );

		$this->assertFalse( $row );


		$conn = $this->_dbm->acquire();
		$result = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute();
		$this->_dbm->release( $conn );

		$this->assertEquals( array( 'id' => 't:2' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testFlush()
	{
		$this->_object->flush();

		$conn = $this->_dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_tag_test"' )->execute()->fetch();
		$this->_dbm->release( $conn );

		$this->assertFalse( $row );


		$conn = $this->_dbm->acquire();
		$row = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute()->fetch();
		$this->_dbm->release( $conn );

		$this->assertFalse( $row );
	}


	public function testGet()
	{
		$this->assertEquals( 'test 1', $this->_object->get( 't:1' ) );
	}


	public function testGetExpired()
	{
		$this->assertEquals( null, $this->_object->get( 't:2' ) );
	}


	public function testGetList()
	{
		$this->assertEquals( array( 't:1' => 'test 1' ), $this->_object->getList( array( 't:1', 't:2' ) ) );
	}


	public function testGetListByTags()
	{
		$this->assertEquals( array( 't:1' => 'test 1' ), $this->_object->getListByTags( array( 'tag:1' ) ) );
	}


	public function testSet()
	{
		$this->_object->set( 't:3', 'test 3', array( 'tag:2', 'tag:3' ), '2100-00-00 00:00:00' );


		$conn = $this->_dbm->acquire();
		$result = $conn->create( 'SELECT "name" FROM "mw_cache_tag_test" WHERE "id" = \'t:3\' ORDER BY "name"' )->execute();
		$this->_dbm->release( $conn );

		$this->assertEquals( array( 'name' => 'tag:2' ), $result->fetch() );
		$this->assertEquals( array( 'name' => 'tag:3' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = $this->_dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:3\'' )->execute();
		$this->_dbm->release( $conn );

		$expected = array(
			'expire' => '2100-00-00 00:00:00',
			'id' => 't:3',
			'value' => 'test 3',
		);
		$this->assertEquals( $expected, $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testSetList()
	{
		$pairs = array( 't:3' => 'test 3', 't:4' => 'test 4' );
		$tags = array( 't:3' => array( 'tag:2', 'tag:3' ), 't:4' => array( 'tag:4' ) );
		$expires = array( 't:3' => '2100-00-00 00:00:00' );

		$this->_object->setList( $pairs, $tags, $expires );


		$conn = $this->_dbm->acquire();
		$result = $conn->create( 'SELECT "name" FROM "mw_cache_tag_test" WHERE "id" = \'t:3\' ORDER BY "name"' )->execute();
		$this->_dbm->release( $conn );

		$this->assertEquals( array( 'name' => 'tag:2' ), $result->fetch() );
		$this->assertEquals( array( 'name' => 'tag:3' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = $this->_dbm->acquire();
		$result = $conn->create( 'SELECT "name" FROM "mw_cache_tag_test" WHERE "id" = \'t:4\'' )->execute();
		$this->_dbm->release( $conn );

		$this->assertEquals( array( 'name' => 'tag:4' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = $this->_dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:3\'' )->execute();
		$this->_dbm->release( $conn );

		$this->assertEquals( array( 'expire' => '2100-00-00 00:00:00', 'id' => 't:3', 'value' => 'test 3' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = $this->_dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:4\'' )->execute();
		$this->_dbm->release( $conn );

		$this->assertEquals( array( 'expire' => null, 'id' => 't:4', 'value' => 'test 4' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testSetException()
	{
		$this->setExpectedException( 'MW_Cache_Exception' );
		$this->_object->set( array(), '' );
	}


	public function testConstructorNoConfig()
	{
		$this->setExpectedException( 'MW_Cache_Exception' );
		$obj = new MW_Cache_DB( $this->_dbm, array() );
	}


	public function testConstructorNoSql()
	{
		$config = $this->_config;
		unset( $config['sql'] );

		$this->setExpectedException( 'MW_Cache_Exception' );
		$obj = new MW_Cache_DB( $this->_dbm, $config );
	}


	public function testConstructorNoSearch()
	{
		$config = $this->_config;
		unset( $config['search'] );

		$this->setExpectedException( 'MW_Cache_Exception' );
		$obj = new MW_Cache_DB( $this->_dbm, $config );
	}


	public function testConstructorIncompleteSql()
	{
		$config = $this->_config;
		unset( $config['sql']['delete'] );

		$this->setExpectedException( 'MW_Cache_Exception' );
		$obj = new MW_Cache_DB( $this->_dbm, $config );
	}


	public function testConstructorIncompleteSearch()
	{
		$config = $this->_config;
		unset( $config['search']['id'] );

		$this->setExpectedException( 'MW_Cache_Exception' );
		$obj = new MW_Cache_DB( $this->_dbm, $config );
	}

}
