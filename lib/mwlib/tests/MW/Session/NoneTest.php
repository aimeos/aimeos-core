<?php

namespace Aimeos\MW\Session;


/**
 * Test class for \Aimeos\MW\Session\None.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class NoneTest extends \PHPUnit_Framework_TestCase
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
		$this->object = new \Aimeos\MW\Session\None();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset($this->object);
	}


	public function testGet()
	{
		$this->assertEquals(null, $this->object->get('test'));

		$this->object->set('test', '123456789');
		$this->assertEquals('123456789', $this->object->get('test'));
	}


	public function testSet()
	{
		$this->object->set('test', null);
		$this->assertEquals( null, $this->object->get( 'test' ) );

		$this->object->set('test', '234');
		$this->assertEquals( '234', $this->object->get( 'test' ) );
	}
}
