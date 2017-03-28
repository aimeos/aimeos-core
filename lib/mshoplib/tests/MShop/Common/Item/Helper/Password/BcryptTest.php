<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 */


namespace Aimeos\MShop\Common\Item\Helper\Password;


/**
 * Test class for \Aimeos\MShop\Common\Item\Helper\Password\Hash
 */
class BcryptTest extends \PHPUnit_Framework_TestCase
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
		$object = new \Aimeos\MShop\Common\Item\Helper\Password\Bcrypt( [] );

		$this->assertStringStartsWith( '$2y$10$', $object->encode( 'unittest' ) );
	}


	public function testEncodeCosts()
	{
		$object = new \Aimeos\MShop\Common\Item\Helper\Password\Bcrypt( array( 'cost' => 5 ) );

		$this->assertStringStartsWith( '$2y$05$', $object->encode( 'unittest' ) );
	}


	public function testEncodeNoSalt()
	{
		$object = new \Aimeos\MShop\Common\Item\Helper\Password\Bcrypt( [] );

		$this->assertStringStartsWith( '$2y$10$', $object->encode( 'unittest', null ) );
	}
}
