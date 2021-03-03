<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\View;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $translate;


	protected function setUp() : void
	{
		$engines = array( '.phtml' => new \Aimeos\MW\View\Engine\TestEngine() );

		$this->object = new \Aimeos\MW\View\Standard( array( __DIR__ => array( '_testfiles' ) ), $engines );
		$this->translate = new \Aimeos\MW\View\Helper\Translate\Standard( $this->object, new \Aimeos\MW\Translation\None( 'en_GB' ) );
	}


	protected function tearDown() : void
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

		$this->expectException( \Aimeos\MW\View\Exception::class );
		$this->object->test;
	}


	public function testGet()
	{
		$this->object->test = 'val';
		$this->assertEquals( 'val', $this->object->get( 'test' ) );
	}


	public function testGetDefault()
	{
		$this->assertEquals( null, $this->object->get( 'test' ) );
		$this->assertEquals( 1, $this->object->get( 'test', 1 ) );
	}


	public function testGetPath()
	{
		$this->object->test = array( 'key' => 'val' );
		$this->assertEquals( 'val', $this->object->get( 'test/key' ) );
	}


	public function testGetObjectKey()
	{
		$this->object->test = new \stdClass();
		$this->assertEquals( null, $this->object->get( 'test/key' ) );
	}


	public function testSet()
	{
		$result = $this->object->set( 'test', 'value' );
		$this->assertEquals( 'value', $this->object->get( 'test' ) );
		$this->assertInstanceOf( \Aimeos\MW\View\Iface::class, $result );
	}


	public function testSetPath()
	{
		$result = $this->object->set( 'test/key', 'value' );
		$this->assertEquals( 'value', $this->object->get( 'test/key' ) );
		$this->assertInstanceOf( \Aimeos\MW\View\Iface::class, $result );
	}


	public function testCallCreateHelper()
	{
		$enc = $this->object->encoder();
		$this->assertInstanceOf( \Aimeos\MW\View\Helper\Iface::class, $enc );
	}


	public function testCallInvalidName()
	{
		$this->expectException( \Aimeos\MW\View\Exception::class );
		$this->object->invalid();
	}


	public function testCallUnknown()
	{
		$this->expectException( \Aimeos\MW\View\Exception::class );
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
		$filenames = array( 'notexisting', __DIR__ . $ds . '_testfiles' . $ds . 'template1' );

		$output = $this->object->assign( array( 'quantity' => 1 ) )->render( $filenames );
		$this->assertEquals( "Number of files: 1 File", $output );

		$output = $this->object->assign( array( 'quantity' => 0 ) )->render( $filenames );
		$this->assertEquals( "Number of files: 0 Files", $output );
	}


	public function testAssignRenderRelativePath()
	{
		$this->object->addHelper( 'translate', $this->translate );

		$output = $this->object->assign( ['quantity' => 1] )->render( ['notexisting', 'template1'] );
		$this->assertEquals( "Number of files: 1 File", $output );

		$output = $this->object->assign( ['quantity' => 2] )->render( ['notexisting', 'template2'] );
		$this->assertEquals( "Number of directories: 2", $output );
	}
}
