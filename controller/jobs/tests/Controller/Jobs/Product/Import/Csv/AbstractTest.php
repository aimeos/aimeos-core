<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_Product_Import_Csv_AbstractTest extends MW_Unittest_Testcase
{
	private $object;


	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->_object = new Controller_Jobs_Product_Import_Csv_TestAbstract( $context, $arcavias );
	}


	protected function tearDown()
	{
		MShop_Factory::setCache( false );
		MShop_Factory::clear();
	}


	public function testGetCache()
	{
		$cache = $this->_object->getCachePublic( 'attribute' );

		$this->assertInstanceOf( 'Controller_Jobs_Product_Import_Csv_Cache_Interface', $cache );
	}


	public function testGetCacheInvalidType()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->getCachePublic( '$' );
	}


	public function testGetCacheInvalidClass()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->getCachePublic( 'test' );
	}


	public function testGetCacheInvalidInterface()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->getCachePublic( 'attribute', 'Invalid' );
	}


	public function testGetProcessors()
	{
		$processor = $this->_object->getProcessorsPublic( array( 'attribute' => array() ) );

		$this->assertInstanceOf( 'Controller_Jobs_Product_Import_Csv_Processor_Interface', $processor );
	}


	public function testGetProcessorsInvalidType()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->getProcessorsPublic( array( '$' => array() ) );
	}


	public function testGetProcessorsInvalidClass()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->getProcessorsPublic( array( 'test' => array() ) );
	}


	public function testGetProcessorsInvalidInterface()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->getProcessorsPublic( array( 'invalid' => array() ) );
	}


	public function testGetTypeId()
	{
		$typeid = $this->_object->getTypeIdPublic( 'attribute/type', 'product', 'color' );

		$this->assertNotEquals( null, $typeid );
	}


	public function testGetTypeIdUnknown()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$this->_object->getTypeIdPublic( 'attribute/type', 'product', 'unknown' );
	}
}


class Controller_Jobs_Product_Import_Csv_TestAbstract
	extends Controller_Jobs_Product_Import_Csv_Abstract
{
	public function getCachePublic( $type, $name = null )
	{
		return $this->_getCache( $type, $name );
	}


	public function getProcessorsPublic( array $mappings )
	{
		return $this->_getProcessors( $mappings );
	}


	public function getTypeIdPublic( $path, $domain, $code )
	{
		return $this->_getTypeId( $path, $domain, $code );
	}
}


class Controller_Jobs_Product_Import_Csv_Cache_Attribute_Invalid
{
}


class Controller_Jobs_Product_Import_Csv_Processor_Invalid_Default
{
}
