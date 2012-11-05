<?php

/**
 * Test class for MW_Logger_DB.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Logger_DBTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MW_Logger_DB
	 * @access protected
	 */
	protected $_object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( MW_TestHelper::getConfig()->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$this->_dbm = MW_TestHelper::getDBManager();

		$conn = $this->_dbm->acquire();

		$conn->create( '
			CREATE TABLE IF NOT EXISTS "mw_log_test" (
				"facility" VARCHAR(32) NOT NULL,
				"request" VARCHAR(32) NOT NULL,
				"tstamp" VARCHAR(20) NOT NULL,
				"priority" INTEGER NOT NULL,
				"message" TEXT NOT NULL
			);' )->execute()->finish();

		$sql = 'INSERT INTO "mw_log_test" ( "facility", "tstamp", "priority", "message", "request" ) VALUES ( ?, ?, ?, ?, ? )';
		$this->_object = new MW_Logger_DB( $conn->create( $sql ) );

		$this->_dbm->release( $conn );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$conn = $this->_dbm->acquire();

		$conn->create( 'DROP TABLE "mw_log_test"' )->execute()->finish();

		$this->_dbm->release( $conn );
	}

	public function testLog()
	{
		$this->_object->log( 'error' );

		$conn = $this->_dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		$this->_dbm->release( $conn );

		if( $row === false ) {
			throw new Exception( 'No log record found' );
		}

		$this->assertEquals( 'message', $row['facility'] );
		$this->assertEquals( 32, strlen( $row['request'] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $row['tstamp'] ) );
		$this->assertEquals( MW_Logger_Abstract::ERR, $row['priority'] );
		$this->assertEquals( 'error', $row['message'] );


		$this->setExpectedException('MW_Logger_Exception');
		$this->_object->log( 'wrong log level', -1);
	}

	public function testScalarLog()
	{
		$conn = $this->_dbm->acquire();
		$conn->create( 'DELETE FROM "mw_log_test"' )->execute()->finish();

		$this->_object->log( array ( 'scalar', 'errortest' ) );

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();

		$row = $result->fetch();

		$this->_dbm->release( $conn );

		if( $row === false ) {
			throw new Exception( 'No log record found' );
		}

		$this->assertEquals( 'message', $row['facility'] );
		$this->assertEquals( 32, strlen( $row['request'] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $row['tstamp'] ) );
		$this->assertEquals( MW_Logger_Abstract::ERR, $row['priority'] );
		$this->assertEquals( '["scalar","errortest"]', $row['message'] );
	}

	public function testLogCrit()
	{
		$this->_object->log( 'critical', MW_Logger_Abstract::CRIT );

		$conn = $this->_dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		$this->_dbm->release( $conn );

		if( $row === false ) {
			throw new Exception( 'No log record found' );
		}

		$this->assertEquals( 32, strlen( $row['request'] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $row['tstamp'] ) );
		$this->assertEquals( MW_Logger_Abstract::CRIT, $row['priority'] );
		$this->assertEquals( 'critical', $row['message'] );
	}

	public function testLogWarn()
	{
		$this->_object->log( 'debug', MW_Logger_Abstract::WARN );

		$conn = $this->_dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		$this->_dbm->release( $conn );

		if( $row !== false ) {
			throw new Exception( 'Log record found but none expected' );
		}
	}

	public function testFacility()
	{
		$this->_object->log( 'user auth', MW_Logger_Abstract::ERR, 'auth' );

		$conn = $this->_dbm->acquire();

		$result = $conn->create( 'SELECT * FROM "mw_log_test"' )->execute();
		$row = $result->fetch();

		$this->_dbm->release( $conn );

		if( $row === false ) {
			throw new Exception( 'No log record found' );
		}

		$this->assertEquals( 'auth', $row['facility'] );
	}
}
