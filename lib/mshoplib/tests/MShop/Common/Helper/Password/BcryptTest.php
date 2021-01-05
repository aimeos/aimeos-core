<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2021
 */


namespace Aimeos\MShop\Common\Helper\Password;


class BcryptTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		if( !function_exists( 'password_hash' ) ) {
			$this->markTestSkipped( 'Function "password_hash()" is not available' );
		}
	}


	public function testEncode()
	{
		$object = new \Aimeos\MShop\Common\Helper\Password\Bcrypt( [] );

		$this->assertStringStartsWith( '$2y$10$', $object->encode( 'unittest' ) );
	}


	public function testEncodeCosts()
	{
		$object = new \Aimeos\MShop\Common\Helper\Password\Bcrypt( array( 'cost' => 5 ) );

		$this->assertStringStartsWith( '$2y$05$', $object->encode( 'unittest' ) );
	}


	public function testEncodeNoSalt()
	{
		$object = new \Aimeos\MShop\Common\Helper\Password\Bcrypt( [] );

		$this->assertStringStartsWith( '$2y$10$', $object->encode( 'unittest', null ) );
	}
}
