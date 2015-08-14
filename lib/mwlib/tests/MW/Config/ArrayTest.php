<?php

/**
 * Test class for MW_Config_Array.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Config_ArrayTest extends PHPUnit_Framework_TestCase
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
		$dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles';
		$dir2 = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testowrite';

		$conf = array( 'resource' => array( 'db' => array( 'host' => '127.0.0.1' ) ) );
		$this->_object = new MW_Config_Array( $conf, array( $dir, $dir2 ) );
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
		$this->assertEquals( '127.0.0.1', $this->_object->get( 'resource/db/host' ) );

		$x = $this->_object->get( 'config/manager/default/select', 'defvalue1');
		$this->assertEquals( 'select11', $x );

		$x = $this->_object->get( 'config/provider/delivery/sh/select', 'defvalue2');
		$this->assertEquals( 'select2', $x );

		$x = $this->_object->get( 'subconfig/default/subitem/a/aa', 'defvalue3');
		$this->assertEquals( '111', $x );

		$x = $this->_object->get( 'subconfig/subsubconfig/default/subsubitem/aa/aaa', 'defvalue4');
		$this->assertEquals( '111', $x );

		$x = $this->_object->get( 'config/manager/default/select', 'defvalue5');
		$this->assertEquals( 'select11', $x );

		$x = $this->_object->get( 'subconfig/subsubconfig/default/subsubitem/aa/aaa', 'defvalue6');
		$this->assertEquals( '111', $x );

		$x = $this->_object->get( 'subconfig/default/subitem/a/aa', 'defvalue7');
		$this->assertEquals( '111', $x );

		$x = $this->_object->get( 'subconfig/default/subitem/a/bb', 'defvalue8');
		$this->assertEquals( 'defvalue8', $x );

		$x = $this->_object->get( 'nonsubconfig', 'defvalue9');
		$this->assertEquals( 'defvalue9', $x );

		$x = $this->_object->get( 'subconfig', 'defvalue10');
		$this->assertInternalType( 'array', $x );
	}

	public function testGetArray()
	{
		$this->assertEquals( array( 'host' => '127.0.0.1' ), $this->_object->get( 'resource/db/' ) );

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
			$this->_object->get( 'subconfig/default' )
		);
	}

	public function testGetDefault()
	{
		$this->assertEquals( 3306, $this->_object->get( 'resource/db/port', 3306 ) );
	}

	public function testSet()
	{
		$this->_object->set( 'resource/db/database', 'testdb' );
		$this->assertEquals( 'testdb', $this->_object->get( 'resource/db/database' ) );

		$this->_object->set( 'resource/foo', 'testdb' );
		$this->assertEquals( 'testdb', $this->_object->get( 'resource/foo' ) );

		$this->_object->set( 'resource/bar/db', 'testdb' );
		$this->assertEquals( 'testdb', $this->_object->get( 'resource/bar/db' ) );
	}

	public function testSetArray()
	{
		$this->_object->set( 'resource/ldap/', array( 'host' => 'localhost', 'port' => 389 ) );
		$this->assertEquals( array( 'host' => 'localhost', 'port' => 389 ), $this->_object->get( 'resource/ldap' ) );
	}

}
