<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: FactoryTest.php 896 2012-07-04 12:25:26Z nsendetzky $
 */

class Controller_Frontend_Basket_FactoryTest extends MW_Unittest_Testcase
{
	private $_object;


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('Controller_Frontend_Basket_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	protected function setUp()
	{
	}


	protected function tearDown()
	{
	}


	public function testCreateController()
	{
		$target = 'Controller_Frontend_Basket_Interface';

		$controller = Controller_Frontend_Basket_Factory::createController( TestHelper::getContext() );
		$this->assertInstanceOf( $target, $controller );

		$controller = Controller_Frontend_Basket_Factory::createController( TestHelper::getContext(), 'Default' );
		$this->assertInstanceOf( $target, $controller );
	}


	public function testCreateControllerInvalidImplementation()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Basket_Factory::createController( TestHelper::getContext(), 'Invalid' );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Basket_Factory::createController( TestHelper::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Basket_Factory::createController( TestHelper::getContext(), 'notexist' );
	}
}
