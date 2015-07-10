<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for Controller_Jobs_Factory.
 */
class Controller_Jobs_FactoryTest extends MW_Unittest_Testcase
{
	public function testCreateController()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$controller = Controller_Jobs_Factory::createController( $context, $arcavias, 'admin/job' );
		$this->assertInstanceOf( 'Controller_Jobs_Interface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Factory::createController( $context, $arcavias, "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Factory::createController( $context, $arcavias, '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Factory::createController( $context, $arcavias, 'notexist' );
	}


	public function testGetControllers()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$list = Controller_Jobs_Factory::getControllers( $context, $arcavias, TestHelper::getControllerPaths() );

		$this->assertGreaterThan( 0, count( $list ) );

		foreach( $list as $key => $object ) {
			$this->assertInstanceOf( 'Controller_Jobs_Interface', $object );
		}
	}
}