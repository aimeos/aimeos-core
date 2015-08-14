<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MAdmin_Job_Manager_Factory.
 */
class MAdmin_Job_Manager_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateManager()
	{
		$manager = MAdmin_Job_Manager_Factory::createManager( TestHelper::getContext() );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $manager );
	}


	public function testCreateManagerName()
	{
		$manager = MAdmin_Job_Manager_Factory::createManager( TestHelper::getContext(), 'Default' );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $manager );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( 'MAdmin_Job_Exception' );
		MAdmin_Job_Manager_Factory::createManager( TestHelper::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MAdmin_Job_Manager_Factory::createManager( TestHelper::getContext(), 'notexist' );
	}

}