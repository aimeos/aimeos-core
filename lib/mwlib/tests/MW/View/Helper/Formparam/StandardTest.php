<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\View\Helper\Formparam;


/**
 * Test class for \Aimeos\MW\View\Helper\Formparam\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	public function testTransform()
	{
		$view = new \Aimeos\MW\View\Standard();
		$object = new \Aimeos\MW\View\Helper\Formparam\Standard( $view );

		$this->assertEquals( 'test', $object->transform( 'test' ) );
		$this->assertEquals( 'test', $object->transform( array( 'test' ) ) );
	}


	public function testTransformMultiNames()
	{
		$view = new \Aimeos\MW\View\Standard();
		$object = new \Aimeos\MW\View\Helper\Formparam\Standard( $view );

		$this->assertEquals( 'test[test2]', $object->transform( array( 'test', 'test2' ) ) );
	}


	public function testTransformWithPrefix()
	{
		$view = new \Aimeos\MW\View\Standard();
		$object = new \Aimeos\MW\View\Helper\Formparam\Standard( $view, array( 'prefix' ) );

		$this->assertEquals( 'prefix[test]', $object->transform( 'test' ) );
		$this->assertEquals( 'prefix[test]', $object->transform( array( 'test' ) ) );
	}


	public function testTransformWithMultiPrefix()
	{
		$view = new \Aimeos\MW\View\Standard();
		$object = new \Aimeos\MW\View\Helper\Formparam\Standard( $view, array( 'pre', 'fix' ) );

		$this->assertEquals( 'pre[fix][test]', $object->transform( 'test' ) );
		$this->assertEquals( 'pre[fix][test]', $object->transform( array( 'test' ) ) );
	}

}
