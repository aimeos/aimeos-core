<?php

namespace Aimeos\MW\Logger;


/**
 * Test class for \Aimeos\MW\Logger\DB.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class DBTest extends \PHPUnit_Framework_TestCase
{
	private $dbm;
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( \TestHelper::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$this->dbm = \TestHelper::getDBManager();

		$conn = $this->dbm->acquire();

		$conn->create( '
			CREATE TABLE IF NOT EXISTS "mw_log_test" (
				"facility" VARCHAR(32) NOT NULL,
				"request" VARCHAR(32) NOT NULL,
				"tstamp" VARCHAR(20) NOT NULL,
				"priority" INTEGER NOT NULL,
				"message" TEXT NOT NULL
			);' )->execute()->finish();

		$sql = 'INSERT INTO "mw_log_test" ( "facility", "tstamp", "priority", "message", "request" ) VALUES ( ?, ?, ?, ?, ? )';
		$this->object = new \Aimeos\MW\Logger\DB( $conn->create( $sql ) );

		$this->dbm->release( $conn );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		if( \TestHelper::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			return;
		}

		$this->dbm = \TestHelper::getDBManager();

		$conn = $this->dbm->acquire();
		$conn->create( 'DROP TABLE "mw_log_test"' )->execute()->finish();
		$this->dbm->release( $conn );
	}

	public function testLog()
	{
		$this->object->log( 'error' );

		$conn = $this->dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		$this->dbm->release( $conn );

		if( $row === false ) {
			throw new \Exception( 'No log record found' );
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
		$conn = $this->dbm->acquire();
		$conn->create( 'DELETE FROM "mw_log_test"' )->execute()->finish();

		$this->object->log( array ( 'scalar', 'errortest' ) );

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();

		$row = $result->fetch();

		$this->dbm->release( $conn );

		if( $row === false ) {
			throw new \Exception( 'No log record found' );
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

		$conn = $this->dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		$this->dbm->release( $conn );

		if( $row === false ) {
			throw new \Exception( 'No log record found' );
		}

		$this->assertEquals( 32, strlen( $row['request'] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $row['tstamp'] ) );
		$this->assertEquals( \Aimeos\MW\Logger\Base::CRIT, $row['priority'] );
		$this->assertEquals( 'critical', $row['message'] );
	}

	public function testLogWarn()
	{
		$this->object->log( 'debug', \Aimeos\MW\Logger\Base::WARN );

		$conn = $this->dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		$this->dbm->release( $conn );

		if( $row !== false ) {
			throw new \Exception( 'Log record found but none expected' );
		}
	}

	public function testFacility()
	{
		$this->object->log( 'user auth', \Aimeos\MW\Logger\Base::ERR, 'auth' );

		$conn = $this->dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		$this->dbm->release( $conn );

		if( $row === false ) {
			throw new \Exception( 'No log record found' );
		}

		$this->assertEquals( 'auth', $row['facility'] );
	}
}
