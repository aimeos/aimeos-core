<?php

namespace Aimeos\Controller\Common\Order;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$target = '\\Aimeos\\Controller\\Common\\Order\\Iface';

		$controller = \Aimeos\Controller\Common\Order\Factory::createController( \TestHelper::getContext() );
		$this->assertInstanceOf( $target, $controller );

		$controller = \Aimeos\Controller\Common\Order\Factory::createController( \TestHelper::getContext(), 'Standard' );
		$this->assertInstanceOf( $target, $controller );
	}


	public function testCreateControllerInvalidImplementation()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Common\\Exception' );
		\Aimeos\Controller\Common\Order\Factory::createController( \TestHelper::getContext(), 'Invalid' );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Common\\Exception' );
		\Aimeos\Controller\Common\Order\Factory::createController( \TestHelper::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Common\\Exception' );
		\Aimeos\Controller\Common\Order\Factory::createController( \TestHelper::getContext(), 'notexist' );
	}
}
