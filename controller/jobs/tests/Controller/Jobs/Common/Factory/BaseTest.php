<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Controller\Jobs\Common\Factory;


/**
 * Test class for \Aimeos\Controller\Jobs\Common\Factory\BaseTest.
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $aimeos;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->aimeos = \TestHelperJobs::getAimeos();
		$this->context = \TestHelperJobs::getContext();
		$config = $this->context->getConfig();

		$config->set( 'controller/jobs/common/decorators/default', [] );
		$config->set( 'controller/jobs/admin/decorators/global', [] );
		$config->set( 'controller/jobs/admin/decorators/local', [] );

	}


	public function testInjectController()
	{
		$cntl = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Iface' )->getMock();
		TestAbstract::injectController( 'Test', $cntl );
	}


	public function testAddDecoratorsInvalidName()
	{
		$decorators = array( '$' );
		$cntl = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Iface' )->getMock();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		\Aimeos\Controller\Jobs\Common\Factory\TestAbstract::addDecoratorsPublic( $this->context, $this->aimeos, $cntl, $decorators, 'Test_' );
	}


	public function testAddDecoratorsInvalidClass()
	{
		$decorators = array( 'Test' );
		$cntl = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Iface' )->getMock();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		\Aimeos\Controller\Jobs\Common\Factory\TestAbstract::addDecoratorsPublic( $this->context, $this->aimeos, $cntl, $decorators, 'TestDecorator' );
	}


	public function testAddDecoratorsInvalidInterface()
	{
		$decorators = array( 'Test' );
		$cntl = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Iface' )->getMock();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		\Aimeos\Controller\Jobs\Common\Factory\TestAbstract::addDecoratorsPublic( $this->context, $this->aimeos, $cntl,
			$decorators, '\\Aimeos\\Controller\\Jobs\\Common\\Decorator\\' );
	}


	public function testAddControllerDecoratorsInvalidDomain()
	{
		$cntl = $this->getMockBuilder( '\Aimeos\Controller\Jobs\Iface' )->getMock();

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		\Aimeos\Controller\Jobs\Common\Factory\TestAbstract::addControllerDecoratorsPublic( $this->context, $this->aimeos, $cntl, '' );
	}
}


class TestAbstract
	extends \Aimeos\Controller\Jobs\Common\Factory\Base
{
	public static function addDecoratorsPublic( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos,
		\Aimeos\Controller\Jobs\Iface $controller, array $decorators, $classprefix )
	{
		self::addDecorators( $context, $aimeos, $controller, $decorators, $classprefix );
	}

	public static function addControllerDecoratorsPublic( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos,
		\Aimeos\Controller\Jobs\Iface $controller, $domain )
	{
		self::addControllerDecorators( $context, $aimeos, $controller, $domain );
	}
}


class TestDecorator
{
}
