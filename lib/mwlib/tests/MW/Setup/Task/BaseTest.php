<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Task;


class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$config = \TestHelperMw::getConfig();

		if( $config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$dbm = \TestHelperMw::getDBManager();
		$conn = $dbm->acquire();

		$schema = new \Aimeos\MW\Setup\DBSchema\Mysql( $conn, $config->get( 'resource/db/database', 'notfound' ), 'mysql' );
		$this->object = new BaseImpl( $schema, $conn );

		$dbm->release( $conn );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetPreDependencies()
	{
		$this->assertEquals( array( 'TestTask' ), $this->object->getPreDependencies() );
	}


	public function testGetPostDependencies()
	{
		$this->assertEquals( [], $this->object->getPostDependencies() );
	}


	public function testMigrate()
	{
		$this->object->migrate();
	}


	public function testRollback()
	{
		$this->object->rollback();
	}


	public function testClean()
	{
		$this->object->clean();
	}


	public function testRun()
	{
		$this->object->run( 'mysql' );
	}


	public function testGetConnection()
	{
		$class = new \ReflectionClass( '\Aimeos\MW\Setup\Task\Base' );
		$method = $class->getMethod( 'getConnection' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( 'db' ) );

		$this->assertInstanceOf( '\Aimeos\MW\DB\Connection\Iface', $result );
	}


	public function testGetSchema()
	{
		$class = new \ReflectionClass( '\Aimeos\MW\Setup\Task\Base' );
		$method = $class->getMethod( 'getSchema' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( 'db' ) );

		$this->assertInstanceOf( '\Aimeos\MW\Setup\DBSchema\Iface', $result );
	}


	public function testGetTableDefinitions()
	{
		$content = 'CREATE TABLE "test" ( "name" VARCHAR(32) )';

		$class = new \ReflectionClass( '\Aimeos\MW\Setup\Task\Base' );
		$method = $class->getMethod( 'getTableDefinitions' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( $content ) );

		$this->assertEquals( array( 'test' => 'CREATE TABLE "test" ( "name" VARCHAR(32) )' ), $result );
	}


	public function testGetIndexDefinitions()
	{
		$content = 'CREATE INDEX "idx_test" ON "test" ("name")';

		$class = new \ReflectionClass( '\Aimeos\MW\Setup\Task\Base' );
		$method = $class->getMethod( 'getIndexDefinitions' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( $content ) );

		$this->assertEquals( array( 'test.idx_test' => 'CREATE INDEX "idx_test" ON "test" ("name")' ), $result );
	}


	public function testGetTriggerDefinitions()
	{
		$content = 'CREATE TRIGGER "tri_test" ON "text"';

		$class = new \ReflectionClass( '\Aimeos\MW\Setup\Task\Base' );
		$method = $class->getMethod( 'getTriggerDefinitions' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( $content ) );

		$this->assertEquals( array( 'tri_test' => 'CREATE TRIGGER "tri_test" ON "text"' ), $result );
	}
}


class BaseImpl extends \Aimeos\MW\Setup\Task\Base
{
	public function getPreDependencies()
	{
		return array( 'TestTask' );
	}

	public function getPostDependencies()
	{
		return [];
	}

	protected function mysql()
	{
		$this->execute( 'SELECT 1+1' );

		$list = array(
				'SELECT 1+2',
				'SELECT 1+3',
		);

		$this->executeList( $list );
	}
}
