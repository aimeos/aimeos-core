<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\Jobs;


/**
 * Test class for \Aimeos\Controller\Jobs\Factory.
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$controller = \Aimeos\Controller\Jobs\Factory::createController( $context, $aimeos, 'admin/job' );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\Jobs\\Iface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		\Aimeos\Controller\Jobs\Factory::createController( $context, $aimeos, "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		\Aimeos\Controller\Jobs\Factory::createController( $context, $aimeos, '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		\Aimeos\Controller\Jobs\Factory::createController( $context, $aimeos, 'notexist' );
	}


	public function testGetControllers()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$list = \Aimeos\Controller\Jobs\Factory::getControllers( $context, $aimeos, \TestHelper::getControllerPaths() );

		$this->assertGreaterThan( 0, count( $list ) );

		foreach( $list as $key => $object ) {
			$this->assertInstanceOf( '\\Aimeos\\Controller\\Jobs\\Iface', $object );
		}
	}
}