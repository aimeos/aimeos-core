<?php

namespace Aimeos\Controller\Jobs\Product\Import\Csv;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright \Aimeos\Aimeos (aimeos.org), 2015
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$obj = \Aimeos\Controller\Jobs\Product\Import\Csv\Factory::createController( $context, $aimeos );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\Jobs\\Iface', $obj);
	}


	public function testFactoryExceptionWrongName()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$object = \Aimeos\Controller\Jobs\Product\Import\Csv\Factory::createController( $context, $aimeos, 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$object = \Aimeos\Controller\Jobs\Product\Import\Csv\Factory::createController( $context, $aimeos, 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$context = \TestHelper::getContext();
		$aimeos = \TestHelper::getAimeos();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$object = \Aimeos\Controller\Jobs\Product\Import\Csv\Factory::createController( $context, $aimeos, 'Factory' );
	}
}