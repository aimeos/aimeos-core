<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Common_Product_Import_Csv_Cache_Catalog_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$context = TestHelper::getContext();
		$this->_object = new Controller_Common_Product_Import_Csv_Cache_Catalog_Default( $context );
	}


	protected function tearDown()
	{
		MShop_Factory::setCache( false );
		MShop_Factory::clear();
	}


	public function testGet()
	{
		$result = $this->_object->get( 'root' );

		$this->assertNotEquals( null, $result );
		$this->assertInternalType( 'integer', $result );
	}


	public function testGetUnknown()
	{
		$this->assertEquals( null, $this->_object->get( 'cache-test' ) );
	}


	public function testSet()
	{
		$item = MShop_Factory::createManager( TestHelper::getContext(), 'catalog' )->createItem();
		$item->setCode( 'cache-test' );
		$item->setId( 1 );

		$this->_object->set( $item );
		$id = $this->_object->get( 'cache-test' );

		$this->assertEquals( $item->getId(), $id );
	}
}