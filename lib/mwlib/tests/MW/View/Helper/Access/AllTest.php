<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\View\Helper\Access;


class AllTest extends \PHPUnit_Framework_TestCase
{
	public function testTransform()
	{
		$view = new \Aimeos\MW\View\Standard();
		$object = new \Aimeos\MW\View\Helper\Access\All( $view );
		$this->assertTrue( $object->transform( 'editor' ) );
	}
}
