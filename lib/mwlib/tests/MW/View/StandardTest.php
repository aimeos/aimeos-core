<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\View;


/**
 * Test class for \Aimeos\MW\View\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $translate;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new \Aimeos\MW\View\Standard();
		$this->translate = new \Aimeos\MW\View\Helper\Translate\Standard( $this->object, new \Aimeos\MW\Translation\None( 'en_GB' ) );
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


	public function testMagicMethods()
	{
		$this->assertEquals( false, isset( $this->object->test ) );

		$this->object->test = 10;
		$this->assertEquals( 10, $this->object->test );
		$this->assertEquals( true, isset( $this->object->test ) );

		unset( $this->object->test );
		$this->assertEquals( false, isset( $this->object->test ) );

		$this->setExpectedException( '\\Aimeos\\MW\\View\\Exception' );
		$this->object->test;
	}


	public function testGet()
	{
		$this->assertEquals( null, $this->object->get( 'test' ) );
		$this->assertEquals( 1, $this->object->get( 'test', 1 ) );

		$this->object->test = 10;
		$this->assertEquals( 10, $this->object->get( 'test' ) );
	}


	public function testCallCreateHelper()
	{
		$enc = $this->object->encoder();
		$this->assertInstanceOf( '\\Aimeos\\MW\\View\\Helper\\Iface', $enc );
	}


	public function testCallInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\View\\Exception' );
		$this->object->invalid();
	}


	public function testCallUnknown()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\View\\Exception' );
		$this->object->unknown();
	}


	public function testCallAddHelper()
	{
		$this->object->addHelper( 'translate', $this->translate );

		$this->assertEquals( 'File', $this->object->translate( 'test', 'File', 'Files', 1 ) );
	}


	public function testAssignRender()
	{
		$this->object->addHelper( 'translate', $this->translate );
		$filename = __DIR__ . DIRECTORY_SEPARATOR . 'testfiles'. DIRECTORY_SEPARATOR . 'template';


		$this->object->assign( array( 'quantity' => 1 ) );
		$output = $this->object->render( $filename );

		$expected = "Number of files:\n1 File";
		$this->assertEquals( $expected, $output );


		$this->object->assign( array( 'quantity' => 0 ) );
		$output = $this->object->render( $filename );

		$expected = "Number of files:\n0 Files";
		$this->assertEquals( $expected, $output );
	}


	public function testAssignRenderRelativePath()
	{
		$this->object = new \Aimeos\MW\View\Standard( array( __DIR__ => array( 'testfiles' ) ) );
		$this->object->addHelper( 'translate', $this->translate );


		$this->object->assign( array( 'quantity' => 1 ) );
		$output = $this->object->render( 'template' );

		$expected = "Number of files:\n1 File";
		$this->assertEquals( $expected, $output );


		$this->object->assign( array( 'quantity' => 0 ) );
		$output = $this->object->render( 'template' );

		$expected = "Number of files:\n0 Files";
		$this->assertEquals( $expected, $output );
	}
}
