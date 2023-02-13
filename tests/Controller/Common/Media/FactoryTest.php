<?php

namespace Aimeos\Controller\Common\Media;


/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */
class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateController()
	{
		$target = \Aimeos\Controller\Common\Media\Iface::class;

		$controller = \Aimeos\Controller\Common\Media\Factory::create( \TestHelper::context() );
		$this->assertInstanceOf( $target, $controller );

		$controller = \Aimeos\Controller\Common\Media\Factory::create( \TestHelper::context(), 'Standard' );
		$this->assertInstanceOf( $target, $controller );
	}


	public function testCreateControllerInvalidImplementation()
	{
		$this->expectException( \LogicException::class );
		\Aimeos\Controller\Common\Media\Factory::create( \TestHelper::context(), 'Invalid' );
	}


	public function testCreateControllerInvalidName()
	{
		$this->expectException( \LogicException::class );
		\Aimeos\Controller\Common\Media\Factory::create( \TestHelper::context(), '%^unknown' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->expectException( \LogicException::class );
		\Aimeos\Controller\Common\Media\Factory::create( \TestHelper::context(), 'unknown' );
	}
}
