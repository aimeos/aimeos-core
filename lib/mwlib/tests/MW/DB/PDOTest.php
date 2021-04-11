<?php

namespace Aimeos\MW\DB;


class PDOTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $config;
	private $conn;


	protected function setUp() : void
	{
		$this->config = \TestHelperMw::getConfig();
		$this->object = new \Aimeos\MW\DB\Manager\PDO( $this->config );

		$sql = 'CREATE TABLE "mw_unit_test" ( "id" INTEGER NOT NULL, "name" VARCHAR(20) NOT NULL )';

		$this->conn = $this->object->acquire();
		$this->conn->create( $sql )->execute()->finish();
	}


	protected function tearDown() : void
	{
		$sql = 'DROP TABLE "mw_unit_test"';

		$this->conn->create( $sql )->execute()->finish();
		$this->object->release( $this->conn );

		unset( $this->object );
	}


	public function testTransactionCommit()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (1, \'a\')';
		$sqlselect = 'SELECT "name" FROM "mw_unit_test" WHERE "name" = \'a\'';

		$this->conn->begin();
		$stmt = $this->conn->create( $sqlinsert );
		$stmt->execute()->finish();
		$stmt->execute()->finish();
		$this->conn->commit();

		$result = $this->conn->create( $sqlselect )->execute();

		$rows = [];
		while( ( $row = $result->fetch() ) !== null ) {
			$rows[] = $row;
		}

		$result->finish();

		$this->assertEquals( 2, count( $rows ) );
	}


	public function testTransactionCommitMultiple()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (1, \'a\')';
		$sqlselect = 'SELECT "name" FROM "mw_unit_test" WHERE "name" = \'a\'';

		$stmt = $this->conn->create( $sqlinsert );

		$this->conn->begin();
		$stmt->execute()->finish();
		$this->conn->commit();

		$this->conn->begin();
		$stmt->execute()->finish();
		$this->conn->commit();

		$result = $this->conn->create( $sqlselect )->execute();

		$rows = [];
		while( ( $row = $result->fetch() ) !== null ) {
			$rows[] = $row;
		}

		$result->finish();

		$this->assertEquals( 2, count( $rows ) );
	}


	public function testTransactionRollback()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (1, \'a\')';
		$sqlselect = 'SELECT "name" FROM "mw_unit_test" WHERE "name" = \'a\'';

		$this->conn->begin();
		$stmt = $this->conn->create( $sqlinsert );
		$stmt->execute()->finish();
		$stmt->execute()->finish();
		$this->conn->rollback();

		$result = $this->conn->create( $sqlselect )->execute();

		$rows = [];
		while( ( $row = $result->fetch() ) !== null ) {
			$rows[] = $row;
		}

		$result->finish();

		$this->assertEquals( 0, count( $rows ) );
	}


	public function testTransactionStackCommit()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (1, \'a\')';
		$sqlselect = 'SELECT "name" FROM "mw_unit_test" WHERE "name" = \'a\'';

		$this->conn->begin();
		$this->conn->begin();

		$stmt = $this->conn->create( $sqlinsert );
		$stmt->execute()->finish();

		$this->conn->commit();
		$this->conn->commit();

		$result = $this->conn->create( $sqlselect )->execute();

		$rows = [];
		while( ( $row = $result->fetch() ) !== null ) {
			$rows[] = $row;
		}

		$result->finish();

		$this->assertEquals( 1, count( $rows ) );
	}


	public function testTransactionStackRollback()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (1, \'a\')';
		$sqlselect = 'SELECT "name" FROM "mw_unit_test" WHERE "name" = \'a\'';

		$this->conn->begin();
		$this->conn->begin();

		$stmt = $this->conn->create( $sqlinsert );
		$stmt->execute()->finish();

		$this->conn->rollback();
		$this->conn->rollback();

		$result = $this->conn->create( $sqlselect )->execute();

		$rows = [];
		while( ( $row = $result->fetch() ) !== null ) {
			$rows[] = $row;
		}

		$result->finish();

		$this->assertEquals( 0, count( $rows ) );
	}


	public function testAffectedRows()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (1, \'a\')';

		$result = $this->conn->create( $sqlinsert )->execute();
		$rows = $result->affectedRows();
		$result->finish();

		$this->assertEquals( 1, $rows );
	}


	public function testStmtEscape()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (1, :value)';

		$value = "(\\')";
		$sqlinsert = str_replace( ':value', '\'' . $this->conn->escape( $value ) . '\'', $sqlinsert );
		$stmt = $this->conn->create( $sqlinsert );
		$stmt->execute()->finish();

		$this->assertRegexp( '/^INSERT INTO "mw_unit_test" \("id", "name"\) VALUES \(1, \'\(.*\\\'\)\'\)$/', strval( $stmt ) );
	}


	public function testStmtSimpleBindApostrophes()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (1, \'' . $this->conn->escape( '\'\'' ) . '\')';

		$result = $this->conn->create( $sqlinsert )->execute()->finish();

		$this->assertInstanceOf( \Aimeos\MW\DB\Result\Iface::class, $result );
	}


	public function testStmtSimpleBindOne()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (1, ?)';

		$stmt = $this->conn->create( $sqlinsert );
		$stmt->bind( 1, 'test' );
		$result = $stmt->execute()->finish();

		$this->assertInstanceOf( \Aimeos\MW\DB\Result\Iface::class, $result );
	}


	public function testStmtSimpleBindTwo()
	{
		$sqlinsert2 = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';

		$stmt2 = $this->conn->create( $sqlinsert2 );
		$stmt2->bind( 1, 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$stmt2->bind( 2, 0.12 );
		$result = $stmt2->execute()->finish();

		$this->assertInstanceOf( \Aimeos\MW\DB\Result\Iface::class, $result );
	}


	public function testStmtSimpleBindThree()
	{
		$sqlinsert3 = 'INSERT INTO "mw_unit_test" ("name", "id") VALUES (\'?te?st?\', ?)';

		$stmt2 = $this->conn->create( $sqlinsert3 );
		$stmt2->bind( 1, 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$result = $stmt2->execute()->finish();

		$this->assertInstanceOf( \Aimeos\MW\DB\Result\Iface::class, $result );
	}


	public function testStmtSimpleInvalidBindParamType()
	{
		$sqlinsert2 = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';

		$stmt2 = $this->conn->create( $sqlinsert2 );
		$stmt2->bind( 1, 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$stmt2->bind( 2, 0.15, 123 );

		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$stmt2->execute();
	}


	public function testStmtSimpleBindInvalid()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("name") VALUES (?)';

		$stmt = $this->conn->create( $sqlinsert );

		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$stmt->execute();
	}


	public function testResultFetch()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';
		$sqlselect = 'SELECT * FROM "mw_unit_test"';

		$stmt = $this->conn->create( $sqlinsert );
		$stmt->bind( 1, 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$stmt->bind( 2, 'test' );
		$stmt->execute()->finish();

		$stmt = $this->conn->create( $sqlselect );
		$result = $stmt->execute();
		$row = $result->fetch();
		$result->finish();

		$this->assertEquals( array( 'id' => 1, 'name' => 'test' ), $row );
	}


	public function testResultFetchAll()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';
		$sqlselect = 'SELECT * FROM "mw_unit_test"';

		$stmt = $this->conn->create( $sqlinsert );
		$stmt->bind( 1, 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$stmt->bind( 2, 'test' );
		$stmt->execute()->finish();

		$stmt = $this->conn->create( $sqlselect );
		$result = $stmt->execute();
		$rows = $result->all();
		$result->finish();

		$this->assertEquals( [['id' => 1, 'name' => 'test']], $rows );
	}


	public function testMultipleResults()
	{
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';
		$sqlselect = 'SELECT * FROM "mw_unit_test"';

		$stmt = $this->conn->create( $sqlinsert );
		$stmt->bind( 1, 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$stmt->bind( 2, 'test' );
		$stmt->execute()->finish();

		$stmt->bind( 1, 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$stmt->bind( 2, 1, \Aimeos\MW\DB\Statement\Base::PARAM_BOOL );
		$stmt->execute()->finish();

		$stmt->bind( 1, 123, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$stmt->bind( 2, 0.1, \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT );
		$stmt->execute()->finish();


		$stmt = $this->conn->create( $sqlselect );
		$result = $stmt->execute();
		$resultSets = [];

		do {
			$resultSets[] = $result->fetch();
		}
		while( $result->nextResult() !== false );

		$result->finish();

		$this->assertEquals( array( array( 'id' => 1, 'name' => 'test' ) ), $resultSets );
	}


	public function testWrongFieldType()
	{
		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$sqlinsert = 'INSERT INTO "mw_unit_test" ("id", "name") VALUES (?, ?)';

		$stmt = $this->conn->create( $sqlinsert );
		$stmt->bind( 1, 1, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
		$stmt->bind( 2, 'test', 123 );

		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$stmt->execute();
	}


	public function testGetRawObject()
	{
		$this->assertInstanceOf( \PDO::class, $this->conn->getRawObject() );
	}


	public function testNonExisting()
	{
		$sql = 'SELECT * FROM "mw_non_existing"';

		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$this->conn->create( $sql )->execute()->finish();
	}


	public function testSqlError()
	{
		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$this->conn->create( 'SELECT *' )->execute()->finish();
	}


	public function testPDOException()
	{
		$mock = $this->getMockBuilder( \Aimeos\MW\DB\Connection\Iface::class )->getMock();

		$this->expectException( \Aimeos\MW\DB\Exception::class );
		$this->object->release( $mock );
	}


	public function testDBFactory()
	{
		$this->assertInstanceOf( \Aimeos\MW\DB\Manager\Iface::class, $this->object );
	}


	public function testFactoryFail()
	{
		$this->expectException( \Aimeos\MW\DB\Exception::class );
		\Aimeos\MW\DB\Factory::create( \TestHelperMw::getConfig(), 'notDefined' );
	}
}
