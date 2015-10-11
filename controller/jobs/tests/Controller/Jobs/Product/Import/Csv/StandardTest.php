<?php

namespace Aimeos\Controller\Jobs\Product\Import\Csv;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;
	private $aimeos;


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
		$this->aimeos = \TestHelper::getAimeos();
		$config = $this->context->getConfig();

		$config->set( 'controller/jobs/product/import/csv/skip-lines', 1 );
		$config->set( 'controller/jobs/product/import/csv/location', __DIR__ . '/_testfiles/valid' );

		$this->object = new \Aimeos\Controller\Jobs\Product\Import\Csv\Standard( $this->context, $this->aimeos );
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

		$this->object = null;

		if( file_exists( 'tmp/import.zip' ) ) {
			unlink( 'tmp/import.zip' );
		}
	}


	public function testGetName()
	{
		$this->assertEquals( 'Product import CSV', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Imports new and updates existing products from CSV files';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$prodcodes = array( 'job_csv_test', 'job_csv_test2' );
		$nondelete = array( 'attribute', 'product' );
		$delete = array( 'media', 'price', 'text' );

		$convert = array(
			1 => 'Text/LatinUTF8',
		);

		$this->context->getConfig()->set( 'controller/jobs/product/import/csv/converter', $convert );

		$this->object->run();

		$result = $this->get( $prodcodes, array_merge( $delete, $nondelete ) );
		$properties = $this->getProperties( array_keys( $result ) );
		$this->delete( $prodcodes, $delete, $nondelete );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 2, count( $properties ) );

		foreach( $result as $product ) {
			$this->assertEquals( 5, count( $product->getListItems() ) );
		}
	}


	public function testRunUpdate()
	{
		$prodcodes = array( 'job_csv_test', 'job_csv_test2' );
		$nondelete = array( 'attribute', 'product' );
		$delete = array( 'media', 'price', 'text' );

		$this->object->run();
		$this->object->run();

		$result = $this->get( $prodcodes, array_merge( $delete, $nondelete ) );
		$properties = $this->getProperties( array_keys( $result ) );
		$this->delete( $prodcodes, $delete, $nondelete );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 2, count( $properties ) );

		foreach( $result as $product ) {
			$this->assertEquals( 5, count( $product->getListItems() ) );
		}
	}


	public function testRunProcessorInvalidPosition()
	{
		$prodcodes = array( 'job_csv_test', 'job_csv_test2' );

		$mapping = array(
			'item' => array(
				0 => 'product.code',
				1 => 'product.label',
				2 => 'product.type',
				3 => 'product.status',
			),
			'text' => array(
				4 => 'text.type',
				5 => 'text.content',
				100 => 'text.type',
				101 => 'text.content',
			),
			'media' => array(
				8 => 'media.url',
			),
		);

		$this->context->getConfig()->set( 'controller/jobs/product/import/csv/mapping', $mapping );

		$this->object->run();

		$this->delete( $prodcodes, array( 'text', 'media' ), array() );
	}


	public function testRunProcessorInvalidMapping()
	{
		$mapping = array(
			'media' => array(
					8 => 'media.url',
			),
		);

		$this->context->getConfig()->set( 'controller/jobs/product/import/csv/mapping', $mapping );

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->run();
	}


	public function testRunProcessorInvalidData()
	{
		$mapping = array(
			'item' => array(
				0 => 'product.code',
				1 => 'product.label',
				2 => 'product.type',
			),
			'text' => array(
				3 => 'text.type',
				4 => 'text.content',
			),
			'media' => array(
				5 => 'media.url',
				6 => 'product.lists.type',
			),
			'price' => array(
				7 => 'price.type',
				8 => 'price.value',
				9 => 'price.taxrate',
			),
			'attribute' => array(
				10 => 'attribute.type',
				11 => 'attribute.code',
			),
			'product' => array(
				12 => 'product.code',
				13 => 'product.lists.type',
			),
			'property' => array(
				14 => 'product.property.type',
				15 => 'product.property.value',
			),
		);

		$this->context->getConfig()->set( 'controller/jobs/product/import/csv/mapping', $mapping );

		$config = $this->context->getConfig();
		$config->set( 'controller/jobs/product/import/csv/skip-lines', 0 );
		$config->set( 'controller/jobs/product/import/csv/location', __DIR__ . '/_testfiles/invalid' );

		$this->object = new \Aimeos\Controller\Jobs\Product\Import\Csv\Standard( $this->context, $this->aimeos );

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->run();
	}


	public function testRunBackup()
	{
		$config = $this->context->getConfig();
		$config->set( 'controller/jobs/product/import/csv/container/type', 'Zip' );
		$config->set( 'controller/jobs/product/import/csv/location', 'tmp/import.zip' );
		$config->set( 'controller/jobs/product/import/csv/backup', 'tmp/test-%Y-%m-%d.zip' );

		if( copy( __DIR__ . '/_testfiles/import.zip', 'tmp/import.zip' ) === false ) {
			throw new \Exception( 'Unable to copy test file' );
		}

		$this->object->run();

		$filename = strftime( 'tmp/test-%Y-%m-%d.zip' );
		$this->assertTrue( file_exists( $filename ) );

		unlink( $filename );
	}


	public function testRunBackupInvalid()
	{
		$config = $this->context->getConfig();
		$config->set( 'controller/jobs/product/import/csv/container/type', 'Zip' );
		$config->set( 'controller/jobs/product/import/csv/location', 'tmp/import.zip' );
		$config->set( 'controller/jobs/product/import/csv/backup', 'tmp/notexist/import.zip' );

		if( copy( __DIR__ . '/_testfiles/import.zip', 'tmp/import.zip' ) === false ) {
			throw new \Exception( 'Unable to copy test file' );
		}

		$this->setExpectedException( '\\Aimeos\\Controller\\Jobs\\Exception' );
		$this->object->run();
	}


	protected function delete( array $prodcodes, array $delete, array $nondelete )
	{
		$catListManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context )->getSubmanager( 'lists' );
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$listManager = $productManager->getSubManager( 'lists' );

		foreach( $this->get( $prodcodes, $delete + $nondelete ) as $id => $product )
		{
			foreach( $delete as $domain )
			{
				$manager = \Aimeos\MShop\Factory::createManager( $this->context, $domain );

				foreach( $product->getListItems( $domain ) as $listItem )
				{
					$manager->deleteItem( $listItem->getRefItem()->getId() );
					$listManager->deleteItem( $listItem->getId() );
				}
			}

			foreach( $nondelete as $domain )
			{
				$ids = array_keys( $product->getListItems( $domain ) );
				$listManager->deleteItems( $ids );
			}

			$productManager->deleteItem( $product->getId() );

			$search = $catListManager->createSearch();
			$search->setConditions( $search->compare( '==', 'catalog.lists.refid', $id ) );
			$result = $catListManager->searchItems( $search );

			$catListManager->deleteItems( array_keys( $result ) );
		}


		$attrManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->context );

		$search = $attrManager->createSearch();
		$search->setConditions( $search->compare( '==', 'attribute.code', 'import-test' ) );

		$result = $attrManager->searchItems( $search );

		$attrManager->deleteItems( array_keys( $result ) );
	}


	protected function get( array $prodcodes, array $domains )
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $prodcodes ) );

		return $productManager->searchItems( $search, $domains );
	}


	protected function getProperties( array $prodids )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context )->getSubManager( 'property' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.property.parentid', $prodids ) );
		$search->setSortations( array( $search->sort( '+', 'product.property.type.code' ) ) );

		return $manager->searchItems( $search );
	}
}