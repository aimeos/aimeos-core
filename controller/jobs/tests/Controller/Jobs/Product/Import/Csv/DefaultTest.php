<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_Product_Import_Csv_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;
	private $_arcavias;


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
		$this->_arcavias = TestHelper::getArcavias();

		$this->_context->getConfig()->set( 'controller/jobs/product/import/csv/default/location', __DIR__ . '/_testfiles' );
		$this->_object = new Controller_Jobs_Product_Import_Csv_Default( $this->_context, $this->_arcavias );
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

		$this->_object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Product import CSV', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Imports new and updates existing products from CSV files';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$prodcodes = array( 'job_csv_test', 'job_csv_test2' );
		$nondelete = array( 'attribute', 'product' );
		$delete = array( 'media', 'price', 'text' );

		$this->_object->run();

		$result = $this->_get( $prodcodes, array_merge( $delete, $nondelete ) );
		$properties = $this->_getProperties( array_keys( $result ) );
		$this->_delete( $prodcodes, $delete, $nondelete );

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

		$this->_object->run();
		$this->_object->run();

		$result = $this->_get( $prodcodes, array_merge( $delete, $nondelete ) );
		$properties = $this->_getProperties( array_keys( $result ) );
		$this->_delete( $prodcodes, $delete, $nondelete );

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

		$this->_context->getConfig()->set( 'controller/jobs/product/import/csv/default/mapping', $mapping );

		$this->_object->run();

		$this->_delete( $prodcodes, array( 'text', 'media' ), array() );
	}


	protected function _delete( array $prodcodes, array $delete, array $nondelete )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$listManager = $productManager->getSubManager( 'list' );

		foreach( $this->_get( $prodcodes, $delete + $nondelete ) as $id => $product )
		{
			foreach( $delete as $domain )
			{
				$manager = MShop_Factory::createManager( $this->_context, $domain );

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
		}
	}


	protected function _get( array $prodcodes, array $domains )
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $prodcodes ) );

		return $productManager->searchItems( $search, $domains );
	}


	protected function _getProperties( array $prodids )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context )->getSubManager( 'property' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.property.parentid', $prodids ) );
		$search->setSortations( array( $search->sort( '+', 'product.property.type.code' ) ) );

		return $manager->searchItems( $search );
	}
}