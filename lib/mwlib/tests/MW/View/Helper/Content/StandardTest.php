<?php

namespace Aimeos\MW\View\Helper\Content;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$view = new \Aimeos\MW\View\Standard();

		$helper = new \Aimeos\MW\View\Helper\Encoder\Standard( $view );
		$view->addHelper( 'encoder', $helper );

		$this->object = new \Aimeos\MW\View\Helper\Content\Standard( $view, 'base/url' );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testTransformRelativeUrl()
	{
		$output = $this->object->transform( 'path/to/resource' );
		$this->assertEquals( 'base/url/path/to/resource', $output );
	}


	public function testTransformAbsoluteUrl()
	{
		$output = $this->object->transform( 'https://host:443/path/to/resource' );
		$this->assertEquals( 'https://host:443/path/to/resource', $output );
	}


	public function testTransformDataUrl()
	{
		$output = $this->object->transform( 'data:image/gif;base64,R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs=' );
		$this->assertEquals( 'data:image/gif;base64,R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs=', $output );
	}
}
