<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Controller_Common_Order_FactoryTest extends MW_Unittest_Testcase
{
	public function testCreateController()
	{
		$target = 'Controller_Common_Order_Interface';

		$controller = Controller_Common_Order_Factory::createController( TestHelper::getContext() );
		$this->assertInstanceOf( $target, $controller );

		$controller = Controller_Common_Order_Factory::createController( TestHelper::getContext(), 'Default' );
		$this->assertInstanceOf( $target, $controller );
	}


	public function testCreateControllerInvalidImplementation()
	{
		$this->setExpectedException( 'Controller_Common_Exception' );
		Controller_Common_Order_Factory::createController( TestHelper::getContext(), 'Invalid' );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( 'Controller_Common_Exception' );
		Controller_Common_Order_Factory::createController( TestHelper::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_Common_Exception' );
		Controller_Common_Order_Factory::createController( TestHelper::getContext(), 'notexist' );
	}
}
