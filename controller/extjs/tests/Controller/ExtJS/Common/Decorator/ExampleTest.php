<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for Controller_ExtJS_Common_Decorator_ExampleTest.
 */
class Controller_ExtJS_Common_Decorator_ExampleTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$context = TestHelper::getContext();

		$controller = Controller_ExtJS_Admin_Job_Factory::createController( $context );
		$this->object = new Controller_ExtJS_Common_Decorator_Example( $context, $controller );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCall()
	{
		$this->object->additionalMethod();
	}


	public function testDeleteItems()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->object->deleteItems( new stdClass() );
	}


	public function testSaveItems()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->object->saveItems( new stdClass() );
	}


	public function testSearchItems()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->object->searchItems( new stdClass() );
	}


	public function testGetServiceDescription()
	{
		$this->object->getServiceDescription();
	}


	public function testGetItemSchema()
	{
		$this->object->getItemSchema();
	}


	public function testGetSearchSchema()
	{
		$this->object->getSearchSchema();
	}

}