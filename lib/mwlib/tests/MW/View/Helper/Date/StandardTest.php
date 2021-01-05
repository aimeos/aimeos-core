<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\View\Helper\Date;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Date\Standard( $view, 'd.m.Y' );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertEquals( '01.01.2000', $this->object->transform( '2000-01-01 00:00:00' ) );
		$this->assertEquals( '01.01.0000', $this->object->transform( '0000-01-01 00:00:00' ) );
	}

}
