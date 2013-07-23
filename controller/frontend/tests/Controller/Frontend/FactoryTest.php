<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Controller_Frontend_Factory.
 */
class Controller_Frontend_FactoryTest extends MW_Unittest_Testcase
{
	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('Controller_Frontend_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testCreateManager()
	{
		$manager = Controller_Frontend_Factory::createController( TestHelper::getContext(), 'basket' );
		$this->assertInstanceOf( 'Controller_Frontend_Common_Interface', $manager );
	}


	public function testCreateManagerEmpty()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), "\t\n" );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), 'notexist' );
	}


	public function testCreateSubManagerNotExisting()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), 'basket/notexist' );
	}

}