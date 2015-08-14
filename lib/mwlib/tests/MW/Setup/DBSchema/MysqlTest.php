<?php

/**
 * Test class for MW_Setup_DBSchema_Mysql.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Setup_DBSchema_MysqlTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_dbm;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$config = TestHelper::getConfig();

		if( $config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$this->_dbm = TestHelper::getDBManager();
		$conn = $this->_dbm->acquire();

		$sql = '
			CREATE TABLE IF NOT EXISTS "mw_setup_dbschema_test" (
				"integer" INTEGER NOT NULL AUTO_INCREMENT,
				"varchar16" VARCHAR(16) NOT NULL DEFAULT \'default\',
				"smallint" SMALLINT NOT NULL,
				"integernull" INTEGER,
			CONSTRAINT "unq_mwsdt_integer" UNIQUE ("integer")
			);
		';
		$conn->create( $sql )->execute()->finish();
		$conn->create( 'CREATE INDEX "idx_msdt_smallint" ON "mw_setup_dbschema_test" ("smallint")' )->execute()->finish();

		$this->_object = new MW_Setup_DBSchema_Mysql( $conn, $config->get( 'resource/db/database', 'notfound' ) );

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
		$this->_dbm = TestHelper::getDBManager();

		$conn = $this->_dbm->acquire();
		$conn->create( 'DROP INDEX "idx_msdt_smallint" ON "mw_setup_dbschema_test"' )->execute()->finish();
		$conn->create( 'DROP TABLE "mw_setup_dbschema_test"' )->execute()->finish();
		$this->_dbm->release( $conn );
	}

	public function testTableExists()
	{
		$this->assertTrue( $this->_object->tableExists( 'mw_setup_dbschema_test' ) );
		$this->assertFalse( $this->_object->tableExists( 'notexisting' ) );
	}

	public function testConstraintExists()
	{
		$this->assertTrue( $this->_object->constraintExists( 'mw_setup_dbschema_test', 'unq_mwsdt_integer' ) );
		$this->assertFalse( $this->_object->constraintExists( 'mw_setup_dbschema_test', 'notexisting' ) );
	}

	public function testColumnExists()
	{
		$this->assertTrue( $this->_object->columnExists( 'mw_setup_dbschema_test', 'integer' ) );
		$this->assertFalse( $this->_object->columnExists( 'mw_setup_dbschema_test', 'notexisting' ) );
	}

	public function testGetColumnDetails()
	{
		$columnItem = $this->_object->getColumnDetails( 'mw_setup_dbschema_test', 'varchar16' );
		$this->assertEquals( 'mw_setup_dbschema_test', $columnItem->getTableName() );
		$this->assertEquals( 'varchar16', $columnItem->getName() );
		$this->assertEquals( 'varchar', $columnItem->getDataType() );
		$this->assertEquals( 16, $columnItem->getMaxLength() );
		$this->assertEquals( 'default', $columnItem->getDefaultValue() );
		$this->assertFalse( $columnItem->isNullable() );

		$columnItem = $this->_object->getColumnDetails( 'mw_setup_dbschema_test', 'integernull' );
		$this->assertEquals( 'mw_setup_dbschema_test', $columnItem->getTableName() );
		$this->assertEquals( 'integernull', $columnItem->getName() );
		$this->assertEquals( 'int', $columnItem->getDataType() );
		$this->assertEquals( 10, $columnItem->getMaxLength() );
		$this->assertEquals( null, $columnItem->getDefaultValue() );
		$this->assertTrue( $columnItem->isNullable() );

		$this->setExpectedException('MW_Setup_Exception');
		$this->_object->getColumnDetails( 'mw_setup_dbschema_test', 'notexisting' );
	}

	public function testIndexExists()
	{
		$this->assertTrue( $this->_object->indexExists( 'mw_setup_dbschema_test', 'idx_msdt_smallint' ) );
		$this->assertFalse( $this->_object->indexExists( 'mw_setup_dbschema_test', 'notexisting' ) );
	}
}
