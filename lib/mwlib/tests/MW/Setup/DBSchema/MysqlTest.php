<?php

namespace Aimeos\MW\Setup\DBSchema;


/**
 * Test class for \Aimeos\MW\Setup\DBSchema\Mysql.
 *
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class MysqlTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $dbm;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$config = \TestHelperMw::getConfig();

		if( $config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$this->dbm = \TestHelperMw::getDBManager();
		$conn = $this->dbm->acquire();

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

		$this->object = new \Aimeos\MW\Setup\DBSchema\Mysql( $conn, $config->get( 'resource/db/database', 'notfound' ) );

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
		$this->dbm = \TestHelperMw::getDBManager();

		$conn = $this->dbm->acquire();
		$conn->create( 'DROP INDEX "idx_msdt_smallint" ON "mw_setup_dbschema_test"' )->execute()->finish();
		$conn->create( 'DROP TABLE "mw_setup_dbschema_test"' )->execute()->finish();
		$this->dbm->release( $conn );
	}

	public function testTableExists()
	{
		$this->assertTrue( $this->object->tableExists( 'mw_setup_dbschema_test' ) );
		$this->assertFalse( $this->object->tableExists( 'notexisting' ) );
	}

	public function testConstraintExists()
	{
		$this->assertTrue( $this->object->constraintExists( 'mw_setup_dbschema_test', 'unq_mwsdt_integer' ) );
		$this->assertFalse( $this->object->constraintExists( 'mw_setup_dbschema_test', 'notexisting' ) );
	}

	public function testColumnExists()
	{
		$this->assertTrue( $this->object->columnExists( 'mw_setup_dbschema_test', 'integer' ) );
		$this->assertFalse( $this->object->columnExists( 'mw_setup_dbschema_test', 'notexisting' ) );
	}

	public function testGetColumnDetails()
	{
		$columnItem = $this->object->getColumnDetails( 'mw_setup_dbschema_test', 'varchar16' );
		$this->assertEquals( 'mw_setup_dbschema_test', $columnItem->getTableName() );
		$this->assertEquals( 'varchar16', $columnItem->getName() );
		$this->assertEquals( 'varchar', $columnItem->getDataType() );
		$this->assertEquals( 16, $columnItem->getMaxLength() );
		$this->assertEquals( 'default', $columnItem->getDefaultValue() );
		$this->assertFalse( $columnItem->isNullable() );

		$columnItem = $this->object->getColumnDetails( 'mw_setup_dbschema_test', 'integernull' );
		$this->assertEquals( 'mw_setup_dbschema_test', $columnItem->getTableName() );
		$this->assertEquals( 'integernull', $columnItem->getName() );
		$this->assertEquals( 'int', $columnItem->getDataType() );
		$this->assertEquals( 10, $columnItem->getMaxLength() );
		$this->assertEquals( null, $columnItem->getDefaultValue() );
		$this->assertTrue( $columnItem->isNullable() );

		$this->setExpectedException('\\Aimeos\\MW\\Setup\\Exception');
		$this->object->getColumnDetails( 'mw_setup_dbschema_test', 'notexisting' );
	}

	public function testIndexExists()
	{
		$this->assertTrue( $this->object->indexExists( 'mw_setup_dbschema_test', 'idx_msdt_smallint' ) );
		$this->assertFalse( $this->object->indexExists( 'mw_setup_dbschema_test', 'notexisting' ) );
	}
}
