<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\View\Helper\Config;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();

		$config = array(
			'page' => 'test',
			'sub' => array(
				'subpage' => 'test2',
			),
		);

		$conf = new \Aimeos\MW\Config\PHPArray( $config );
		$this->object = new \Aimeos\MW\View\Helper\Config\Standard( $view, $conf );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertEquals( 'test', $this->object->transform( 'page', 'none' ) );
		$this->assertEquals( 'none', $this->object->transform( 'missing', 'none' ) );
	}


	public function testTransformPath()
	{
		$this->assertEquals( 'test2', $this->object->transform( 'sub/subpage', 'none' ) );
		$this->assertEquals( array( 'subpage' => 'test2' ), $this->object->transform( 'sub' ) );
	}


	public function testTransformNoDefault()
	{
		$this->assertEquals( 'test', $this->object->transform( 'page' ) );
		$this->assertEquals( null, $this->object->transform( 'missing' ) );
	}

}
