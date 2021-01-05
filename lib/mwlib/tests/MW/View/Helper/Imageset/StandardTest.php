<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\View\Helper\Imageset;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();
		$view->addHelper( 'content', new \Aimeos\MW\View\Helper\Content\Standard( $view, '/path/to' ) );

		$this->object = new \Aimeos\MW\View\Helper\Imageset\Standard( $view );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testTransform()
	{
		$expected = '/path/to/image-1.jpg 100w, /path/to/image-2.jpg 200w';
		$images = ['100' => 'image-1.jpg', '200' => 'image-2.jpg'];

		$this->assertEquals( $expected, $this->object->transform( $images ) );
	}
}
