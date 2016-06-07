<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MW\View\Helper\Access;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	public function testTransformSingleOK()
	{
		$view = new \Aimeos\MW\View\Standard();
		$fcn = function() { return array( 'editor' ); };

		$object = new \Aimeos\MW\View\Helper\Access\Standard( $view, $fcn );
		$this->assertTrue( $object->transform( 'editor' ) );
	}


	public function testTransformSingleFailure()
	{
		$view = new \Aimeos\MW\View\Standard();
		$fcn = function() { return array( 'editor' ); };

		$object = new \Aimeos\MW\View\Helper\Access\Standard( $view, $fcn );
		$this->assertFalse( $object->transform( 'admin' ) );
	}


	public function testTransformMultipleOK()
	{
		$view = new \Aimeos\MW\View\Standard();
		$fcn = function() { return array( 'admin', 'editor' ); };

		$object = new \Aimeos\MW\View\Helper\Access\Standard( $view, $fcn );
		$this->assertTrue( $object->transform( array( 'editor' ) ) );
	}


	public function testTransformMultipleFailure()
	{
		$view = new \Aimeos\MW\View\Standard();
		$fcn = function() { return array( 'admin', 'editor' ); };

		$object = new \Aimeos\MW\View\Helper\Access\Standard( $view, $fcn );
		$this->assertFalse( $object->transform( array( 'test', 'example' ) ) );
	}
}
