<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Test class for MW_View_Helper_Number.
 */
class MW_View_Helper_Number_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$view = new MW_View_Standard();
		$this->object = new MW_View_Helper_Number_Standard( $view, '.', ' ' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertEquals( '1.00', $this->object->transform( 1 ) );
		$this->assertEquals( '1.00', $this->object->transform( 1.0 ) );
		$this->assertEquals( '1 000.00', $this->object->transform( 1000.0 ) );
	}


	public function testTransformNoDecimals()
	{
		$this->assertEquals( '1', $this->object->transform( 1, 0 ) );
		$this->assertEquals( '1', $this->object->transform( 1.0, 0 ) );
		$this->assertEquals( '1 000', $this->object->transform( 1000.0, 0 ) );
	}

}
