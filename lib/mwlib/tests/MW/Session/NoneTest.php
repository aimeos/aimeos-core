<?php

/**
 * Test class for MW_Session_None.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Session_NoneTest extends PHPUnit_Framework_TestCase
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
		$this->_object = new MW_Session_None();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset($this->_object);
	}


	public function testGet()
	{
		$this->assertEquals(null, $this->_object->get('test'));

		$this->_object->set('test', '123456789');
		$this->assertEquals('123456789', $this->_object->get('test'));
	}


	public function testSet()
	{
		$this->_object->set('test', null);
		$this->assertEquals( null, $this->_object->get( 'test' ) );

		$this->_object->set('test', '234');
		$this->assertEquals( '234', $this->_object->get( 'test' ) );
	}
}
