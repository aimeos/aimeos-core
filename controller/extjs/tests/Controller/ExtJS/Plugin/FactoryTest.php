<?php

namespace Aimeos\Controller\ExtJS\Plugin;


/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$context = \TestHelper::getContext();
		$context->getConfig()->set( 'controller/extjs/common/decorators/default', array() );
	}


	public function testCreateController()
	{
		$obj = \Aimeos\Controller\ExtJS\Plugin\Factory::createController( \TestHelper::getContext() );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\ExtJS\\Iface', $obj );
	}


	public function testFactoryExceptionWrongName()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Plugin\Factory::createController( \TestHelper::getContext(), 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Plugin\Factory::createController( \TestHelper::getContext(), 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Plugin\Factory::createController( \TestHelper::getContext(), 'Factory' );
	}


	public function testAbstractAddControllerDecoratorsWithExclude()
	{
		$context = \TestHelper::getContext();
		$config = $context->getConfig();

		$config->set( 'controller/extjs/common/decorators/default', array( 'Example' ) );
		$config->set( 'controller/extjs/plugin/decorators/excludes', array( 'Example' ) );

		$controller = \Aimeos\Controller\ExtJS\Plugin\Factory::createController( $context, 'Standard' );

		$this->assertInstanceOf( '\\Aimeos\\Controller\\ExtJS\\Common\\Iface', $controller );
	}


	// using Factorylocal class
	public function testAbstractAddControllerDecoratorsNoDomainException()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Plugin\Factorylocal::createController( \TestHelper::getContext(), 'Standard', '' );
	}


	public function testAbstractAddDecorators()
	{
		$context = \TestHelper::getContext();
		$config = $context->getConfig();

		$config->set( 'controller/extjs/common/decorators/default', array( 'Example', 'Example' ) );
		$config->set( 'controller/extjs/plugin/decorators/excludes', array() );

		$controller = \Aimeos\Controller\ExtJS\Plugin\Factory::createController( $context, 'Standard' );

		$this->assertInstanceOf( '\\Aimeos\\Controller\\ExtJS\\Common\\Decorator\\Iface', $controller );
	}


	public function testAbstractAddDecoratorsExceptionWrongName()
	{
		$context = \TestHelper::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/extjs/common/decorators/default', array( '$$' ) );

		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Plugin\Factorylocal::createController( $context, 'Standard', 'plugin' );
	}


	public function testAbstractAddDecoratorsExceptionWrongClass()
	{
		$context = \TestHelper::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/extjs/common/decorators/default', array( 'WrongClass' ) );

		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Plugin\Factorylocal::createController( $context, 'Standard', 'plugin' );
	}

}
