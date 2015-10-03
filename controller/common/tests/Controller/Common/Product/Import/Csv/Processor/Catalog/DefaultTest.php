<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Common_Product_Import_Csv_Processor_Catalog_DefaultTest extends PHPUnit_Framework_TestCase
{
	private static $product;
	private $context;
	private $endpoint;


	public static function setUpBeforeClass()
	{
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$typeManager = $manager->getSubmanager( 'type' );

		$typeSearch = $typeManager->createSearch();
		$typeSearch->setConditions( $typeSearch->compare( '==', 'product.type.code', 'default' ) );
		$result = $typeManager->searchItems( $typeSearch );

		if( ( $typeItem = reset( $result ) ) === false ) {
			throw new Exception( 'Product type "default" not found' );
		}

		$item = $manager->createItem();
		$item->setCode( 'job_csv_prod' );
		$item->setTypeId( $typeItem->getId() );
		$item->setStatus( 1 );

		$manager->saveItem( $item );

		self::$product = $item;
	}


	public static function tearDownAfterClass()
	{
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$manager->deleteItem( self::$product->getId() );
	}


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
			0 => 'catalog.list.type',
			1 => 'catalog.code',
			2 => 'catalog.list.type',
			3 => 'catalog.code',
		);

		$data = array(
			0 => 'default',
			1 => 'job_csv_test',
			2 => 'promotion',
			3 => 'job_csv_test',
		);

		$category = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Catalog_Default( $this->context, $mapping, $this->endpoint );
		$result = $object->process( self::$product, $data );

		$category = $this->get( 'job_csv_test' );
		$this->delete( $category );


		$pos = 0;
		$listItems = $category->getListItems();
		$expected = array(
			array( 'default', 'job_csv_prod' ),
			array( 'promotion', 'job_csv_prod' ),
		);

		$this->assertEquals( 2, count( $listItems ) );

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
			0 => 'catalog.list.type',
			1 => 'catalog.code',
			2 => 'catalog.list.type',
			3 => 'catalog.code',
		);

		$data = array(
			0 => 'default',
			1 => "job_csv_test\njob_csv_test2",
			2 => 'promotion',
			3 => "job_csv_test\njob_csv_test2",
		);

		$category = $this->create( 'job_csv_test' );
		$category2 = $this->create( 'job_csv_test2' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Catalog_Default( $this->context, $mapping, $this->endpoint );
		$result = $object->process( self::$product, $data );

		$category = $this->get( 'job_csv_test' );
		$category2 = $this->get( 'job_csv_test2' );

		$this->delete( $category );
		$this->delete( $category2 );


		$pos = 0;
		$types = array( 'default', 'promotion', 'default', 'promotion' );

		foreach( array( $category->getListItems(), $category2->getListItems() ) as $listItems )
		{
			$this->assertEquals( 2, count( $listItems ) );

			foreach( $listItems as $listItem )
			{
				$this->assertEquals( 1, $listItem->getStatus() );
				$this->assertEquals( 'product', $listItem->getDomain() );
				$this->assertEquals( $types[$pos], $listItem->getType() );
				$this->assertEquals( 'job_csv_prod', $listItem->getRefItem()->getCode() );
				$pos++;
			}
		}
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'catalog.list.type',
			1 => 'catalog.code',
		);

		$data = array(
			0 => 'default',
			1 => 'job_csv_test',
		);

		$dataUpdate = array(
			0 => 'promotion',
			1 => 'job_csv_test',
		);

		$category = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Catalog_Default( $this->context, $mapping, $this->endpoint );
		$result = $object->process( self::$product, $data );
		$result = $object->process( self::$product, $dataUpdate );

		$category = $this->get( 'job_csv_test' );
		$this->delete( $category );


		$listItems = $category->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_List_Iface', $listItem );

		$this->assertEquals( 'job_csv_prod', $listItem->getRefItem()->getCode() );
	}


	public function testProcessDelete()
	{
		$mapping = array(
			0 => 'catalog.list.type',
			1 => 'catalog.code',
		);

		$data = array(
			0 => 'default',
			1 => 'job_csv_test',
		);

		$category = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Catalog_Default( $this->context, $mapping, $this->endpoint );
		$result = $object->process( self::$product, $data );

		$object = new Controller_Common_Product_Import_Csv_Processor_Catalog_Default( $this->context, array(), $this->endpoint );
		$result = $object->process( self::$product, array() );

		$category = $this->get( 'job_csv_test' );
		$this->delete( $category );


		$listItems = $category->getListItems();

		$this->assertEquals( 0, count( $listItems ) );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'catalog.list.type',
			1 => 'catalog.code',
			2 => 'catalog.list.type',
			3 => 'catalog.code',
		);

		$data = array(
			0 => '',
			1 => '',
			2 => 'default',
			3 => 'job_csv_test',
		);

		$category = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Catalog_Default( $this->context, $mapping, $this->endpoint );
		$result = $object->process( self::$product, $data );

		$category = $this->get( 'job_csv_test' );
		$this->delete( $category );


		$listItems = $category->getListItems();

		$this->assertEquals( 1, count( $listItems ) );
	}


	public function testProcessListtypes()
	{
		$mapping = array(
			0 => 'catalog.list.type',
			1 => 'catalog.code',
			2 => 'catalog.list.type',
			3 => 'catalog.code',
		);

		$data = array(
			0 => 'promotion',
			1 => 'job_csv_test',
			2 => 'default',
			3 => 'job_csv_test',
		);

		$this->context->getConfig()->set( 'controller/common/product/import/csv/processor/catalog/listtypes', array( 'default' ) );

		$category = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Catalog_Default( $this->context, $mapping, $this->endpoint );
		$result = $object->process( self::$product, $data );

		$category = $this->get( 'job_csv_test' );
		$this->delete( $category );


		$listItems = $category->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_List_Iface', $listItem );

		$this->assertEquals( 'default', $listItem->getType() );
		$this->assertEquals( 'job_csv_prod', $listItem->getRefItem()->getCode() );
	}


	protected function create( $code )
	{
		$manager = MShop_Catalog_Manager_Factory::createManager( $this->context );

		$item = $manager->createItem();
		$item->setCode( $code );

		$manager->insertItem( $item );

		return $item;
	}


	protected function delete( MShop_Catalog_Item_Iface $catItem )
	{
		$manager = MShop_Catalog_Manager_Factory::createManager( $this->context );
		$listManager = $manager->getSubManager( 'list' );

		foreach( $catItem->getListItems('product') as $listItem ) {
			$listManager->deleteItem( $listItem->getId() );
		}

		$manager->deleteItem( $catItem->getId() );
	}


	protected function get( $code )
	{
		$manager = MShop_Catalog_Manager_Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', $code ) );

		$result = $manager->searchItems( $search, array('product') );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No catalog item for code "%1$s"', $code ) );
		}

		return $item;
	}
}