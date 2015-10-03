<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_Product_Export_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$obj = Controller_Jobs_Product_Export_Factory::createController( $context, $aimeos );
		$this->assertInstanceOf( 'Controller_Jobs_Iface', $obj);
	}


	public function testFactoryExceptionWrongName()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$object = Controller_Jobs_Product_Export_Factory::createController( $context, $aimeos, 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$object = Controller_Jobs_Product_Export_Factory::createController( $context, $aimeos, 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$object = Controller_Jobs_Product_Export_Factory::createController( $context, $aimeos, 'Factory' );
	}
}