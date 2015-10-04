<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Test class for MW_View_Helper_Config.
 */
class MW_View_Helper_Config_StandardTest extends PHPUnit_Framework_TestCase
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

		$config = array(
			'page' => 'test',
			'sub' => array(
				'subpage' => 'test2',
			),
		);

		$conf = new MW_Config_PHPArray( $config );
		$this->object = new MW_View_Helper_Config_Standard( $view, $conf );
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
		$this->assertEquals( 'test', $this->object->transform( 'page', 'none' ) );
		$this->assertEquals( 'none', $this->object->transform( 'missing', 'none' ) );
	}


	public function testTransformPath()
	{
		$this->assertEquals( 'test2', $this->object->transform( 'sub/subpage', 'none' ) );
		$this->assertEquals( array( 'subpage' => 'test2' ), $this->object->transform( 'sub' ) );
	}


	public function testTransformNoDefault()
	{
		$this->assertEquals( 'test', $this->object->transform( 'page' ) );
		$this->assertEquals( null, $this->object->transform( 'missing' ) );
	}

}
