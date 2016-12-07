<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\View\Helper\Url;


/**
 * Test class for \Aimeos\MW\View\Helper\Url\None.
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
		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Url\None( $view );
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
		$params = array( 'plain' => 1, 'multi' => array( 'sub' => true ) );
		$trailing = array( 'some', 'nice', 'text' );

		$this->assertEquals( '', $this->object->transform( 'module', 'test', 'index', $params, $trailing ) );
	}

}
