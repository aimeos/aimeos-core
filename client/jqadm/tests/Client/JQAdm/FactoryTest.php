<?php

namespace Aimeos\Client\JQAdm;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $templatePaths;


	protected function setUp()
	{
		$this->context = \TestHelper::getContext();
		$this->templatePaths = \TestHelper::getJQAdmTemplatePaths();
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreateClient()
	{
		$client = \Aimeos\Client\JQAdm\Factory::createClient( $this->context, $this->templatePaths, 'product' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\JQAdm\\Iface', $client );
	}


	public function testCreateClientName()
	{
		$client = \Aimeos\Client\JQAdm\Factory::createClient( $this->context, $this->templatePaths, 'product', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\JQAdm\\Iface', $client );
	}


	public function testCreateClientNameEmpty()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\JQAdm\\Exception' );
		\Aimeos\Client\JQAdm\Factory::createClient( $this->context, $this->templatePaths, '' );
	}


	public function testCreateClientNameInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\JQAdm\\Exception' );
		\Aimeos\Client\JQAdm\Factory::createClient( $this->context, $this->templatePaths, '%product' );
	}


	public function testCreateClientNameNotFound()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\JQAdm\\Exception' );
		\Aimeos\Client\JQAdm\Factory::createClient( $this->context, $this->templatePaths, 'prod' );
	}

}
