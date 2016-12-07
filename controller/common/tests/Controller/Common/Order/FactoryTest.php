<?php

namespace Aimeos\Controller\Common\Order;


/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$target = '\\Aimeos\\Controller\\Common\\Order\\Iface';

		$controller = \Aimeos\Controller\Common\Order\Factory::createController( \TestHelperCntl::getContext() );
		$this->assertInstanceOf( $target, $controller );

		$controller = \Aimeos\Controller\Common\Order\Factory::createController( \TestHelperCntl::getContext(), 'Standard' );
		$this->assertInstanceOf( $target, $controller );
	}


	public function testCreateControllerInvalidImplementation()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Common\\Exception' );
		\Aimeos\Controller\Common\Order\Factory::createController( \TestHelperCntl::getContext(), 'Invalid' );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Common\\Exception' );
		\Aimeos\Controller\Common\Order\Factory::createController( \TestHelperCntl::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Common\\Exception' );
		\Aimeos\Controller\Common\Order\Factory::createController( \TestHelperCntl::getContext(), 'notexist' );
	}
}
