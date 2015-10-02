<?php

/**
 * Test class for MW_Config_Decorator_Memory.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Config_Decorator_MemoryTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$conf = new MW_Config_Array( array() );
		$this->object = new MW_Config_Decorator_Memory( $conf );
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
		$this->object->set( 'resource/db/host', '127.0.0.1' );
		$this->assertEquals( '127.0.0.1', $this->object->get( 'resource/db/host', '127.0.0.2' ) );
	}

	public function testGetCached()
	{
		$conf = new MW_Config_Array( array() );
		$cached = array( 'resource' => array( 'db' => array( 'host' => '127.0.0.1' ) ) );
		$this->object = new MW_Config_Decorator_Memory( $conf, $cached );

		$this->assertEquals( '127.0.0.1', $this->object->get( 'resource/db/host', '127.0.0.2' ) );
	}

	public function testGetDefault()
	{
		$this->assertEquals( 3306, $this->object->get( 'resource/db/port', 3306 ) );
	}
}
