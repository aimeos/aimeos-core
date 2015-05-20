<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_Product_Import_Csv_Processor_Media_DefaultTest extends MW_Unittest_Testcase
{
	private $_context;
	private $_endpoint;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$this->_context = TestHelper::getContext();
		$this->_endpoint = new Controller_Jobs_Product_Import_Csv_Processor_Done( $this->_context, array() );
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
			0 => 'media.languageid',
			1 => 'media.label',
			2 => 'media.mimetype',
			3 => 'media.preview',
			4 => 'media.url',
			5 => 'media.status',
		);

		$data = array(
			0 => 'de',
			1 => 'test image',
			2 => 'image/jpeg',
			3 => 'path/to/preview',
			4 => 'path/to/file',
			5 => 1,
		);

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Jobs_Product_Import_Csv_Processor_Media_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertInstanceOf( 'MShop_Common_Item_List_Interface', $listItem );
		$this->assertEquals( 1, count( $listItems ) );

		$this->assertEquals( 1, $listItem->getStatus() );
		$this->assertEquals( 0, $listItem->getPosition() );
		$this->assertEquals( 'media', $listItem->getDomain() );
		$this->assertEquals( 'default', $listItem->getType() );

		$refItem = $listItem->getRefItem();

		$this->assertEquals( 1, $refItem->getStatus() );
		$this->assertEquals( 'default', $refItem->getType() );
		$this->assertEquals( 'product', $refItem->getDomain() );
		$this->assertEquals( 'test image', $refItem->getLabel() );
		$this->assertEquals( 'image/jpeg', $refItem->getMimetype() );
		$this->assertEquals( 'path/to/preview', $refItem->getPreview() );
		$this->assertEquals( 'path/to/file', $refItem->getUrl() );
		$this->assertEquals( 'de', $refItem->getLanguageId() );
	}


	public function testProcessMultiple()
	{
		$mapping = array(
			0 => 'media.url',
			1 => 'media.url',
			2 => 'media.url',
			3 => 'media.url',
		);

		$data = array(
			0 => 'path/to/0',
			1 => 'path/to/1',
			2 => 'path/to/2',
			3 => 'path/to/3',
		);

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Jobs_Product_Import_Csv_Processor_Media_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$pos = 0;
		$listItems = $product->getListItems();

		$this->assertEquals( 4, count( $listItems ) );

		foreach( $listItems as $listItem )
		{
			$this->assertEquals( $data[$pos], $listItem->getRefItem()->getUrl() );
			$pos++;
		}
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'media.url',
		);

		$data = array(
			0 => 'path/to/file',
		);

		$dataUpdate = array(
			0 => 'path/to/new',
		);

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Jobs_Product_Import_Csv_Processor_Media_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );

		$result = $object->process( $product, $dataUpdate );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_List_Interface', $listItem );

		$this->assertEquals( 'path/to/new', $listItem->getRefItem()->getUrl() );
	}


	public function testProcessDelete()
	{
		$mapping = array(
			0 => 'media.url',
		);

		$data = array(
			0 => '/path/to/file',
		);

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Jobs_Product_Import_Csv_Processor_Media_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );

		$object = new Controller_Jobs_Product_Import_Csv_Processor_Media_Default( $this->_context, array(), $this->_endpoint );
		$result = $object->process( $product, array() );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 0, count( $listItems ) );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'media.url',
			1 => 'media.url',
		);

		$data = array(
			0 => 'path/to/file',
			1 => '',
		);

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Jobs_Product_Import_Csv_Processor_Media_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 1, count( $listItems ) );
	}


	public function testProcessListtypes()
	{
		$mapping = array(
			0 => 'media.url',
			1 => 'product.list.type',
			2 => 'media.url',
			3 => 'product.list.type',
		);

		$data = array(
			0 => 'path/to/file',
			1 => 'download',
			2 => 'path/to/file2',
			3 => 'default',
		);

		$this->_context->getConfig()->set( 'controller/jobs/product/import/csv/processor/media/listtypes', array( 'default' ) );

		$product = $this->_create( 'job_csv_test' );

		$object = new Controller_Jobs_Product_Import_Csv_Processor_Media_Default( $this->_context, $mapping, $this->_endpoint );
		$result = $object->process( $product, $data );

		$product = $this->_get( 'job_csv_test' );
		$this->_delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( 'MShop_Common_Item_List_Interface', $listItem );

		$this->assertEquals( 'default', $listItem->getType() );
		$this->assertEquals( 'path/to/file2', $listItem->getRefItem()->getUrl() );
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
		$mediaManager = MShop_Media_Manager_Factory::createManager( $this->_context );
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$listManager = $manager->getSubManager( 'list' );

		foreach( $product->getListItems('media') as $listItem )
		{
			$mediaManager->deleteItem( $listItem->getRefItem()->getId() );
			$listManager->deleteItem( $listItem->getId() );
		}

		$manager->deleteItem( $product->getId() );
	}


	protected function _get( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search, array('media') );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No product item for code "%1$s"', $code ) );
		}

		return $item;
	}
}