<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2021
 */


namespace Aimeos\MShop\Common\Helper\Password;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MShop\Common\Helper\Password\Standard( [] );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testEncode()
	{
		$this->assertStringStartsWith( '$2y$10$', $this->object->encode( 'unittest' ) );
	}
}
