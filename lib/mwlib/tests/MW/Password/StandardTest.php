<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MW\Password;


class StandardtTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\Password\Standard();
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testHash()
	{
		$this->assertStringStartsWith( '$2y$10$', $this->object->hash( 'unittest' ) );
	}


	public function testVerify()
	{
		$this->assertTrue( $this->object->verify( 'unittest', $this->object->hash( 'unittest' ) ) );
	}
}
