<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_Jobs_Admin_Cache_DefaultTest extends PHPUnit_Framework_TestCase
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
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->_object = new Controller_Jobs_Admin_Cache_Default( $context, $arcavias );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Cache cleanup', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Removes the expired entries from the cache';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$this->_object->run();
	}
}
