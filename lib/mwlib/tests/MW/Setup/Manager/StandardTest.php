<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Setup\Manager;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $config;
	private $dbm;


	protected function setUp()
	{
		$this->config = \TestHelperMw::getConfig();

		if( $this->config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}

		$this->dbm = \TestHelperMw::getDBManager();
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
