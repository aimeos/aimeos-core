<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Common_Product_Import_Csv_Processor_Attribute_StandardTest extends PHPUnit_Framework_TestCase
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
			0 => 'attribute.type',
			1 => 'attribute.code',
			2 => 'product.list.type',
			3 => 'attribute.type',
			4 => 'attribute.code',
			5 => 'product.list.type',
			6 => 'attribute.type',
			7 => 'attribute.code',
			8 => 'product.list.type',
		);

		$data = array(
			0 => 'length',
			1 => '30',
			2 => 'variant',
			3 => 'width',
			4 => '29',
			5 => 'variant',
			6 => 'color',
			7 => 'white',
			8 => 'variant',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Attribute_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$pos = 0;
		$listItems = $product->getListItems();
		$expected = array(
			array( 'variant', 'length', '30' ),
			array( 'variant', 'width', '29' ),
			array( 'variant', 'color', 'white' ),
		);

		$this->assertEquals( 3, count( $listItems ) );

		foreach( $listItems as $listItem )
		{
			$this->assertEquals( 1, $listItem->getStatus() );
			$this->assertEquals( 'attribute', $listItem->getDomain() );
			$this->assertEquals( $expected[$pos][0], $listItem->getType() );
			$this->assertEquals( $expected[$pos][1], $listItem->getRefItem()->getType() );
			$this->assertEquals( $expected[$pos][2], $listItem->getRefItem()->getCode() );
			$pos++;
		}
	}


	public function testProcessMultiple()
	{
		$mapping = array(
			0 => 'attribute.type',
			1 => 'attribute.code',
			2 => 'product.list.type',
		);

		$data = array(
			0 => 'color',
			1 => "white\nblack\naimeos",
			2 => 'variant',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Catalog_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$this->delete( $product );


		$pos = 0;
		$codes = array( 'white', 'black', 'aimeos' );

		foreach( $product->getListItems() as $listItems )
		{
			$this->assertEquals( 3, count( $listItems ) );

			foreach( $listItems as $listItem )
			{
				$this->assertEquals( 1, $listItem->getStatus() );
				$this->assertEquals( 'attribute', $listItem->getDomain() );
				$this->assertEquals( 'variant', $listItem->getType() );
				$this->assertEquals( $codes[$pos], $listItem->getRefItem()->getCode() );
				$pos++;
			}
		}
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'attribute.type',
			1 => 'attribute.code',
		);

		$data = array(
			0 => 'length',
			1 => '30',
		);

		$dataUpdate = array(
			0 => 'width',
			1 => '29',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Attribute_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$result = $object->process( $product, $dataUpdate );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_List_Iface', $listItem );

		$this->assertEquals( 'width', $listItem->getRefItem()->getType() );
		$this->assertEquals( '29', $listItem->getRefItem()->getCode() );
	}


	public function testProcessDelete()
	{
		$mapping = array(
			0 => 'attribute.type',
			1 => 'attribute.code',
		);

		$data = array(
			0 => 'length',
			1 => '30',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Attribute_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Attribute_Standard( $this->context, array(), $this->endpoint );
		$result = $object->process( $product, array() );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 0, count( $listItems ) );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'attribute.type',
			1 => 'attribute.code',
			2 => 'attribute.type',
			3 => 'attribute.code',
		);

		$data = array(
			0 => '',
			1 => '',
			2 => 'length',
			3 => '30',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Attribute_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 1, count( $listItems ) );
	}


	public function testProcessListtypes()
	{
		$mapping = array(
			0 => 'attribute.type',
			1 => 'attribute.code',
			2 => 'product.list.type',
			3 => 'attribute.type',
			4 => 'attribute.code',
			5 => 'product.list.type',
		);

		$data = array(
			0 => 'length',
			1 => '32',
			2 => 'custom',
			3 => 'width',
			4 => '30',
			5 => 'default',
		);

		$this->context->getConfig()->set( 'controller/common/product/import/csv/processor/attribute/listtypes', array( 'default' ) );

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Attribute_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_List_Iface', $listItem );

		$this->assertEquals( 'default', $listItem->getType() );
		$this->assertEquals( 'width', $listItem->getRefItem()->getType() );
		$this->assertEquals( '30', $listItem->getRefItem()->getCode() );
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

		foreach( $product->getListItems('attribute') as $listItem ) {
			$listManager->deleteItem( $listItem->getId() );
		}

		$manager->deleteItem( $product->getId() );
	}


	protected function get( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search, array('attribute') );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No product item for code "%1$s"', $code ) );
		}

		return $item;
	}
}