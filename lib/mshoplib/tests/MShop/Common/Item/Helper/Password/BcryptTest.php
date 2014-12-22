<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */


/**
 * Test class for MShop_Common_Item_Helper_Password_Hash
 */
class MShop_Common_Item_Helper_Password_BcryptTest extends MW_Unittest_Testcase
{
	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		if( !function_exists( 'password_hash' ) ) {
			$this->markTestSkipped( 'Function "password_hash()" is not available' );
		}
	}
	
	
	public function testEncode()
	{
		$object = new MShop_Common_Item_Helper_Password_Bcrypt( array() );

		$this->assertEquals(
			'$2y$10$c2FsdF9zYWx0X3NhbHRfcuNJSf6HFyF2qPGZDWvEXBLuTtcAslWdq',
			$object->encode( 'unittest', 'salt_salt_salt_salt_salt' )
		);
	}


	public function testEncodeCosts()
	{
		$object = new MShop_Common_Item_Helper_Password_Bcrypt( array( 'cost' => 5 ) );

		$this->assertEquals(
			'$2y$05$c2FsdF9zYWx0X3NhbHRfcuHzU2BI0BC7YakW47ir/Ddzn0/D/gNqS',
			$object->encode( 'unittest', 'salt_salt_salt_salt_salt' )
		);
	}


	public function testEncodeNoSalt()
	{
		$object = new MShop_Common_Item_Helper_Password_Bcrypt( array() );

		$this->assertStringStartsWith( '$2y$10$', $object->encode( 'unittest', null ) );
	}
}
