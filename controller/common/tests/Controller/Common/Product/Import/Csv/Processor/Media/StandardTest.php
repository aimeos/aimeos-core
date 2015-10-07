<?php

namespace Aimeos\Controller\Common\Product\Import\Csv\Processor\Media;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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
		\Aimeos\MShop\Factory::setCache( true );

		$this->context = \TestHelper::getContext();
		$this->endpoint = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Done( $this->context, array() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();
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

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $listItem );
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
		);

		$data = array(
			0 => "path/to/0\npath/to/1\npath/to/2\npath/to/3",
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$pos = 0;
		$listItems = $product->getListItems();
		$expected = array( 'path/to/0', 'path/to/1', 'path/to/2', 'path/to/3' );

		$this->assertEquals( 4, count( $listItems ) );

		foreach( $listItems as $listItem )
		{
			$this->assertEquals( $expected[$pos], $listItem->getRefItem()->getUrl() );
			$pos++;
		}
	}


	public function testProcessMultipleFields()
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

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


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

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$result = $object->process( $product, $dataUpdate );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $listItem );

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

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, array(), $this->endpoint );
		$result = $object->process( $product, array() );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


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

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 1, count( $listItems ) );
	}


	public function testProcessListtypes()
	{
		$mapping = array(
			0 => 'media.url',
			1 => 'product.lists.type',
			2 => 'media.url',
			3 => 'product.lists.type',
		);

		$data = array(
			0 => 'path/to/file',
			1 => 'download',
			2 => 'path/to/file2',
			3 => 'default',
		);

		$this->context->getConfig()->set( 'controller/common/product/import/csv/processor/media/listtypes', array( 'default' ) );

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Media\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $listItem );

		$this->assertEquals( 'default', $listItem->getType() );
		$this->assertEquals( 'path/to/file2', $listItem->getRefItem()->getUrl() );
	}


	protected function create( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$typeManager = $manager->getSubManager( 'type' );

		$typeSearch = $typeManager->createSearch();
		$typeSearch->setConditions( $typeSearch->compare( '==', 'product.type.code', 'default' ) );
		$typeResult = $typeManager->searchItems( $typeSearch );

		if( ( $typeItem = reset( $typeResult ) ) === false ) {
			throw new \Exception( 'No product type "default" found' );
		}

		$item = $manager->createItem();
		$item->setTypeid( $typeItem->getId() );
		$item->setCode( $code );

		$manager->saveItem( $item );

		return $item;
	}


	protected function delete( \Aimeos\MShop\Product\Item\Iface $product )
	{
		$mediaManager = \Aimeos\MShop\Media\Manager\Factory::createManager( $this->context );
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$listManager = $manager->getSubManager( 'lists' );

		foreach( $product->getListItems('media') as $listItem )
		{
			$mediaManager->deleteItem( $listItem->getRefItem()->getId() );
			$listManager->deleteItem( $listItem->getId() );
		}

		$manager->deleteItem( $product->getId() );
	}


	protected function get( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search, array('media') );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Exception( sprintf( 'No product item for code "%1$s"', $code ) );
		}

		return $item;
	}
}