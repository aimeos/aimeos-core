<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Cache;


class DBTest extends \PHPUnit_Framework_TestCase
{
	private static $dbm;
	private $config;
	private $object;


	public static function setUpBeforeClass()
	{
		self::$dbm = \TestHelperMw::getDBManager();

		if( !( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL ) ) {
			return;
		}

		$schema = new \Doctrine\DBAL\Schema\Schema();

		$cacheTable = $schema->createTable( 'mw_cache_test' );
		$cacheTable->addColumn( 'id', 'string', array( 'length' => 255 ) );
		$cacheTable->addColumn( 'siteid', 'integer', array( 'notnull' => false ) );
		$cacheTable->addColumn( 'expire', 'datetime', array( 'notnull' => false ) );
		$cacheTable->addColumn( 'value', 'text', array( 'length' => 0xffff ) );
		$cacheTable->addUniqueIndex( array( 'id', 'siteid' ) );
		$cacheTable->addIndex( array( 'expire' ) );

		$tagTable = $schema->createTable( 'mw_cache_tag_test' );
		$tagTable->addColumn( 'tid', 'string', array( 'length' => 255 ) );
		$tagTable->addColumn( 'tsiteid', 'integer', array( 'notnull' => false ) );
		$tagTable->addColumn( 'tname', 'string', array( 'length' => 255 ) );
		$tagTable->addUniqueIndex( array( 'tid', 'tsiteid', 'tname' ) );
		$tagTable->addForeignKeyConstraint( 'mw_cache_test', array( 'tid', 'tsiteid' ), array( 'id', 'siteid' ), array( 'onDelete' => 'CASCADE' ) );


		$conn = self::$dbm->acquire();

		foreach( $schema->toSQL( $conn->getRawObject()->getDatabasePlatform() ) as $sql ) {
			$conn->create( $sql )->execute()->finish();
		}

		self::$dbm->release( $conn );
	}


	public static function tearDownAfterClass()
	{
		if( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL )
		{
			$conn = self::$dbm->acquire();

			$conn->create( 'DROP TABLE "mw_cache_tag_test"' )->execute()->finish();
			$conn->create( 'DROP TABLE "mw_cache_test"' )->execute()->finish();

			self::$dbm->release( $conn );
		}
	}


	protected function setUp()
	{
		if( !( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL ) ) {
			$this->markTestSkipped( 'No DBAL database manager configured' );
		}


		$conn = self::$dbm->acquire();

		$sql = 'INSERT INTO "mw_cache_test" ("id", "siteid", "expire", "value") VALUES (\'t:1\', 1, NULL, \'test 1\')';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_cache_test" ("id", "siteid", "expire", "value") VALUES (\'t:2\', 1, \'2000-01-01 00:00:00\', \'test 2\')';
		$conn->create( $sql )->execute()->finish();

		$sql = 'INSERT INTO "mw_cache_tag_test" ("tid", "tsiteid", "tname") VALUES (\'t:1\', 1, \'tag:1\')';
		$conn->create( $sql )->execute()->finish();

		self::$dbm->release( $conn );


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

		$this->object = new \Aimeos\MW\Cache\DB( $this->config, self::$dbm );
	}


	public function tearDown()
	{
		if( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL )
		{
			$conn = self::$dbm->acquire();

			$conn->create( 'DELETE FROM "mw_cache_tag_test"' )->execute()->finish();
			$conn->create( 'DELETE FROM "mw_cache_test"' )->execute()->finish();

			self::$dbm->release( $conn );
		}
	}


	public function testConstructorNoConfig()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		new \Aimeos\MW\Cache\DB( [], self::$dbm );
	}


	public function testConstructorNoSql()
	{
		$config = $this->config;
		unset( $config['sql'] );

		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		new \Aimeos\MW\Cache\DB( $config, self::$dbm );
	}


	public function testConstructorNoSearch()
	{
		$config = $this->config;
		unset( $config['search'] );

		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		new \Aimeos\MW\Cache\DB( $config, self::$dbm );
	}


	public function testConstructorIncompleteSql()
	{
		$config = $this->config;
		unset( $config['sql']['delete'] );

		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		new \Aimeos\MW\Cache\DB( $config, self::$dbm );
	}


	public function testConstructorIncompleteSearch()
	{
		$config = $this->config;
		unset( $config['search']['cache.id'] );

		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		new \Aimeos\MW\Cache\DB( $config, self::$dbm );
	}


	public function testCleanup()
	{
		$this->object->cleanup();


		$conn = self::$dbm->acquire();
		$result = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute();
		self::$dbm->release( $conn );

		$this->assertEquals( array( 'id' => 't:1' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testDelete()
	{
		$this->object->delete( 't:1' );

		$conn = self::$dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_tag_test"' )->execute()->fetch();
		self::$dbm->release( $conn );

		$this->assertFalse( $row );


		$conn = self::$dbm->acquire();
		$result = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute();
		self::$dbm->release( $conn );

		$this->assertEquals( array( 'id' => 't:2' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testDeleteMultiple()
	{
		$this->object->deleteMultiple( array( 't:1', 't:2' ) );

		$conn = self::$dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_test"' )->execute()->fetch();
		self::$dbm->release( $conn );

		$this->assertFalse( $row );
	}


	public function testDeleteByTags()
	{
		$this->object->deleteByTags( array( 'tag:1' ) );

		$conn = self::$dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_tag_test"' )->execute()->fetch();
		self::$dbm->release( $conn );

		$this->assertFalse( $row );


		$conn = self::$dbm->acquire();
		$result = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute();
		self::$dbm->release( $conn );

		$this->assertEquals( array( 'id' => 't:2' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testClear()
	{
		$this->object->clear();

		$conn = self::$dbm->acquire();
		$row = $conn->create( 'SELECT * FROM "mw_cache_tag_test"' )->execute()->fetch();
		self::$dbm->release( $conn );

		$this->assertFalse( $row );


		$conn = self::$dbm->acquire();
		$row = $conn->create( 'SELECT "id" FROM "mw_cache_test"' )->execute()->fetch();
		self::$dbm->release( $conn );

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


	public function testGetMultiple()
	{
		$this->assertEquals( array( 't:1' => 'test 1', 't:2' => null ), $this->object->getMultiple( array( 't:1', 't:2' ) ) );
	}


	public function testGetMultipleByTags()
	{
		$this->assertEquals( array( 't:1' => 'test 1' ), $this->object->getMultipleByTags( array( 'tag:1' ) ) );
	}


	public function testSet()
	{
		$this->object->set( 't:3', 'test 3', '2100-01-01 00:00:00', array( 'tag:2', 'tag:3' ) );


		$conn = self::$dbm->acquire();
		$result = $conn->create( 'SELECT "tname" FROM "mw_cache_tag_test" WHERE "tid" = \'t:3\' ORDER BY "tname"' )->execute();
		self::$dbm->release( $conn );

		$this->assertEquals( array( 'tname' => 'tag:2' ), $result->fetch() );
		$this->assertEquals( array( 'tname' => 'tag:3' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = self::$dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:3\'' )->execute();
		self::$dbm->release( $conn );

		$expected = array(
			'expire' => '2100-01-01 00:00:00',
			'id' => 't:3',
			'siteid' => 1,
			'value' => 'test 3',
		);
		$this->assertEquals( $expected, $result->fetch() );
		$this->assertFalse( $result->fetch() );
	}


	public function testSetMultiple()
	{
		$pairs = array( 't:3' => 'test 3', 't:2' => 'test 4' );
		$tags = array( 't:3' => array( 'tag:2', 'tag:3' ), 't:2' => array( 'tag:4' ) );
		$expires = array( 't:3' => '2100-01-01 00:00:00', 't:2' => 300 );

		$this->object->setMultiple( $pairs, $expires, $tags );


		$conn = self::$dbm->acquire();
		$result = $conn->create( 'SELECT "tname" FROM "mw_cache_tag_test" WHERE "tid" = \'t:3\' ORDER BY "tname"' )->execute();
		self::$dbm->release( $conn );

		$this->assertEquals( array( 'tname' => 'tag:2' ), $result->fetch() );
		$this->assertEquals( array( 'tname' => 'tag:3' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = self::$dbm->acquire();
		$result = $conn->create( 'SELECT "tname" FROM "mw_cache_tag_test" WHERE "tid" = \'t:2\'' )->execute();
		self::$dbm->release( $conn );

		$this->assertEquals( array( 'tname' => 'tag:4' ), $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = self::$dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:3\'' )->execute();
		self::$dbm->release( $conn );

		$expected = array(
			'expire' => '2100-01-01 00:00:00',
			'id' => 't:3',
			'siteid' => 1,
			'value' => 'test 3',
		);
		$this->assertEquals( $expected, $result->fetch() );
		$this->assertFalse( $result->fetch() );


		$conn = self::$dbm->acquire();
		$result = $conn->create( 'SELECT * FROM "mw_cache_test" WHERE "id" = \'t:2\'' )->execute();
		self::$dbm->release( $conn );

		$actual = $result->fetch();

		$this->assertFalse( $result->fetch() );
		$this->assertEquals( 't:2', $actual['id'] );
		$this->assertEquals( 1, $actual['siteid'] );
		$this->assertEquals( 'test 4', $actual['value'] );
		$this->assertGreaterThan( date( 'Y-m-d H:i:s' ), $actual['expire'] );
	}


	public function testSetException()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		$this->object->set( [], '' );
	}

}
