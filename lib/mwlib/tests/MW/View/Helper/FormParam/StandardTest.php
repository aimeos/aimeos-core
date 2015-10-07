<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


namespace Aimeos\MW\View\Helper\FormParam;


/**
 * Test class for \Aimeos\MW\View\Helper\FormParam\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	public function testTransform()
	{
		$view = new \Aimeos\MW\View\Standard();
		$object = new \Aimeos\MW\View\Helper\FormParam\Standard( $view );

		$this->assertEquals( 'test', $object->transform( 'test' ) );
		$this->assertEquals( 'test', $object->transform( array( 'test' ) ) );
	}


	public function testTransformMultiNames()
	{
		$view = new \Aimeos\MW\View\Standard();
		$object = new \Aimeos\MW\View\Helper\FormParam\Standard( $view );

		$this->assertEquals( 'test[test2]', $object->transform( array( 'test', 'test2' ) ) );
	}


	public function testTransformWithPrefix()
	{
		$view = new \Aimeos\MW\View\Standard();
		$object = new \Aimeos\MW\View\Helper\FormParam\Standard( $view, array( 'prefix' ) );

		$this->assertEquals( 'prefix[test]', $object->transform( 'test' ) );
		$this->assertEquals( 'prefix[test]', $object->transform( array( 'test' ) ) );
	}


	public function testTransformWithMultiPrefix()
	{
		$view = new \Aimeos\MW\View\Standard();
		$object = new \Aimeos\MW\View\Helper\FormParam\Standard( $view, array( 'pre', 'fix' ) );

		$this->assertEquals( 'pre[fix][test]', $object->transform( 'test' ) );
		$this->assertEquals( 'pre[fix][test]', $object->transform( array( 'test' ) ) );
	}

}
