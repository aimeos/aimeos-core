<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Controller\Jobs;


/**
 * Test class for \Aimeos\Controller\Jobs\Factory.
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateControllerEmpty()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		\Aimeos\Controller\Jobs\Factory::createController( $context, $aimeos, "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		\Aimeos\Controller\Jobs\Factory::createController( $context, $aimeos, '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		\Aimeos\Controller\Jobs\Factory::createController( $context, $aimeos, 'notexist' );
	}


	public function testGetControllers()
	{
		$context = \TestHelperJobs::getContext();
		$aimeos = \TestHelperJobs::getAimeos();

		$list = \Aimeos\Controller\Jobs\Factory::getControllers( $context, $aimeos, \TestHelperJobs::getControllerPaths() );

		$this->assertEquals( 0, count( $list ) );
	}
}