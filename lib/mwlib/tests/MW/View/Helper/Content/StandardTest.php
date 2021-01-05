<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\View\Helper\Content;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();

		$helper = new \Aimeos\MW\View\Helper\Encoder\Standard( $view );
		$view->addHelper( 'encoder', $helper );

		$this->object = new \Aimeos\MW\View\Helper\Content\Standard( $view, 'base/url' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testTransform()
	{
		$this->assertEquals( '', $this->object->transform( null ) );
	}


	public function testTransformRelativeUrl()
	{
		$output = $this->object->transform( 'path/to/resource' );
		$this->assertEquals( 'base/url/path/to/resource', $output );
	}


	public function testTransformAbsoluteUrl()
	{
		$output = $this->object->transform( '/path/to/resource' );
		$this->assertEquals( '/path/to/resource', $output );
	}


	public function testTransformDataUrl()
	{
		$output = $this->object->transform( 'data:image/gif;base64,R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs=' );
		$this->assertEquals( 'data:image/gif;base64,R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs=', $output );
	}


	public function testTransformHttpUrl()
	{
		$output = $this->object->transform( 'https://host:443/path/to/resource' );
		$this->assertEquals( 'https://host:443/path/to/resource', $output );
	}
}
