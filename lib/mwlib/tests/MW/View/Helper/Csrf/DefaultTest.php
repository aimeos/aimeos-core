<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


/**
 * Test class for MW_View_Helper_Csrf_Default.
 */
class MW_View_Helper_Csrf_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$view = new MW_View_Default();
		$this->_object = new MW_View_Helper_Csrf_Default( $view, 'cname', 'cvalue' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testTransform()
	{
		$this->assertInstanceOf( 'MW_View_Helper_Interface', $this->_object->transform() );
	}


	public function testTransformName()
	{
		$this->assertEquals( 'cname', $this->_object->transform()->name() );
	}


	public function testTransformValue()
	{
		$this->assertEquals( 'cvalue', $this->_object->transform()->value() );
	}


	public function testTransformFormfield()
	{
		$expected = '<input type="hidden" name="cname" value="cvalue" />';

		$this->assertEquals( $expected, $this->_object->transform()->formfield() );
	}


	public function testTransformFormfieldNone()
	{
		$view = new MW_View_Default();
		$object = new MW_View_Helper_Csrf_Default( $view, 'cname', '' );

		$this->assertEquals( '', $object->transform()->formfield() );
	}
}
