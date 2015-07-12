<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Order_FactoryTest extends MW_Unittest_Testcase
{
	public function testCreateController()
	{
		$obj = Controller_ExtJS_Order_Factory::createController( TestHelper::getContext() );
		$this->assertInstanceOf( 'Controller_ExtJS_Interface', $obj);
	}


	public function testFactoryExceptionWrongName()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Order_Factory::createController(TestHelper::getContext(), 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Order_Factory::createController(TestHelper::getContext(), 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Order_Factory::createController(TestHelper::getContext(), 'Factory' );
	}

}
