<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class MW_View_Helper_Request_DefaultTest extends PHPUnit_Framework_TestCase
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
		$view = new MW_View_Default();
		$this->object = new MW_View_Helper_Request_Default( $view, 'body', '127.0.0.1' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertInstanceOf( 'MW_View_Helper_Interface', $this->object->transform() );
	}


	public function testGetBody()
	{
		$this->assertEquals( 'body', $this->object->transform()->getBody() );
	}


	public function testGetClientAddress()
	{
		$this->assertEquals( '127.0.0.1', $this->object->transform()->getClientAddress() );
	}

}
