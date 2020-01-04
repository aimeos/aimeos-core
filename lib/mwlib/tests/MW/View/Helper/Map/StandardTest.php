<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */


namespace Aimeos\MW\View\Helper\Map;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\View\Helper\Map\Standard( new \Aimeos\MW\View\Standard() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testTransform()
	{
		$list = [['test1' => 'value1', 'test2' => 'value2']];
		$this->assertEquals( ['value1' => 'value2'], $this->object->transform( $list, 'test1', 'test2' )->toArray() );
	}


	public function testTransformMap()
	{
		$list = \Aimeos\Map::from( [['test1' => 'value1', 'test2' => 'value2']] );
		$this->assertEquals( ['value1' => 'value2'], $this->object->transform( $list, 'test1', 'test2' )->toArray() );
	}
}
