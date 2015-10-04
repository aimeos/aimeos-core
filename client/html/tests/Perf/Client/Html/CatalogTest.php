<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Perf_Client_Html_CatalogTest extends PHPUnit_Framework_TestCase
{
	protected $context;
	protected $paths;
	protected $view;


	protected function setUp()
	{
		$this->paths = TestHelper::getHtmlTemplatePaths();
		$this->context = TestHelper::getContext( 'unitperf' );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'home' ) );
		$items = $catalogManager->searchItems( $search );

		if( ( $catalogItem = reset( $items ) ) === false ) {
			throw new Exception( 'No catalog item with code "home" found' );
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $this->context );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'perf-00000' ) );
		$items = $productManager->searchItems( $search );

		if( ( $productItem = reset( $items ) ) === false ) {
			throw new Exception( 'No product item with code "perf-00000" found' );
		}

		$this->view = TestHelper::getView( 'unitperf' );

		$param = array(
			'f_catid' => $catalogItem->getId(),
			'd_prodid' => $productItem->getId()
		);
		$helper = new MW_View_Helper_Parameter_Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
	}


	public function testFilter()
	{
		// parser warm up so files are already parsed (same as APC is used)
		$client = Client_Html_Catalog_Filter_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();
		$client->getHeader();


		$start = microtime( true );

		$client = Client_Html_Catalog_Filter_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog filter: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testFilterHeader()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_Filter_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();

		$stop = microtime( true );
		echo "\n    catalog filter header: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testFilterBody()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_Filter_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog filter body: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testList()
	{
		// parser warm up so files are already parsed (same as APC is used)
		$client = Client_Html_Catalog_List_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();
		$client->getHeader();


		$start = microtime( true );

		$client = Client_Html_Catalog_List_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog list: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testListHeader()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_List_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();

		$stop = microtime( true );
		echo "\n    catalog list header: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testListBody()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_List_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog list body: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testDetail()
	{
		// parser warm up so files are already parsed (same as APC is used)
		$client = Client_Html_Catalog_Detail_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();
		$client->getHeader();


		$start = microtime( true );

		$client = Client_Html_Catalog_Detail_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog detail: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testDetailHeader()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_Detail_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getHeader();

		$stop = microtime( true );
		echo "\n    catalog detail header: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testDetailBody()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_Detail_Factory::createClient( $this->context, $this->paths );
		$client->setView( $this->view );
		$client->getBody();

		$stop = microtime( true );
		echo "\n    catalog detail body: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}
}
