<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_Product_Import_Csv_Cache_Attribute_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$context = TestHelper::getContext();
		$this->_object = new Controller_Jobs_Product_Import_Csv_Cache_Attribute_Default( $context );
	}


	protected function tearDown()
	{
		MShop_Factory::setCache( false );
		MShop_Factory::clear();
	}


	public function testGet()
	{
		$item = $this->_object->get( 'black', 'color' );

		$this->assertInstanceOf( 'MShop_Attribute_Item_Interface', $item );
		$this->assertEquals( 'black', $item->getCode() );
		$this->assertEquals( 'color', $item->getType() );
	}


	public function testGetUnknown()
	{
		$this->assertEquals( null, $this->_object->get( 'cache-test', 'color' ) );
	}


	public function testSet()
	{
		$item = $this->_object->get( 'black', 'color' );
		$item->setCode( 'cache-test' );

		$this->_object->set( $item );
		$item = $this->_object->get( 'cache-test', 'color' );

		$this->assertInstanceOf( 'MShop_Attribute_Item_Interface', $item );
		$this->assertEquals( 'cache-test', $item->getCode() );
		$this->assertEquals( 'color', $item->getType() );
	}
}