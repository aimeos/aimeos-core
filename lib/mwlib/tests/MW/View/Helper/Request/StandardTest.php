<?php

namespace Aimeos\MW\View\Helper\Request;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$view = new \Aimeos\MW\View\Standard();
		$files = array(
			'test' => array(
				'tmp_name' => 'tempfile.txt',
				'name' => 'clientfile.txt',
				'type' => 'text/plain',
				'size' => 1024,
				'error' => 0,
			)
		);

		$this->object = new \Aimeos\MW\View\Helper\Request\Standard( $view, 'body', '127.0.0.1', 'test', $files );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\View\\Helper\\Iface', $this->object->transform() );
	}


	public function testGetBody()
	{
		$this->assertEquals( 'body', $this->object->transform()->getBody() );
	}


	public function testGetClientAddress()
	{
		$this->assertEquals( '127.0.0.1', $this->object->transform()->getClientAddress() );
	}


	public function testGetTarget()
	{
		$this->assertEquals( 'test', $this->object->transform()->getTarget() );
	}


	public function testGetUploadedFiles()
	{
		$files = $this->object->transform()->getUploadedFiles();

		$this->assertArrayHasKey( 'test', $files );
		$this->assertInstanceOf( '\Psr\Http\Message\UploadedFileInterface', $files['test'] );
	}


	public function testGetUploadedFilesMultiple()
	{
		$view = new \Aimeos\MW\View\Standard();
		$files = array(
			'test' => array(
				'tmp_name' => array(
					'tempfile.txt',
					'tempfile2.txt',
				),
				'name' => array(
					'clientfile.txt',
					'clientfile2.txt',
				),
				'type' => array(
					'text/plain',
					'text/english',
				),
				'size' => array(
					1024,
					2048,
				),
				'error' => array(
					0,
					1,
				),
			)
		);

		$object = new \Aimeos\MW\View\Helper\Request\Standard( $view, 'body', '127.0.0.1', 'test', $files );

		$files = $object->transform()->getUploadedFiles();

		$this->assertArrayHasKey( 'test', $files );

		foreach( $files['test'] as $object ) {
			$this->assertInstanceOf( '\Psr\Http\Message\UploadedFileInterface', $object );
		}
	}


	public function testGetUploadedFilesSeveral()
	{
		$view = new \Aimeos\MW\View\Standard();
		$files = array(
			'other' => array(
				'test' => array(
					'tmp_name' => 'tempfile2.txt',
					'name' => 'clientfile2.txt',
					'type' => 'text/english',
					'size' => 2048,
					'error' => 1,
				),
			),
			'test' => array(
				'tmp_name' => 'tempfile.txt',
				'name' => 'clientfile.txt',
				'type' => 'text/plain',
				'size' => 1024,
				'error' => 0,
			)
		);

		$object = new \Aimeos\MW\View\Helper\Request\Standard( $view, 'body', '127.0.0.1', 'test', $files );

		$files = $object->transform()->getUploadedFiles();

		$this->assertArrayHasKey( 'test', $files );
		$this->assertArrayHasKey( 'other', $files );
		$this->assertArrayHasKey( 'test', $files['other'] );

		$this->assertInstanceOf( '\Psr\Http\Message\UploadedFileInterface', $files['test'] );
		$this->assertInstanceOf( '\Psr\Http\Message\UploadedFileInterface', $files['other']['test'] );
	}

}
