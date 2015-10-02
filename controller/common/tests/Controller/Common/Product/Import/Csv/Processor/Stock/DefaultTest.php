<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Common_Product_Import_Csv_Processor_Stock_DefaultTest extends PHPUnit_Framework_TestCase
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
			0 => 'product.stock.stocklevel',
			1 => 'product.stock.dateback',
		);

		$data = array(
			0 => '100',
			1 => '2000-01-01 00:00:00',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Stock_Default( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$items = $this->getStockItems( $product->getId() );
		$this->delete( $product );


		$this->assertEquals( 1, count( $items ) );

		foreach( $items as $item )
		{
			$this->assertEquals( 100, $item->getStocklevel() );
			$this->assertEquals( '2000-01-01 00:00:00', $item->getDateBack() );
		}
	}


	public function testProcessMultiple()
	{
		$mapping = array(
			0 => 'product.stock.warehouse',
			1 => 'product.stock.stocklevel',
			2 => 'product.stock.warehouse',
			3 => 'product.stock.stocklevel',
		);

		$data = array(
			0 => 'unit_warehouse1',
			1 => '200',
			2 => 'unit_warehouse2',
			3 => '200',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Stock_Default( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$items = $this->getStockItems( $product->getId() );
		$this->delete( $product );


		$this->assertEquals( 2, count( $items ) );

		foreach( $items as $item ) {
			$this->assertEquals( 200, $item->getStocklevel() );
		}
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'product.stock.stocklevel',
		);

		$data = array(
			0 => '10',
		);

		$dataUpdate = array(
			0 => '20',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Stock_Default( $this->context, $mapping, $this->endpoint );

		$result = $object->process( $product, $data );
		$result = $object->process( $product, $dataUpdate );

		$items = $this->getStockItems( $product->getId() );
		$this->delete( $product );


		$item = reset( $items );

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 20, $item->getStocklevel() );
	}


	public function testProcessDelete()
	{
		$mapping = array(
			0 => 'product.stock.stocklevel',
		);

		$data = array(
			0 => 50,
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Stock_Default( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$object = new Controller_Common_Product_Import_Csv_Processor_Stock_Default( $this->context, array(), $this->endpoint );
		$result = $object->process( $product, array() );

		$items = $this->getStockItems( $product->getId() );
		$this->delete( $product );


		$this->assertEquals( 0, count( $items ) );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'product.stock.warehouse',
			1 => 'product.stock.stocklevel',
			2 => 'product.stock.dateback',
		);

		$data = array(
			0 => 'unit_warehouse1',
			1 => '',
			2 => '',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Stock_Default( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$items = $this->getStockItems( $product->getId() );
		$this->delete( $product );

		$this->assertEquals( 1, count( $items ) );

		foreach( $items as $item )
		{
			$this->assertEquals( null, $item->getStocklevel() );
			$this->assertEquals( null, $item->getDateBack() );
		}
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


	protected function delete( MShop_Product_Item_Interface $product )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->context );
		$manager->deleteItem( $product->getId() );
	}


	protected function getStockItems( $prodid )
	{
		$manager = MShop_Factory::createManager( $this->context, 'product/stock' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.productid', $prodid ) );

		return $manager->searchItems( $search );
	}
}