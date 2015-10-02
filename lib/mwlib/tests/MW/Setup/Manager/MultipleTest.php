<?php

/**
 * Test class for MW_Setup_Manager_Multiple.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Setup_Manager_MultipleTest extends PHPUnit_Framework_TestCase
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
		$this->config = TestHelper::getConfig();

		if( $this->config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}

		$this->dbm = TestHelper::getDBManager();
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

		$taskPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tasks';
		$object = new MW_Setup_Manager_Default( $conn, $this->config->get( 'resource/db', array() ), $taskPath );

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
			dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tasks',
			dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tasks2',
		);

		$object = new MW_Setup_Manager_Default( $conn, $this->config->get( 'resource/db', array() ), $taskPath );

		$this->dbm->release( $conn );

		ob_start();

		$object->run( 'mysql' );

		$result = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $expected, $result );
	}
}
