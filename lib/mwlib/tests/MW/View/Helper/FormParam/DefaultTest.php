<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @version $Id$
 */


/**
 * Test class for MW_View_Helper_FormParam_Default.
 */
class MW_View_Helper_FormParam_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		$suite  = new PHPUnit_Framework_TestSuite('MW_View_Helper_FormParam_Default');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testTransform()
	{
		$view = new MW_View_Default();
		$object = new MW_View_Helper_FormParam_Default( $view );

		$this->assertEquals( 'test', $object->transform( 'test' ) );
		$this->assertEquals( 'test', $object->transform( array( 'test' ) ) );
	}


	public function testTransformMultiNames()
	{
		$view = new MW_View_Default();
		$object = new MW_View_Helper_FormParam_Default( $view );

		$this->assertEquals( 'test[test2]', $object->transform( array( 'test', 'test2' ) ) );
	}


	public function testTransformWithPrefix()
	{
		$view = new MW_View_Default();
		$object = new MW_View_Helper_FormParam_Default( $view, array( 'prefix' ) );

		$this->assertEquals( 'prefix[test]', $object->transform( 'test' ) );
		$this->assertEquals( 'prefix[test]', $object->transform( array( 'test' ) ) );
	}


	public function testTransformWithMultiPrefix()
	{
		$view = new MW_View_Default();
		$object = new MW_View_Helper_FormParam_Default( $view, array( 'pre', 'fix' ) );

		$this->assertEquals( 'pre[fix][test]', $object->transform( 'test' ) );
		$this->assertEquals( 'pre[fix][test]', $object->transform( array( 'test' ) ) );
	}

}
