<?php

/**
 * Test class for MW_DB_Manager_PDO.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_DB_PDOTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_config;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		$suite  = new PHPUnit_Framework_TestSuite('MW_DB_PDOTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_config = TestHelper::getConfig();

		if( ( $adapter = $this->_config->get( 'resource/db/adapter', false ) ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$this->_object = new MW_DB_Manager_PDO( $this->_config );

		if( $adapter == 'mysql' ) {
			$sql = 'CREATE TABLE "mw_unit_test" ( "id" INT NOT NULL PRIMARY KEY AUTO_INCREMENT, "name" VARCHAR(20) NOT NULL ) ENGINE=InnoDB';
		} else {
			$sql = 'CREATE TABLE "mw_unit_test" ( "id" INT NOT NULL PRIMARY KEY AUTO_INCREMENT, "name" VARCHAR(20) NOT NULL )';
		}

		$conn = $this->_object->acquire();
		$conn->create( $sql )->execute()->finish();
		$this->_object->release( $conn );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = new MW_DB_Manager_PDO( $this->_config );
		$sql = 'DROP TABLE "mw_unit_test"';

		$conn = $this->_object->acquire();
		$conn->create( $sql )->execute()->finish();
		$this->_object->release( $conn );
	}

	public function testTransactionCommit()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("name") VALUES (1)';
		$sqlselect = 'SELECT "name" FROM "mw_unit_test" WHERE "name" = 1';

		$conn = $this->_object->acquire();

		$conn->begin();
		$stmt = $conn->create( $sqlinsert );
		$stmt->execute()->finish();
		$stmt->execute()->finish();
		$conn->commit();

		$result = $conn->create( $sqlselect )->execute();

		$rows = array();
		while( ( $row = $result->fetch() ) !== false ) {
			$rows[] = $row;
		}

		$result->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 2, count( $rows ) );
	}

	public function testTransactionCommitMultiple()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("name") VALUES (1)';
		$sqlselect = 'SELECT "name" FROM "mw_unit_test" WHERE "name" = 1';

		$conn = $this->_object->acquire();

		$stmt = $conn->create( $sqlinsert );

		$conn->begin();
		$stmt->execute()->finish();
		$conn->commit();

		$conn->begin();
		$stmt->execute()->finish();
		$conn->commit();

		$result = $conn->create( $sqlselect )->execute();

		$rows = array();
		while( ( $row = $result->fetch() ) !== false ) {
			$rows[] = $row;
		}

		$result->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 2, count( $rows ) );
	}

	public function testTransactionRollback()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("name") VALUES (1)';
		$sqlselect = 'SELECT "name" FROM "mw_unit_test" WHERE "name" = 1';

		$conn = $this->_object->acquire();

		$conn->begin();
		$stmt = $conn->create( $sqlinsert );
		$stmt->execute()->finish();
		$stmt->execute()->finish();
		$conn->rollback();

		$result = $conn->create( $sqlselect )->execute();

		$rows = array();
		while( ( $row = $result->fetch() ) !== false ) {
			$rows[] = $row;
		}

		$result->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 0, count( $rows ) );
	}

	public function testTransactionStackCommit()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("name") VALUES (1)';
		$sqlselect = 'SELECT "name" FROM "mw_unit_test" WHERE "name" = 1';

		$conn = $this->_object->acquire();

		$conn->begin();
		$conn->begin();

		$stmt = $conn->create( $sqlinsert );
		$stmt->execute()->finish();

		$conn->rollback();
		$conn->commit();

		$result = $conn->create( $sqlselect )->execute();

		$rows = array();
		while( ( $row = $result->fetch() ) !== false ) {
			$rows[] = $row;
		}

		$result->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 1, count( $rows ) );
	}

	public function testTransactionStackRollback()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("name") VALUES (1)';
		$sqlselect = 'SELECT "name" FROM "mw_unit_test" WHERE "name" = 1';

		$conn = $this->_object->acquire();

		$conn->begin();
		$conn->begin();

		$stmt = $conn->create( $sqlinsert );
		$stmt->execute()->finish();

		$conn->commit();
		$conn->rollback();

		$result = $conn->create( $sqlselect )->execute();

		$rows = array();
		while( ( $row = $result->fetch() ) !== false ) {
			$rows[] = $row;
		}

		$result->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 0, count( $rows ) );
	}

	public function testAffectedRows()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("name") VALUES (1)';

		$conn = $this->_object->acquire();

		$result = $conn->create( $sqlinsert )->execute();
		$rows = $result->affectedRows();
		$result->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 1, $rows );
	}

	public function testStmtSimpleBindOne()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("name") VALUES (?)';

		$conn = $this->_object->acquire();

		$stmt = $conn->create( $sqlinsert );
		$stmt->bind( 1, 'test' );
		$stmt->execute()->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 'INSERT INTO "mw_unit_test" ("name") VALUES (\'test\')', strval( $stmt ) );
	}

	public function testStmtSimpleBindTwo()
	{
		$sqlinsert2 =  'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';

		$conn = $this->_object->acquire();

		$stmt2 = $conn->create( $sqlinsert2 );
		$stmt2->bind( 1, null, MW_DB_Statement_Abstract::PARAM_NULL);
		$stmt2->bind( 2, 0.12, MW_DB_Statement_Abstract::PARAM_FLOAT);
		$stmt2->execute()->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (NULL, 0.12)', strval( $stmt2 ) );
	}

	public function testStmtSimpleBindThree()
	{
		$sqlinsert3 =  'INSERT INTO "mw_unit_test" ("name", "id") VALUES (\'?te?st?\', ?)';

		$conn = $this->_object->acquire();

		$stmt2 = $conn->create( $sqlinsert3 );
		$stmt2->bind( 1, null, MW_DB_Statement_Abstract::PARAM_NULL);
		$stmt2->execute()->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 'INSERT INTO "mw_unit_test" ("name", "id") VALUES (\'?te?st?\', NULL)', strval( $stmt2 ) );
	}

	public function testStmtSimpleBindParamType()
	{
		$sqlinsert2 =  'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';

		$conn = $this->_object->acquire();

		$stmt2 = $conn->create( $sqlinsert2 );
		$stmt2->bind( 1, 0, MW_DB_Statement_Abstract::PARAM_NULL);
		$stmt2->bind( 2, 0.15, 123);
		$result = $stmt2->execute();
		$rows = $result->affectedRows();
		$result->finish();

		$this->assertEquals( 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (NULL, 0.15)', strval( $stmt2 ) );
		$this->assertEquals( 1, $rows );
	}

	public function testStmtSimpleBindInvalid()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("name") VALUES (?)';

		$conn = $this->_object->acquire();

		try {
			$stmt = $conn->create( $sqlinsert );
			$result = $stmt->execute();
		} catch ( MW_DB_Exception $de ) {
			$this->_object->release( $conn );
			return;
		}

		$this->_object->release( $conn );
		$this->fail('An expected exception has not been raised');
	}

	public function testStmtPreparedBindOne()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("name") VALUES (?)';

		$conn = $this->_object->acquire();

		$stmt = $conn->create( $sqlinsert, MW_DB_Connection_Abstract::TYPE_PREP );
		$stmt->bind( 1, 'test' );
		$result = $stmt->execute();
		$rows = $result->affectedRows();
		$result->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 1, $rows );
	}

	public function testStmtPreparedBindTwo()
	{
		$sqlinsert2 = 'INSERT INTO "mw_unit_test" ("name", "id") VALUES (\'?te?st?\', ?)';

		$conn = $this->_object->acquire();

		$stmt = $conn->create( $sqlinsert2, MW_DB_Connection_Abstract::TYPE_PREP );
		$stmt->bind( 1, null );
		$result = $stmt->execute();
		$rows = $result->affectedRows();
		$result->finish();

		$this->_object->release( $conn );

		$this->assertEquals( 1, $rows );
	}

	public function testResultFetch()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';
		$sqlselect = 'SELECT * FROM "mw_unit_test"';

		$conn = $this->_object->acquire();

		$stmt = $conn->create( $sqlinsert, MW_DB_Connection_Abstract::TYPE_PREP );
		$stmt->bind( 1, 1 );
		$stmt->bind( 2, 'test' );
		$stmt->execute()->finish();

		$stmt = $conn->create( $sqlselect );
		$result = $stmt->execute();
		$row = $result->fetch();
		$result->finish();

		$this->_object->release( $conn );

		$this->assertEquals( array( 'id' => 1, 'name' => 'test' ), $row );
	}

	public function testMultipleResults()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';
		$sqlselect = 'SELECT * FROM "mw_unit_test"; SELECT * FROM "mw_unit_test"';

		$conn = $this->_object->acquire();

		$stmt = $conn->create( $sqlinsert, MW_DB_Connection_Abstract::TYPE_PREP );
		$stmt->bind( 1, 1 );
		$stmt->bind( 2, 'test' );
		$stmt->execute()->finish();

		$stmt->bind( 1, null, MW_DB_Statement_Abstract::PARAM_NULL );
		$stmt->bind( 2, 1, MW_DB_Statement_Abstract::PARAM_BOOL );
		$stmt->execute()->finish();

		$stmt->bind( 1, 123, MW_DB_Statement_Abstract::PARAM_LOB );
		$stmt->bind( 2, 0.1, MW_DB_Statement_Abstract::PARAM_FLOAT );
		$stmt->execute()->finish();


		$stmt = $conn->create( $sqlselect );
		$result = $stmt->execute();
		$resultSets = array();

		do {
			$resultSets[] = $result->fetch();
		}
		while( $result->nextResult() !== false );

		$result->finish();

		$this->_object->release( $conn );


		$expected = array(
			array( 'id' => 1, 'name' => 'test' ),
			array( 'id' => 1, 'name' => 'test' ),
		);
		$this->assertEquals( $expected, $resultSets );
	}

	public function testWrongFieldType()
	{
		$this->setExpectedException('MW_DB_Exception');
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';

		$conn = $this->_object->acquire();

		try
		{
			$stmt = $conn->create( $sqlinsert, MW_DB_Connection_Abstract::TYPE_PREP );
			$stmt->bind( 1, 1 );
			$stmt->bind( 2, 'test', 123 );
		}
		catch ( MW_DB_Exception $e )
		{
			$this->_object->release( $conn );
			throw $e;
		}
	}

	public function testNonExisting()
	{
		$sql = 'SELECT * FROM "mw_non_existing"';

		$conn = $this->_object->acquire();

		$this->setExpectedException('MW_DB_Exception');

		try
		{
			$stmt = $conn->create( $sql )->execute()->finish();
		}
		catch ( MW_DB_Exception $e )
		{
			$this->_object->release( $conn );
			throw $e;
		}
	}

	public function testSqlError()
	{
		$conn = $this->_object->acquire();

		$this->setExpectedException('MW_DB_Exception');

		try
		{
			$stmt = $conn->create( 'SELECT *' )->execute()->finish();
		}
		catch ( MW_DB_Exception $e )
		{
			$this->_object->release( $conn );
			throw $e;
		}
	}

	public function testWrongStmtType()
	{
		$sql = 'SELECT * FROM "mw_unit_test"';

		$conn = $this->_object->acquire();

		$this->setExpectedException('MW_DB_Exception');

		try
		{
			$stmt = $conn->create( $sql, 123 );
		}
		catch (MW_DB_Exception $e)
		{
			$this->_object->release( $conn );
			throw $e;
		}
	}

	public function testPDOException()
	{
		$this->setExpectedException('MW_DB_Exception');
		$conn = new MW_DB_Connection_TestForPDOException();
		$this->_object->release($conn);
	}

	public function testDBFactory()
	{
		$this->assertInstanceOf('MW_DB_Manager_Interface', $this->_object);
	}

	public function testFactoryFail()
	{
		$this->setExpectedException('MW_DB_Exception');
		$this->_object = MW_DB_Factory::createManager( TestHelper::getConfig(), 'notDefined' );
	}
}



class MW_DB_Connection_TestForPDOException implements MW_DB_Connection_Interface
{
	public function create($sql, $type = MW_DB_Connection_Abstract::TYPE_SIMPLE)
	{
	}

	public function escape($data)
	{
	}

	public function begin()
	{
	}

	public function commit()
	{
	}

	public function rollback()
	{
	}
}
