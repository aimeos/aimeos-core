<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_Jobs_Order_Email_Payment_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$obj = Controller_Jobs_Order_Email_Payment_Factory::createController( $context, $aimeos );
		$this->assertInstanceOf( 'Controller_Jobs_Interface', $obj );
	}


	public function testFactoryExceptionWrongName()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Order_Email_Payment_Factory::createController( $context, $aimeos, 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Order_Email_Payment_Factory::createController( $context, $aimeos, 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Order_Email_Payment_Factory::createController( $context, $aimeos, 'Factory' );
	}

}
