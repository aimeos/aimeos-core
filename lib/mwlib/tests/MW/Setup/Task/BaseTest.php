<?php

namespace Aimeos\MW\Setup\Task;


/**
 * Test class for \Aimeos\MW\Setup\Task\Base.
 *
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $object;


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


		$dbm = \TestHelperMw::getDBManager();
		$conn = $dbm->acquire();

		$schema = new \Aimeos\MW\Setup\DBSchema\Mysql( $conn, $config->get( 'resource/db/database', 'notfound' ) );
		$this->object = new BaseImpl( $schema, $conn );

		$dbm->release( $conn );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}

	public function testGetPreDependencies()
	{
		$this->assertEquals( array( 'TestTask' ), $this->object->getPreDependencies() );
	}

	public function testGetPostDependencies()
	{
		$this->assertEquals( array(), $this->object->getPostDependencies() );
	}

	public function testRun()
	{
		$this->object->run( 'mysql' );
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
		return array();
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
