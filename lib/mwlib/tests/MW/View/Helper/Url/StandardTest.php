<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\View\Helper\Url;


/**
 * Test class for \Aimeos\MW\View\Helper\Url.
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
		$this->object = new \Aimeos\MW\View\Helper\Url\Standard( $view, '/baseurl/' );
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
		$expected = '/baseurl/module/test/index/some-nice-text?plain=1&multi%5Bsub%5D=1';
		$params = array( 'plain' => 1, 'multi' => array( 'sub' => true ) );
		$trailing = array( 'some', 'nice', 'text' );

		$this->assertEquals( $expected, $this->object->transform( 'module', 'test', 'index', $params, $trailing ) );
	}


	public function testTransformNoTrailing()
	{
		$expected = '/baseurl/module/test/index/?plain=1&multi%5Bsub%5D=1';
		$params = array( 'plain' => 1, 'multi' => array( 'sub' => true ) );

		$this->assertEquals( $expected, $this->object->transform( 'module', 'test', 'index', $params ) );
	}


	public function testTransformOnlyBase()
	{
		$this->assertEquals( '/baseurl/', $this->object->transform() );
	}

}
