<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Controller_Frontend_ExceptionTest extends PHPUnit_Framework_TestCase
{
	private $_object;


	protected function setUp()
	{
		$this->_object = new Controller_Frontend_Exception( 'msg', 1, null, array( 'key' => 'value' ) );
	}


	protected function tearDown()
	{
	}


	public function testGetMessage()
	{
		$this->assertEquals( 'msg', $this->_object->getMessage() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 1, $this->_object->getCode() );
	}


	public function testGetErrorList()
	{
		$this->assertEquals( array( 'key' => 'value' ), $this->_object->getErrorList() );
	}
}
