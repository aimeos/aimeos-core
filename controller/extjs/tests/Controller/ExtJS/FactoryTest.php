<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Controller_ExtJS_Factory.
 */
class Controller_ExtJS_FactoryTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite('Controller_ExtJS_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testCreateController()
	{
		$controller = Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'attribute' );
		$this->assertInstanceOf( 'Controller_ExtJS_Common_Interface', $controller );
	}


	public function testCreateSubController()
	{
		$controller = Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'attribute/list/type' );
		$this->assertInstanceOf( 'Controller_ExtJS_Common_Interface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'notexist' );
	}


	public function testCreateSubControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'attribute/notexist' );
	}


	public function testClear()
	{
		$context = TestHelper::getContext();

		$controller1 = Controller_ExtJS_Factory::createController( $context, 'attribute' );
		Controller_ExtJS_Factory::clear();
		$controller2 = Controller_ExtJS_Factory::createController( $context, 'attribute' );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$context = TestHelper::getContext();

		$cntlA1 = Controller_ExtJS_Factory::createController( $context, 'attribute' );
		$cntlB1 = Controller_ExtJS_Factory::createController( $context, 'attribute/list/type' );
		Controller_ExtJS_Factory::clear( $context->getLocale()->getSiteId() );

		$cntlA2 = Controller_ExtJS_Factory::createController( $context, 'attribute' );
		$cntlB2 = Controller_ExtJS_Factory::createController( $context, 'attribute/list/type' );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertNotSame( $cntlB1, $cntlB2 );
	}


	public function testClearSpecific()
	{
		$context = TestHelper::getContext();

		$cntlA1 = Controller_ExtJS_Factory::createController( $context, 'attribute' );
		$cntlB1 = Controller_ExtJS_Factory::createController( $context, 'attribute/list/type' );
		Controller_ExtJS_Factory::clear( $context->getLocale()->getSiteId(), 'attribute' );

		$cntlA2 = Controller_ExtJS_Factory::createController( $context, 'attribute' );
		$cntlB2 = Controller_ExtJS_Factory::createController( $context, 'attribute/list/type' );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertSame( $cntlB1, $cntlB2 );
	}

}