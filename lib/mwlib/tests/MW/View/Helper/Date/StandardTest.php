<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\View\Helper\Date;


/**
 * Test class for \Aimeos\MW\View\Helper\Date.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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
		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Date\Standard( $view, 'd.m.Y' );
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
		$this->assertEquals( '01.01.2000', $this->object->transform( '2000-01-01 00:00:00' ) );
		$this->assertEquals( '01.01.0000', $this->object->transform( '0000-01-01 00:00:00' ) );
	}

}
