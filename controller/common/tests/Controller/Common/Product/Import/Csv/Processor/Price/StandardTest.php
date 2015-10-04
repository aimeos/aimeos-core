<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Common_Product_Import_Csv_Processor_Price_StandardTest extends PHPUnit_Framework_TestCase
{
	private $context;
	private $endpoint;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$this->context = TestHelper::getContext();
		$this->endpoint = new Controller_Common_Product_Import_Csv_Processor_Done( $this->context, array() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		MShop_Factory::setCache( false );
		MShop_Factory::clear();
	}


	public function testProcess()
	{
		$mapping = array(
			0 => 'price.type',
			1 => 'price.label',
			2 => 'price.currencyid',
			3 => 'price.quantity',
			4 => 'price.value',
			5 => 'price.costs',
			6 => 'price.rebate',
			7 => 'price.taxrate',
			8 => 'price.status',
		);

		$data = array(
			0 => 'default',
			1 => 'EUR 1.00',
			2 => 'EUR',
			3 => 5,
			4 => '1.00',
			5 => '0.20',
			6 => '0.10',
			7 => '20.00',
			8 => 1,
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Price_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertInstanceOf( 'MShop_Common_Item_Lists_Iface', $listItem );
		$this->assertEquals( 1, count( $listItems ) );

		$this->assertEquals( 1, $listItem->getStatus() );
		$this->assertEquals( 0, $listItem->getPosition() );
		$this->assertEquals( 'price', $listItem->getDomain() );
		$this->assertEquals( 'default', $listItem->getType() );

		$refItem = $listItem->getRefItem();

		$this->assertEquals( 1, $refItem->getStatus() );
		$this->assertEquals( 'default', $refItem->getType() );
		$this->assertEquals( 'product', $refItem->getDomain() );
		$this->assertEquals( 'EUR 1.00', $refItem->getLabel() );
		$this->assertEquals( 5, $refItem->getQuantity() );
		$this->assertEquals( '1.00', $refItem->getValue() );
		$this->assertEquals( '0.20', $refItem->getCosts() );
		$this->assertEquals( '0.10', $refItem->getRebate() );
		$this->assertEquals( '20.00', $refItem->getTaxrate() );
		$this->assertEquals( 1, $refItem->getStatus() );
	}


	public function testProcessMultiple()
	{
		$mapping = array(
			0 => 'price.value',
			1 => 'price.value',
			2 => 'price.value',
			3 => 'price.value',
		);

		$data = array(
			0 => '1.00',
			1 => '2.00',
			2 => '3.00',
			3 => '4.00',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Price_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$pos = 0;
		$listItems = $product->getListItems();

		$this->assertEquals( 4, count( $listItems ) );

		foreach( $listItems as $listItem )
		{
			$this->assertEquals( $data[$pos], $listItem->getRefItem()->getValue() );
			$pos++;
		}
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'price.value',
		);

		$data = array(
			0 => '1.00',
		);

		$dataUpdate = array(
			0 => '2.00',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Price_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$result = $object->process( $product, $dataUpdate );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_Lists_Iface', $listItem );

		$this->assertEquals( '2.00', $listItem->getRefItem()->getValue() );
	}


	public function testProcessDelete()
	{
		$mapping = array(
			0 => 'price.value',
		);

		$data = array(
			0 => '1.00',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Price_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Price_Standard( $this->context, array(), $this->endpoint );
		$result = $object->process( $product, array() );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 0, count( $listItems ) );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'price.value',
			1 => 'price.value',
		);

		$data = array(
			0 => '1.00',
			1 => '',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Price_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 1, count( $listItems ) );
	}


	public function testProcessListtypes()
	{
		$mapping = array(
			0 => 'price.value',
			1 => 'product.list.type',
			2 => 'price.value',
			3 => 'product.list.type',
		);

		$data = array(
			0 => '1.00',
			1 => 'test',
			2 => '2.00',
			3 => 'default',
		);

		$this->context->getConfig()->set( 'controller/common/product/import/csv/processor/price/listtypes', array( 'default' ) );

		$product = $this->create( 'job_csv_test' );

		$object = new Controller_Common_Product_Import_Csv_Processor_Price_Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_Lists_Iface', $listItem );

		$this->assertEquals( 'default', $listItem->getType() );
		$this->assertEquals( '2.00', $listItem->getRefItem()->getValue() );
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
		$priceManager = MShop_Price_Manager_Factory::createManager( $this->context );
		$manager = MShop_Product_Manager_Factory::createManager( $this->context );
		$listManager = $manager->getSubManager( 'list' );

		foreach( $product->getListItems('price') as $listItem )
		{
			$priceManager->deleteItem( $listItem->getRefItem()->getId() );
			$listManager->deleteItem( $listItem->getId() );
		}

		$manager->deleteItem( $product->getId() );
	}


	protected function get( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search, array('price') );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No product item for code "%1$s"', $code ) );
		}

		return $item;
	}
}