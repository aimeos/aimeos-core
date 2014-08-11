<?php

/**
 * Test class for MW_Config_Decorator_APC.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Config_Decorator_APCTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		if( function_exists( 'apc_store' ) === false ) {
			$this->markTestSkipped( 'APC not installed' );
		}

		$conf = new MW_Config_Array( array() );
		$this->_object = new MW_Config_Decorator_APC( $conf, 'test:' );
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

	public function testGetSet()
	{
		$this->_object->set( 'resource/db/host', '127.0.0.1' );
		$this->assertEquals( '127.0.0.1', $this->_object->get( 'resource/db/host', '127.0.0.2' ) );
	}

	public function testGetDefault()
	{
		$this->assertEquals( 3306, $this->_object->get( 'resource/db/port', 3306 ) );
	}
}
