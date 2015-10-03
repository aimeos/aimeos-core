<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Common_Product_Import_Csv_Cache_Attribute_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$context = TestHelper::getContext();
		$this->object = new Controller_Common_Product_Import_Csv_Cache_Attribute_Default( $context );
	}


	protected function tearDown()
	{
		MShop_Factory::setCache( false );
		MShop_Factory::clear();
	}


	public function testGet()
	{
		$item = $this->object->get( 'black', 'color' );

		$this->assertInstanceOf( 'MShop_Attribute_Item_Iface', $item );
		$this->assertEquals( 'black', $item->getCode() );
		$this->assertEquals( 'color', $item->getType() );
	}


	public function testGetUnknown()
	{
		$this->assertEquals( null, $this->object->get( 'cache-test', 'color' ) );
	}


	public function testSet()
	{
		$item = $this->object->get( 'black', 'color' );
		$item->setCode( 'cache-test' );

		$this->object->set( $item );
		$item = $this->object->get( 'cache-test', 'color' );

		$this->assertInstanceOf( 'MShop_Attribute_Item_Iface', $item );
		$this->assertEquals( 'cache-test', $item->getCode() );
		$this->assertEquals( 'color', $item->getType() );
	}
}