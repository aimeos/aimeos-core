<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Common_Product_Import_Csv_BaseTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->object = new Controller_Common_Product_Import_Csv_TestAbstract( $context, $aimeos );
	}


	protected function tearDown()
	{
		MShop_Factory::setCache( false );
		MShop_Factory::clear();
	}


	public function testGetCache()
	{
		$cache = $this->object->getCachePublic( 'attribute' );

		$this->assertInstanceOf( 'Controller_Common_Product_Import_Csv_Cache_Iface', $cache );
	}


	public function testGetCacheInvalidType()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->object->getCachePublic( '$' );
	}


	public function testGetCacheInvalidClass()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->object->getCachePublic( 'test' );
	}


	public function testGetCacheInvalidInterface()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->object->getCachePublic( 'attribute', 'Invalid' );
	}


	public function testGetProcessors()
	{
		$processor = $this->object->getProcessorsPublic( array( 'attribute' => array() ) );

		$this->assertInstanceOf( 'Controller_Common_Product_Import_Csv_Processor_Iface', $processor );
	}


	public function testGetProcessorsInvalidType()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->object->getProcessorsPublic( array( '$' => array() ) );
	}


	public function testGetProcessorsInvalidClass()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->object->getProcessorsPublic( array( 'test' => array() ) );
	}


	public function testGetProcessorsInvalidInterface()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->object->getProcessorsPublic( array( 'invalid' => array() ) );
	}


	public function testGetTypeId()
	{
		$typeid = $this->object->getTypeIdPublic( 'attribute/type', 'product', 'color' );

		$this->assertNotEquals( null, $typeid );
	}


	public function testGetTypeIdUnknown()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->object->getTypeIdPublic( 'attribute/type', 'product', 'unknown' );
	}
}


class Controller_Common_Product_Import_Csv_TestAbstract
	extends Controller_Common_Product_Import_Csv_Base
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


class Controller_Common_Product_Import_Csv_Cache_Attribute_Invalid
{
}


class Controller_Common_Product_Import_Csv_Processor_Invalid_Standard
{
}
