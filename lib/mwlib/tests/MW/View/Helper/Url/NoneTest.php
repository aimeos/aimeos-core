<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\View\Helper\Url;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Url\None( $view );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$params = array( 'plain' => 1, 'multi' => array( 'sub' => true ) );
		$trailing = array( 'some', 'nice', 'text' );

		$this->assertEquals( '', $this->object->transform( 'module', 'test', 'index', $params, $trailing ) );
	}

}
