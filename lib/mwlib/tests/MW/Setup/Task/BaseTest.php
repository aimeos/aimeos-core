<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Setup\Task;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $dbm;
	private $object;


	protected function setUp() : void
	{
		$config = \TestHelperMw::getConfig();

		if( $config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}


		$this->dbm = \TestHelperMw::getDBManager();
		$schema = new \Aimeos\MW\Setup\DBSchema\Mysql( $this->dbm, 'db', $config->get( 'resource/db/database', 'notfound' ), 'mysql' );

		$this->object = new BaseImpl( $schema, $this->dbm->acquire() );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->dbm );
	}


	public function testGetPreDependencies()
	{
		$this->assertEquals( array( 'TestTask' ), $this->object->getPreDependencies() );
	}


	public function testGetPostDependencies()
	{
		$this->assertEquals( [], $this->object->getPostDependencies() );
	}


	public function testGetSchema()
	{
		$class = new \ReflectionClass( \Aimeos\MW\Setup\Task\Base::class );
		$method = $class->getMethod( 'getSchema' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( 'db' ) );

		$this->assertInstanceOf( \Aimeos\MW\Setup\DBSchema\Iface::class, $result );
	}


	public function testGetTableDefinitions()
	{
		$content = 'CREATE TABLE "test" ( "name" VARCHAR(32) )';

		$class = new \ReflectionClass( \Aimeos\MW\Setup\Task\Base::class );
		$method = $class->getMethod( 'getTableDefinitions' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( $content ) );

		$this->assertEquals( array( 'test' => 'CREATE TABLE "test" ( "name" VARCHAR(32) )' ), $result );
	}


	public function testGetIndexDefinitions()
	{
		$content = 'CREATE INDEX "idx_test" ON "test" ("name")';

		$class = new \ReflectionClass( \Aimeos\MW\Setup\Task\Base::class );
		$method = $class->getMethod( 'getIndexDefinitions' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( $content ) );

		$this->assertEquals( array( 'test.idx_test' => 'CREATE INDEX "idx_test" ON "test" ("name")' ), $result );
	}


	public function testGetTriggerDefinitions()
	{
		$content = 'CREATE TRIGGER "tri_test" ON "text"';

		$class = new \ReflectionClass( \Aimeos\MW\Setup\Task\Base::class );
		$method = $class->getMethod( 'getTriggerDefinitions' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( $content ) );

		$this->assertEquals( array( 'tri_test' => 'CREATE TRIGGER "tri_test" ON "text"' ), $result );
	}
}


class BaseImpl extends \Aimeos\MW\Setup\Task\Base
{
	public function getPreDependencies() : array
	{
		return array( 'TestTask' );
	}
}
