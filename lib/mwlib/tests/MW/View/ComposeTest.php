<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\MW\View;


class ComposeTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $translate;


	protected function setUp()
	{
		$engines = array(
			'.php' => new \Aimeos\MW\View\Standard( array( __DIR__ => array( '_testfiles' . DIRECTORY_SEPARATOR . 'php' ) ) ),
			'.phtml' => new \Aimeos\MW\View\Standard( array( __DIR__ => array( '_testfiles' . DIRECTORY_SEPARATOR . 'phtml' ) ) ),
		);

		$this->object = new \Aimeos\MW\View\Compose( $engines );
		$this->translate = new \Aimeos\MW\View\Helper\Translate\Standard( $this->object, new \Aimeos\MW\Translation\None( 'en_GB' ) );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->translate );
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

		$this->object->test = array( 'key' => 'val' );
		$this->assertEquals( 'val', $this->object->get( 'test/key' ) );

		$this->object->test = new \stdClass();
		$this->assertEquals( null, $this->object->get( 'test/key' ) );
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

		$ds = DIRECTORY_SEPARATOR;
		$phpList = array( 'notexisting', __DIR__ . $ds . '_testfiles' . $ds . 'php' . $ds . 'template.php' );
		$phtmlList = array( 'notexisting', __DIR__ . $ds . '_testfiles' . $ds . 'phtml' . $ds . 'template.phtml' );


		$this->object->assign( array( 'quantity' => 1 ) );
		$output = $this->object->render( $phpList );

		$expected = "Number of files:\n1 File";
		$this->assertEquals( $expected, $output );


		$this->object->assign( array( 'quantity' => 0 ) );
		$output = $this->object->render( $phtmlList );

		$expected = "Number of directories:\n0 Files";
		$this->assertEquals( $expected, $output );
	}


	public function testAssignRenderRelativePath()
	{
		$this->object->addHelper( 'translate', $this->translate );


		$this->object->assign( array( 'quantity' => 1 ) );
		$output = $this->object->render( array( 'notexisting', 'template.php' ) );

		$expected = "Number of files:\n1 File";
		$this->assertEquals( $expected, $output );


		$this->object->assign( array( 'quantity' => 0 ) );
		$output = $this->object->render( array( 'notexisting', 'template.phtml' ) );

		$expected = "Number of directories:\n0 Files";
		$this->assertEquals( $expected, $output );
	}
}
