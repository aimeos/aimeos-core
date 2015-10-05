<?php

namespace Aimeos\Controller\ExtJS\Service\Lists\Type;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$obj = \Aimeos\Controller\ExtJS\Service\Lists\Type\Factory::createController( \TestHelper::getContext() );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\ExtJS\\Iface', $obj );
	}


	public function testFactoryExceptionWrongName()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Service\Lists\Type\Factory::createController( \TestHelper::getContext(), 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Service\Lists\Type\Factory::createController( \TestHelper::getContext(), 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Service\Lists\Type\Factory::createController( \TestHelper::getContext(), 'Factory' );
	}

}
