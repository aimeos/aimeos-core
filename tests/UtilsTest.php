<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos;


class UtilsTest extends \PHPUnit\Framework\TestCase
{
	public function testRefs()
	{
		$ref = ['text', 'product' => 'customer', 'media' => ['default'], 'price' => null, 5 => ['default'], '', 'text', 'stock' => [], 'catalog', 'catalog' => ['default']];
		$expected = ['text' => null, 'customer' => null, 'media' => ['default'], 'stock' => [], 'catalog' => null];

		$this->assertEquals( $expected, \Aimeos\Utils::refs( $ref ) );
	}
}
