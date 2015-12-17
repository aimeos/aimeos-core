<?php

namespace Aimeos\Controller\Common\Product\Import\Csv;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$context = \TestHelperCntl::getContext();
		$aimeos = \TestHelperCntl::getAimeos();

		$this->object = new TestAbstract( $context, $aimeos );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();
	}


	public function testGetCache()
	{
		$cache = $this->object->getCachePublic( 'attribute' );

		$this->assertInstanceOf( '\\Aimeos\\Controller\\Common\\Product\\Import\\Csv\\Cache\\Iface', $cache );
	}


	public function testGetCacheInvalidType()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->getCachePublic( '$' );
	}


	public function testGetCacheInvalidClass()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->getCachePublic( 'test' );
	}


	public function testGetCacheInvalidInterface()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->getCachePublic( 'attribute', 'Invalid' );
	}


	public function testGetProcessors()
	{
		$processor = $this->object->getProcessorsPublic( array( 'attribute' => array() ) );

		$this->assertInstanceOf( '\\Aimeos\\Controller\\Common\\Product\\Import\\Csv\\Processor\\Iface', $processor );
	}


	public function testGetProcessorsInvalidType()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->getProcessorsPublic( array( '$' => array() ) );
	}


	public function testGetProcessorsInvalidClass()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->getProcessorsPublic( array( 'test' => array() ) );
	}


	public function testGetProcessorsInvalidInterface()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->getProcessorsPublic( array( 'TestInvalid' => array() ) );
	}


	public function testGetTypeId()
	{
		$typeid = $this->object->getTypeIdPublic( 'attribute/type', 'product', 'color' );

		$this->assertNotEquals( null, $typeid );
	}


	public function testGetTypeIdUnknown()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->getTypeIdPublic( 'attribute/type', 'product', 'unknown' );
	}
}


class TestAbstract
	extends \Aimeos\Controller\Common\Product\Import\Csv\Base
{
	public function getCachePublic( $type, $name = null )
	{
		return $this->getCache( $type, $name );
	}


	public function getProcessorsPublic( array $mappings )
	{
		return $this->getProcessors( $mappings );
	}


	public function getTypeIdPublic( $path, $domain, $code )
	{
		return $this->getTypeId( $path, $domain, $code );
	}
}


class TestInvalid
{
}
