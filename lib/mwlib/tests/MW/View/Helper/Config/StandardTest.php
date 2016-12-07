<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\View\Helper\Config;


/**
 * Test class for \Aimeos\MW\View\Helper\Config.
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

		$config = array(
			'page' => 'test',
			'sub' => array(
				'subpage' => 'test2',
			),
		);

		$conf = new \Aimeos\MW\Config\PHPArray( $config );
		$this->object = new \Aimeos\MW\View\Helper\Config\Standard( $view, $conf );
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
		$this->assertEquals( 'test', $this->object->transform( 'page', 'none' ) );
		$this->assertEquals( 'none', $this->object->transform( 'missing', 'none' ) );
	}


	public function testTransformPath()
	{
		$this->assertEquals( 'test2', $this->object->transform( 'sub/subpage', 'none' ) );
		$this->assertEquals( array( 'subpage' => 'test2' ), $this->object->transform( 'sub' ) );
	}


	public function testTransformNoDefault()
	{
		$this->assertEquals( 'test', $this->object->transform( 'page' ) );
		$this->assertEquals( null, $this->object->transform( 'missing' ) );
	}

}
