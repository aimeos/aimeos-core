<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Common_Product_Import_Csv_Processor_Product_StandardTest extends PHPUnit_Framework_TestCase
{
	private $context;
	private $endpoint;


	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$this->context = TestHelper::getContext();
		$this->endpoint = new Controller_Common_Product_Import_Csv_Processor_Done( $this->context, array() );
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

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


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


	public function testProcessMultiple()
	{
		$mapping = array(
			0 => 'product.list.type',
			1 => 'product.code',
			2 => 'product.list.type',
			3 => 'product.code',
		);

		$data = array(
			0 => 'default',
			1 => "CNC\nCNE",
			2 => 'suggestion',
			3 => "CNE\nCNC",
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$pos = 0;
		$listItems = $product->getListItems();
		$codes = array( 'CNC', 'CNE', 'CNE', 'CNC' );
		$types = array( 'default', 'default', 'suggestion', 'suggestion' );

		$this->assertEquals( 4, count( $listItems ) );

		foreach( $listItems as $listItem )
		{
			$this->assertEquals( 1, $listItem->getStatus() );
			$this->assertEquals( 'product', $listItem->getDomain() );
			$this->assertEquals( $types[$pos], $listItem->getType() );
			$this->assertEquals( $codes[$pos], $listItem->getRefItem()->getCode() );
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

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$result = $object->process( $product, $dataUpdate );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_List_Iface', $listItem );

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

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Standard( $this->context, array(), $this->endpoint );
		$result = $object->process( $product, array() );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


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

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


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

		$this->context->getConfig()->set( 'controller/common/product/import/csv/processor/product/listtypes', array( 'default' ) );

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Product_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_List_Iface', $listItem );

		$this->assertEquals( 'default', $listItem->getType() );
		$this->assertEquals( 'CNE', $listItem->getRefItem()->getCode() );
	}


	protected function create( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->context );
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


	protected function delete( MShop_Product_Item_Iface $product )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->context );
		$listManager = $manager->getSubManager( 'list' );

		foreach( $product->getListItems('product') as $listItem ) {
			$listManager->deleteItem( $listItem->getId() );
		}

		$manager->deleteItem( $product->getId() );
	}


	protected function get( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search, array('product') );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No product item for code "%1$s"', $code ) );
		}

		return $item;
	}
}