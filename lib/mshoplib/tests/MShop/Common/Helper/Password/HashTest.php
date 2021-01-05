<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2021
 */


namespace Aimeos\MShop\Common\Helper\Password;


class HashTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		if( !function_exists( 'hash' ) ) {
			$this->markTestSkipped( 'Function "hash()" is not available' );
		}
	}


	public function testEncode()
	{
		$object = new \Aimeos\MShop\Common\Helper\Password\Hash( array( 'algorithm' => 'sha512' ) );
		$this->assertEquals(
			'ab255ada0e89787032b8e0ba76c58799be09e7e676a87635e1512f04ebd754258c7fed89299739fa4efbfd68583146f5ebde7ac4a526c16ca1968870289c8589',
			$object->encode( 'unittest', 'salt' )
		);
	}


	public function testEncodeFormat()
	{
		$object = new \Aimeos\MShop\Common\Helper\Password\Hash( array( 'algorithm' => 'sha512', 'format' => '%2$s%1$s' ) );
		$this->assertEquals(
			'e8769dcb36626c891ac942252a4c7b3a442d0d400157222ab4ac0045f0b5f05ffe8ccbf80845b34f6f23b56f31e8ff0c0db96064aa01bf9a850058767a38750f',
			$object->encode( 'unittest', 'salt' )
		);
	}


	public function testEncodeBase64()
	{
		$object = new \Aimeos\MShop\Common\Helper\Password\Hash( array( 'algorithm' => 'sha512', 'base64' => true ) );
		$this->assertEquals(
			'qyVa2g6JeHAyuOC6dsWHmb4J5+Z2qHY14VEvBOvXVCWMf+2JKZc5+k77/WhYMUb16956xKUmwWyhlohwKJyFiQ==',
			$object->encode( 'unittest', 'salt' )
		);
	}


	public function testEncodeIterations()
	{
		$object = new \Aimeos\MShop\Common\Helper\Password\Hash( array( 'algorithm' => 'sha512', 'iterations' => 2 ) );
		$this->assertEquals(
			'95cabc655d134d7cfd357e5eee16fe7bd2e82d4b8eb32550aa3c9b969f23b05db3527e1e8aa3be9b0967dc55ca23a185d24b50535ee733c1bf6a191778995bfe',
			$object->encode( 'unittest', 'salt' )
		);
	}
}
