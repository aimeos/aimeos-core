<?php

namespace Aimeos\MW\Setup\Manager;


/**
 * Test class for \Aimeos\MW\Setup\Manager\Standard.
 *
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $config;
	private $dbm;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->config = \TestHelperMw::getConfig();

		if( $this->config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}

		$this->dbm = \TestHelperMw::getDBManager();
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

	public function testRun()
	{
		$expected = '
Executing OneTask                                                     OK
Executing TwoTask                                                     OK
';

		$conn = $this->dbm->acquire();

		$taskPath = __DIR__ . DIRECTORY_SEPARATOR . 'tasks';
		$object = new \Aimeos\MW\Setup\Manager\Standard( $conn, $this->config->get( 'resource/db', array() ), $taskPath );

		$this->dbm->release( $conn );

		ob_start();

		$object->run( 'mysql' );

		$result = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $expected, $result );
	}

	public function testRun2()
	{
		$expected = '
Executing OneTask                                                     OK
Executing TwoTask                                                     OK
Executing ThreeTask                                                   OK
';

		$conn = $this->dbm->acquire();

		$taskPath =  array(
			__DIR__ . DIRECTORY_SEPARATOR . 'tasks',
			__DIR__ . DIRECTORY_SEPARATOR . 'tasks2',
		);

		$object = new \Aimeos\MW\Setup\Manager\Standard( $conn, $this->config->get( 'resource/db', array() ), $taskPath );

		$this->dbm->release( $conn );

		ob_start();

		$object->run( 'mysql' );

		$result = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $expected, $result );
	}
}
