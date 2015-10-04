<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Test class for MW_View_Helper_FormParam_Standard.
 */
class MW_View_Helper_FormParam_StandardTest extends PHPUnit_Framework_TestCase
{
	public function testTransform()
	{
		$view = new MW_View_Standard();
		$object = new MW_View_Helper_FormParam_Standard( $view );

		$this->assertEquals( 'test', $object->transform( 'test' ) );
		$this->assertEquals( 'test', $object->transform( array( 'test' ) ) );
	}


	public function testTransformMultiNames()
	{
		$view = new MW_View_Standard();
		$object = new MW_View_Helper_FormParam_Standard( $view );

		$this->assertEquals( 'test[test2]', $object->transform( array( 'test', 'test2' ) ) );
	}


	public function testTransformWithPrefix()
	{
		$view = new MW_View_Standard();
		$object = new MW_View_Helper_FormParam_Standard( $view, array( 'prefix' ) );

		$this->assertEquals( 'prefix[test]', $object->transform( 'test' ) );
		$this->assertEquals( 'prefix[test]', $object->transform( array( 'test' ) ) );
	}


	public function testTransformWithMultiPrefix()
	{
		$view = new MW_View_Standard();
		$object = new MW_View_Helper_FormParam_Standard( $view, array( 'pre', 'fix' ) );

		$this->assertEquals( 'pre[fix][test]', $object->transform( 'test' ) );
		$this->assertEquals( 'pre[fix][test]', $object->transform( array( 'test' ) ) );
	}

}
