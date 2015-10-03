<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for Controller_Jobs_Factory.
 */
class Controller_Jobs_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$controller = Controller_Jobs_Factory::createController( $context, $aimeos, 'admin/job' );
		$this->assertInstanceOf( 'Controller_Jobs_Iface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Factory::createController( $context, $aimeos, "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Factory::createController( $context, $aimeos, '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Factory::createController( $context, $aimeos, 'notexist' );
	}


	public function testGetControllers()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$list = Controller_Jobs_Factory::getControllers( $context, $aimeos, TestHelper::getControllerPaths() );

		$this->assertGreaterThan( 0, count( $list ) );

		foreach( $list as $key => $object ) {
			$this->assertInstanceOf( 'Controller_Jobs_Iface', $object );
		}
	}
}