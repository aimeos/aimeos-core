<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */


/**
 * Test class for MShop_Common_Item_Helper_Password_Default
 */
class MShop_Common_Item_Helper_Password_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_object = new MShop_Common_Item_Helper_Password_Default( array( 'format' => '{%2$s}%1$s' ) );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}

	
	public function testEncode()
	{
		$this->assertEquals( '14f40cc1311a52a6021b93a155ed719aac2bdb70', $this->_object->encode( 'unittest', 'salt' ) );
	}
}
