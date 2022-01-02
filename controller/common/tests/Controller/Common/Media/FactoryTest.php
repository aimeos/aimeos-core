<?php

namespace Aimeos\Controller\Common\Media;


/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2022
 */
class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateController()
	{
		$target = \Aimeos\Controller\Common\Media\Iface::class;

		$controller = \Aimeos\Controller\Common\Media\Factory::create( \TestHelperCntl::context() );
		$this->assertInstanceOf( $target, $controller );

		$controller = \Aimeos\Controller\Common\Media\Factory::create( \TestHelperCntl::context(), 'Standard' );
		$this->assertInstanceOf( $target, $controller );
	}


	public function testCreateControllerInvalidImplementation()
	{
		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		\Aimeos\Controller\Common\Media\Factory::create( \TestHelperCntl::context(), 'Invalid' );
	}


	public function testCreateControllerInvalidName()
	{
		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		\Aimeos\Controller\Common\Media\Factory::create( \TestHelperCntl::context(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		\Aimeos\Controller\Common\Media\Factory::create( \TestHelperCntl::context(), 'notexist' );
	}
}
