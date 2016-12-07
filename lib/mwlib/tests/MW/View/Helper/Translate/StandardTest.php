<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\View\Helper\Translate;


/**
 * Test class for \Aimeos\MW\View\Helper\Translate.
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
		$translate = new \Aimeos\MW\Translation\None( 'en_GB' );
		$this->object = new \Aimeos\MW\View\Helper\Translate\Standard( $view, $translate );
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
		$this->assertEquals( 'File', $this->object->transform( 'test', 'File', 'Files', 1 ) );
		$this->assertEquals( 'Files', $this->object->transform( 'test', 'File', 'Files', 2 ) );
	}

}
