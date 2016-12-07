<?php

namespace Aimeos\MW\Config;


/**
 * Test class for \Aimeos\MW\Config\PHPArray.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class PHPArrayTest extends \PHPUnit_Framework_TestCase
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
		$dir = __DIR__ . DIRECTORY_SEPARATOR . 'testfiles';
		$dir2 = __DIR__ . DIRECTORY_SEPARATOR . 'testowrite';

		$conf = array( 'resource' => array( 'db' => array( 'host' => '127.0.0.1' ) ) );
		$this->object = new \Aimeos\MW\Config\PHPArray( $conf, array( $dir, $dir2 ) );
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

	public function testGet()
	{
		$this->assertEquals( '127.0.0.1', $this->object->get( 'resource/db/host' ) );

		$x = $this->object->get( 'config/manager/default/select', 'defvalue1');
		$this->assertEquals( 'select11', $x );

		$x = $this->object->get( 'config/provider/delivery/sh/select', 'defvalue2');
		$this->assertEquals( 'select2', $x );

		$x = $this->object->get( 'subconfig/default/subitem/a/aa', 'defvalue3');
		$this->assertEquals( '111', $x );

		$x = $this->object->get( 'subconfig/subsubconfig/default/subsubitem/aa/aaa', 'defvalue4');
		$this->assertEquals( '111', $x );

		$x = $this->object->get( 'config/manager/default/select', 'defvalue5');
		$this->assertEquals( 'select11', $x );

		$x = $this->object->get( 'subconfig/subsubconfig/default/subsubitem/aa/aaa', 'defvalue6');
		$this->assertEquals( '111', $x );

		$x = $this->object->get( 'subconfig/default/subitem/a/aa', 'defvalue7');
		$this->assertEquals( '111', $x );

		$x = $this->object->get( 'subconfig/default/subitem/a/bb', 'defvalue8');
		$this->assertEquals( 'defvalue8', $x );

		$x = $this->object->get( 'nonsubconfig', 'defvalue9');
		$this->assertEquals( 'defvalue9', $x );

		$x = $this->object->get( 'subconfig', 'defvalue10');
		$this->assertInternalType( 'array', $x );
	}

	public function testGetArray()
	{
		$this->assertEquals( array( 'host' => '127.0.0.1' ), $this->object->get( 'resource/db/' ) );

		$this->assertEquals(
			array(
				'subitem' => array (
					'a' => array(
						'aa' => '111',
					),
				),
				'subbla' => array(
					'b' => array (
						'bb' => '22',
					),
				),
			),
			$this->object->get( 'subconfig/default' )
		);
	}

	public function testGetDefault()
	{
		$this->assertEquals( 3306, $this->object->get( 'resource/db/port', 3306 ) );
	}

	public function testSet()
	{
		$this->object->set( 'resource/db/database', 'testdb' );
		$this->assertEquals( 'testdb', $this->object->get( 'resource/db/database' ) );

		$this->object->set( 'resource/foo', 'testdb' );
		$this->assertEquals( 'testdb', $this->object->get( 'resource/foo' ) );

		$this->object->set( 'resource/bar/db', 'testdb' );
		$this->assertEquals( 'testdb', $this->object->get( 'resource/bar/db' ) );
	}

	public function testSetArray()
	{
		$this->object->set( 'resource/ldap/', array( 'host' => 'localhost', 'port' => 389 ) );
		$this->assertEquals( array( 'host' => 'localhost', 'port' => 389 ), $this->object->get( 'resource/ldap' ) );
	}

}
