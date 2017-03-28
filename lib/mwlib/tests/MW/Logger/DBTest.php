<?php

namespace Aimeos\MW\Logger;


class DBTest extends \PHPUnit_Framework_TestCase
{
	private static $dbm;
	private $object;


	public static function setUpBeforeClass()
	{
		self::$dbm = \TestHelperMw::getDBManager();

		if( !( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL ) ) {
			return;
		}

		$schema = new \Doctrine\DBAL\Schema\Schema();

		$table = $schema->createTable( 'mw_log_test' );
		$table->addColumn( 'facility', 'string', array( 'length' => 32 ) );
		$table->addColumn( 'request', 'string', array( 'length' => 32 ) );
		$table->addColumn( 'tstamp', 'string', array( 'length' => 20 ) );
		$table->addColumn( 'priority', 'integer', [] );
		$table->addColumn( 'message', 'text', array( 'length' => 0xffff ) );

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

			$conn->create( 'DROP TABLE "mw_log_test"' )->execute()->finish();

			self::$dbm->release( $conn );
		}
	}


	protected function setUp()
	{
		if( !( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL ) ) {
			$this->markTestSkipped( 'No DBAL database manager configured' );
		}


		$conn = self::$dbm->acquire();

		$sql = 'INSERT INTO "mw_log_test" ( "facility", "tstamp", "priority", "message", "request" ) VALUES ( ?, ?, ?, ?, ? )';
		$this->object = new \Aimeos\MW\Logger\DB( $conn->create( $sql ) );

		self::$dbm->release( $conn );
	}


	protected function tearDown()
	{
		if( self::$dbm instanceof \Aimeos\MW\DB\Manager\DBAL )
		{
			$conn = self::$dbm->acquire();

			$conn->create( 'DELETE FROM "mw_log_test"' )->execute()->finish();

			self::$dbm->release( $conn );
		}
	}


	public function testLog()
	{
		$this->object->log( 'error' );

		$conn = self::$dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		self::$dbm->release( $conn );

		if( $row === false ) {
			throw new \RuntimeException( 'No log record found' );
		}

		$this->assertEquals( 'message', $row['facility'] );
		$this->assertEquals( 32, strlen( $row['request'] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $row['tstamp'] ) );
		$this->assertEquals( \Aimeos\MW\Logger\Base::ERR, $row['priority'] );
		$this->assertEquals( 'error', $row['message'] );


		$this->setExpectedException('\\Aimeos\\MW\\Logger\\Exception');
		$this->object->log( 'wrong log level', -1);
	}


	public function testScalarLog()
	{
		$conn = self::$dbm->acquire();
		$conn->create( 'DELETE FROM "mw_log_test"' )->execute()->finish();

		$this->object->log( array ( 'scalar', 'errortest' ) );

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();

		$row = $result->fetch();

		self::$dbm->release( $conn );

		if( $row === false ) {
			throw new \RuntimeException( 'No log record found' );
		}

		$this->assertEquals( 'message', $row['facility'] );
		$this->assertEquals( 32, strlen( $row['request'] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $row['tstamp'] ) );
		$this->assertEquals( \Aimeos\MW\Logger\Base::ERR, $row['priority'] );
		$this->assertEquals( '["scalar","errortest"]', $row['message'] );
	}


	public function testLogCrit()
	{
		$this->object->log( 'critical', \Aimeos\MW\Logger\Base::CRIT );

		$conn = self::$dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		self::$dbm->release( $conn );

		if( $row === false ) {
			throw new \RuntimeException( 'No log record found' );
		}

		$this->assertEquals( 32, strlen( $row['request'] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $row['tstamp'] ) );
		$this->assertEquals( \Aimeos\MW\Logger\Base::CRIT, $row['priority'] );
		$this->assertEquals( 'critical', $row['message'] );
	}


	public function testLogWarn()
	{
		$this->object->log( 'debug', \Aimeos\MW\Logger\Base::WARN );

		$conn = self::$dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		self::$dbm->release( $conn );

		if( $row !== false ) {
			throw new \RuntimeException( 'Log record found but none expected' );
		}
	}


	public function testFacility()
	{
		$this->object->log( 'user auth', \Aimeos\MW\Logger\Base::ERR, 'auth' );

		$conn = self::$dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		self::$dbm->release( $conn );

		if( $row === false ) {
			throw new \RuntimeException( 'No log record found' );
		}

		$this->assertEquals( 'auth', $row['facility'] );
	}
}
