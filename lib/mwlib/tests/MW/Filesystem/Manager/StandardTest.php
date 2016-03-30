<?php

namespace Aimeos\MW\Filesystem\Manager;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $config;
	private $object;


	protected function setUp()
	{
		$this->config = \TestHelperMw::getConfig();
		$this->object = new \Aimeos\MW\Filesystem\Manager\Standard( $this->config );
	}


	protected function tearDown()
	{
		$this->config->set( 'resource/fs-media', null );
		$this->config->set( 'resource/fs', null );

		unset( $this->object );
	}


	public function testGet()
	{
		$this->config->set( 'resource/fs-media', array( 'adapter' => 'Standard', 'basedir' => __DIR__ ) );
		$this->assertInstanceof( 'Aimeos\MW\Filesystem\Iface', $this->object->get( 'fs-media' ) );
	}


	public function testGetFallback()
	{
		$this->config->set( 'resource/fs', array( 'adapter' => 'Standard', 'basedir' => __DIR__ ) );
		$this->assertInstanceof( 'Aimeos\MW\Filesystem\Iface', $this->object->get( 'fs-media' ) );
	}
}
