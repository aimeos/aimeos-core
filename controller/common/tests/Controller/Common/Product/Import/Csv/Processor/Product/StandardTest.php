<?php

namespace Aimeos\Controller\Common\Product\Import\Csv\Processor\Product;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $endpoint;


	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$this->context = \TestHelperCntl::getContext();
		$this->endpoint = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Done( $this->context, array() );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();
	}


	public function testProcess()
	{
		$mapping = array(
			0 => 'product.lists.type',
			1 => 'product.code',
			2 => 'product.lists.type',
			3 => 'product.code',
		);

		$data = array(
			0 => 'default',
			1 => 'CNC',
			2 => 'suggestion',
			3 => 'CNE',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Product\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems1 = $product->getListItems( 'product', 'default' );
		$listItems2 = $product->getListItems( 'product', 'suggestion' );

		$this->assertEquals( 1, count( $listItems1 ) );
		$this->assertEquals( 1, count( $listItems2 ) );

		$this->assertEquals( 1, reset( $listItems1 )->getStatus() );
		$this->assertEquals( 1, reset( $listItems2 )->getStatus() );

		$this->assertEquals( 'CNC', reset( $listItems1 )->getRefItem()->getCode() );
		$this->assertEquals( 'CNE', reset( $listItems2 )->getRefItem()->getCode() );
	}


	public function testProcessMultiple()
	{
		$mapping = array(
			0 => 'product.lists.type',
			1 => 'product.code',
		);

		$data = array(
			0 => 'default',
			1 => "CNC\nCNE",
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Product\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$pos = 0;
		$codes = array( 'CNC', 'CNE' );
		$listItems = $product->getListItems();

		$this->assertEquals( 2, count( $listItems ) );

		foreach( $listItems as $listItem )
		{
			$this->assertEquals( 1, $listItem->getStatus() );
			$this->assertEquals( 'product', $listItem->getDomain() );
			$this->assertEquals( 'default', $listItem->getType() );
			$this->assertEquals( $codes[$pos], $listItem->getRefItem()->getCode() );
			$pos++;
		}
	}


	public function testProcessUpdate()
	{
		$mapping = array(
			0 => 'product.lists.type',
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

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Product\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$result = $object->process( $product, $dataUpdate );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $listItem );

		$this->assertEquals( 'CNE', $listItem->getRefItem()->getCode() );
	}


	public function testProcessDelete()
	{
		$mapping = array(
			0 => 'product.lists.type',
			1 => 'product.code',
		);

		$data = array(
			0 => 'default',
			1 => 'CNC',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Product\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Product\Standard( $this->context, array(), $this->endpoint );
		$result = $object->process( $product, array() );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$this->assertEquals( 0, count( $product->getListItems() ) );
	}


	public function testProcessEmpty()
	{
		$mapping = array(
			0 => 'product.lists.type',
			1 => 'product.code',
			2 => 'product.lists.type',
			3 => 'product.code',
		);

		$data = array(
			0 => '',
			1 => '',
			2 => 'default',
			3 => 'CNE',
		);

		$product = $this->create( 'job_csv_test' );

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Product\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();

		$this->assertEquals( 1, count( $listItems ) );
	}


	public function testProcessListtypes()
	{
		$mapping = array(
			0 => 'product.lists.type',
			1 => 'product.code',
			2 => 'product.lists.type',
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

		$object = new \Aimeos\Controller\Common\Product\Import\Csv\Processor\Product\Standard( $this->context, $mapping, $this->endpoint );
		$result = $object->process( $product, $data );

		$product = $this->get( 'job_csv_test' );
		$this->delete( $product );


		$listItems = $product->getListItems();
		$listItem = reset( $listItems );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $listItem );

		$this->assertEquals( 'default', $listItem->getType() );
		$this->assertEquals( 'CNE', $listItem->getRefItem()->getCode() );
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
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$listManager = $manager->getSubManager( 'lists' );

		foreach( $product->getListItems('product') as $listItem ) {
			$listManager->deleteItem( $listItem->getId() );
		}

		$manager->deleteItem( $product->getId() );
	}


	protected function get( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search, array('product') );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Exception( sprintf( 'No product item for code "%1$s"', $code ) );
		}

		return $item;
	}
}
