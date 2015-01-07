<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_Product_Export_FactoryTest extends MW_Unittest_Testcase
{
	public function testCreateController()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$obj = Controller_Jobs_Product_Export_Factory::createController( $context, $arcavias );
		$this->assertInstanceOf( 'Controller_Jobs_Interface', $obj);
	}


	public function testFactoryExceptionWrongName()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$object = Controller_Jobs_Product_Export_Factory::createController( $context, $arcavias, 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$object = Controller_Jobs_Product_Export_Factory::createController( $context, $arcavias, 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$object = Controller_Jobs_Product_Export_Factory::createController( $context, $arcavias, 'Factory' );
	}
}