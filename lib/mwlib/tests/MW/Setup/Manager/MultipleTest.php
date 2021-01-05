<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MW\Setup\Manager;


class MultipleTest extends \PHPUnit\Framework\TestCase
{
	private $config;
	private $dbm;


	protected function setUp() : void
	{
		$this->config = \TestHelperMw::getConfig();

		if( $this->config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}

		$this->dbm = \TestHelperMw::getDBManager();
	}


	protected function tearDown() : void
	{
		unset( $this->dbm );
	}


	public function testMigrate()
	{
		$taskPath = __DIR__ . DIRECTORY_SEPARATOR . 'tasks';
		$conf = array( 'db' => $this->config->get( 'resource/db', [] ) );
		$object = new \Aimeos\MW\Setup\Manager\Multiple( $this->dbm, $conf, $taskPath );

		ob_start();

		$object->migrate();

		$result = ob_get_contents();
		ob_end_clean();

		$this->assertStringContainsString( 'OneTask', $result );
		$this->assertStringContainsString( 'TwoTask', $result );
	}
}
