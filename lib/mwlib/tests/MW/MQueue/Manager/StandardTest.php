<?php

namespace Aimeos\MW\MQueue\Manager;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $config;
	private $object;


	protected function setUp()
	{
		$this->config = \TestHelperMw::getConfig();
		$this->object = new \Aimeos\MW\MQueue\Manager\Standard( $this->config );
	}


	protected function tearDown()
	{
		$this->config->set( 'resource/mq-email', null );
		$this->config->set( 'resource/mq', null );

		unset( $this->object );
	}


	public function testGet()
	{
		$this->config->set( 'resource/mq-email', array( 'adapter' => 'None' ) );
		$this->assertInstanceof( 'Aimeos\MW\MQueue\Iface', $this->object->get( 'mq-email' ) );
	}


	public function testGetFallback()
	{
		$this->config->set( 'resource/mq', array( 'adapter' => 'None' ) );
		$this->assertInstanceof( 'Aimeos\MW\MQueue\Iface', $this->object->get( 'mq-email' ) );
	}


	public function testGetDatabaseConfig()
	{
		$this->config->set( 'resource/mq-email', array( 'adapter' => 'None', 'db' => 'db' ) );
		$this->assertInstanceof( 'Aimeos\MW\MQueue\Iface', $this->object->get( 'mq-email' ) );
	}
}
