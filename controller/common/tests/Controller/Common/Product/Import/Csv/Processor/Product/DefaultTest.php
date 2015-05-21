<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Common_Product_Import_Csv_Processor_Product_DefaultTest extends MW_Unittest_Testcase
{
	private $_context;
	private $_endpoint;


	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$this->_context = TestHelper::getContext();
		$this->_endpoint = new Controller_Common_Product_Import_Csv_Processor_Done( $this->_context, array() );
	}


	protected function tearDown()
	{
		MShop_Factory::setCache( false );
		MShop_Factory::clear();
	}


	public function testProcess()
	{
		$mapping = array(
			0 => 'product.list.type',
			1 => 'product.code',
			2 => 'product.list.type',
			3 => 'product.code',
			4 => 'product.list.type',
			5 => 'product.code',
		);

		$data = array(
			0 => 'default',
			1 => 'CNC',
			2 => 'default',
			3 => 'CNE',
			4 => 'suggestion',
			5 => 'CNE',
		);

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$pos = 0;
		$listItems = $product->getListItems();
		$expected = array(
			array( 'default', 'CNC' ),
			array( 'default', 'CNE' ),
			array( 'suggestion', 'CNE' ),
		);

		$this->assertEquals( 3, count( $listItems ) );

		foreach( $listItems as $listItem )
		{
			$this->assertEquals( 1, $listItem->getStatus() );
			$this->assertEquals( 'product', $listItem->getDomain() );
			$this->assertEquals( $expected[$pos][0], $listItem->getType() );
			$this->assertEquals( $expected[$pos][1], $listItem->getRefItem()->getCode() );
			$pos++;
		}
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'product.list.type',
			1 => 'product.code',
		);

		$data = array(
			0 => 'default',
			1 => 'CNC',
		);

		$dataUpdate = array(
			0 => 'default',
			1 => 'CNE',
		);

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );

		$result = $object->process( $product, $dataUpdate );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_List_Interface', $listItem );

		$this->assertEquals( 'CNE', $listItem->getRefItem()->getCode() );
	}


	public function testProcessDelete()
	{
		$mapping = array(
			0 => 'product.list.type',
			1 => 'product.code',
		);

		$data = array(
			0 => 'default',
			1 => 'CNC',
		);

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Default( $this->_context, array(), $this->_endpoint );
		$result = $object->process( $product, array() );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 0, count( $listItems ) );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'product.list.type',
			1 => 'product.code',
			2 => 'product.list.type',
			3 => 'product.code',
		);

		$data = array(
			0 => '',
			1 => '',
			2 => 'default',
			3 => 'CNE',
		);

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 1, count( $listItems ) );
	}


	public function testProcessListtypes()
	{
		$mapping = array(
			0 => 'product.list.type',
			1 => 'product.code',
			2 => 'product.list.type',
			3 => 'product.code',
		);

		$data = array(
			0 => 'bought-together',
			1 => 'CNC',
			2 => 'default',
			3 => 'CNE',
		);

		$this->_context->getConfig()->set( 'controller/jobs/product/import/csv/processor/product/listtypes', array( 'default' ) );

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_List_Interface', $listItem );

		$this->assertEquals( 'default', $listItem->getType() );
		$this->assertEquals( 'CNE', $listItem->getRefItem()->getCode() );
	}


	protected function _create( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$typeManager = $manager->getSubManager( 'type' );

		$typeSearch = $typeManager->createSearch();
		$typeSearch->setConditions( $typeSearch->compare( '==', 'product.type.code', 'default' ) );
		$typeResult = $typeManager->searchItems( $typeSearch );

		if( ( $typeItem = reset( $typeResult ) ) === false ) {
			throw new Exception( 'No product type "default" found' );
		}

		$item = $manager->createItem();
		$item->setTypeid( $typeItem->getId() );
		$item->setCode( $code );

		$manager->saveItem( $item );

		return $item;
	}


	protected function _delete( MShop_Product_Item_Interface $product )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$listManager = $manager->getSubManager( 'list' );

		foreach( $product->getListItems('product') as $listItem ) {
			$listManager->deleteItem( $listItem->getId() );
		}

		$manager->deleteItem( $product->getId() );
	}


	protected function _get( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search, array('product') );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No product item for code "%1$s"', $code ) );
		}

		return $item;
	}
}