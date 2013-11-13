<?php

/**
 * Test class for MW_Setup_Manager_Default.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Setup_Manager_DefaultTest extends MW_Unittest_Testcase
{
	private $_config;
	private $_dbm;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_config = TestHelper::getConfig();

		if( $this->_config->get( 'resource/db/adapter', false ) === false ) {
			$this->markTestSkipped( 'No database configured' );
		}

		$this->_dbm = TestHelper::getDBManager();
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

		$conn = $this->_dbm->acquire();

		$taskPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tasks';
		$object = new MW_Setup_Manager_Default( $conn, $this->_config->get( 'resource/db', array() ), $taskPath );

		$this->_dbm->release( $conn );

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

		$conn = $this->_dbm->acquire();

		$taskPath =  array(
			dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tasks',
			dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tasks2',
		);

		$object = new MW_Setup_Manager_Default( $conn, $this->_config->get( 'resource/db', array() ), $taskPath );

		$this->_dbm->release( $conn );

		ob_start();

		$object->run( 'mysql' );

		$result = ob_get_contents();
		ob_end_clean();

		$this->assertEquals( $expected, $result );
	}
}
