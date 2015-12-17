<?php

namespace Aimeos\Controller\ExtJS\Coupon\Code;


/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$obj = \Aimeos\Controller\ExtJS\Coupon\Code\Factory::createController( \TestHelperExtjs::getContext() );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\ExtJS\\Iface', $obj );
	}


	public function testFactoryExceptionWrongName()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Coupon\Code\Factory::createController( \TestHelperExtjs::getContext(), 'Wrong$$$Name' );
	}

	public function testFactoryExceptionWrongClass()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Coupon\Code\Factory::createController( \TestHelperExtjs::getContext(), 'WrongClass' );
	}

	public function testFactoryExceptionWrongInterface()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Coupon\Code\Factory::createController( \TestHelperExtjs::getContext(), 'Factory' );
	}

}
