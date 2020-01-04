<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2018
 */


namespace Aimeos\MShop\Common\Helper\Password;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MShop\Common\Helper\Password\Standard( array( 'format' => '{%2$s}%1$s' ) );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testEncode()
	{
		$this->assertEquals( '14f40cc1311a52a6021b93a155ed719aac2bdb70', $this->object->encode( 'unittest', 'salt' ) );
	}
}
