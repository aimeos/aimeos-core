<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 */


namespace Aimeos\MW\Criteria\Plugin;


class CutTest extends \PHPUnit\Framework\TestCase
{
	public function testTranslate()
	{
		$object = new \Aimeos\MW\Criteria\Plugin\Cut();
		$this->assertEquals( 255, strlen( $object->translate( str_pad( '0', 256 ) ) ) );
	}


	public function testTranslateValue()
	{
		$object = new \Aimeos\MW\Criteria\Plugin\Cut();
		$this->assertEquals( [str_pad( '0', 255 )], $object->translate( [str_pad( '0', 256 )] ) );
	}


	public function testReverse()
	{
		$object = new \Aimeos\MW\Criteria\Plugin\Cut();
		$this->assertEquals( 'test', $object->reverse( 'test' ) );
	}
}
