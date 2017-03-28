<?php

namespace Aimeos\MW\Config\Decorator;


/**
 * Test class for \Aimeos\MW\Config\Decorator\Memory.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class MemoryTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$conf = new \Aimeos\MW\Config\PHPArray( [] );
		$this->object = new \Aimeos\MW\Config\Decorator\Memory( $conf );
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

	public function testGetLocal()
	{
		$conf = new \Aimeos\MW\Config\PHPArray( [] );
		$local = array( 'resource' => array( 'db' => array( 'host' => '127.0.0.1' ) ) );
		$this->object = new \Aimeos\MW\Config\Decorator\Memory( $conf, $local );

		$this->assertEquals( '127.0.0.1', $this->object->get( 'resource/db/host', '127.0.0.2' ) );
	}

	public function testGetDefault()
	{
		$this->assertEquals( 3306, $this->object->get( 'resource/db/port', 3306 ) );
	}

	public function testGetOverwrite()
	{
		$cfg = array( 'resource' => array( 'db' => array( 'database' => 'test' ) ) );
		$conf = new \Aimeos\MW\Config\PHPArray( $cfg );

		$local = array( 'resource' => array( 'db' => array( 'host' => '127.0.0.1' ) ) );
		$this->object = new \Aimeos\MW\Config\Decorator\Memory( $conf, $local );

		$result = $this->object->get( 'resource/db', [] );
		$this->assertArrayNotHasKey( 'database', $result );
		$this->assertArrayHasKey( 'host', $result );
	}
}
