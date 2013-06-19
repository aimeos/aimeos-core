<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Perf_Client_Html_CatalogTest extends MW_Unittest_Testcase
{
	protected static $_context;
	protected static $_paths;
	protected static $_view;


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('Perf_Client_Html_CatalogTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public static function setUpBeforeClass()
	{
		self::$_paths = TestHelper::getHtmlTemplatePaths();
		self::$_context = TestHelper::getContext( 'unitperf' );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( self::$_context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'home' ) );
		$items = $catalogManager->searchItems( $search );

		if( ( $catalogItem = reset( $items ) ) === false ) {
			throw new Exception( 'No catalog item with code "home" found' );
		}

		$productManager = MShop_Product_Manager_Factory::createManager( self::$_context );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'perf-00000' ) );
		$items = $productManager->searchItems( $search );

		if( ( $productItem = reset( $items ) ) === false ) {
			throw new Exception( 'No product item with code "perf-00000" found' );
		}

		self::$_view = TestHelper::getView();

		$param = array(
			'f-catalog-id' => $catalogItem->getId(),
			'l-product-id' => $productItem->getId()
		);
		$helper = new MW_View_Helper_Parameter_Default( self::$_view, $param );
		self::$_view->addHelper( 'param', $helper );
	}


	protected function setUp()
	{
	}


	public function testFilter()
	{
		// parser warm up so files are already parsed (same as APC is used)
		$client = Client_Html_Catalog_Filter_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getBody();
		$result = $client->getHeader();


		$start = microtime( true );

		$client = Client_Html_Catalog_Filter_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getHeader();
		$result = $client->getBody();

		$stop = microtime( true );
		echo "\n    catalog filter: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testFilterHeader()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_Filter_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getHeader();

		$stop = microtime( true );
		echo "\n    catalog filter header: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testFilterBody()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_Filter_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getBody();

		$stop = microtime( true );
		echo "\n    catalog filter body: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testList()
	{
		// parser warm up so files are already parsed (same as APC is used)
		$client = Client_Html_Catalog_List_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getBody();
		$result = $client->getHeader();


		$start = microtime( true );

		$client = Client_Html_Catalog_List_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getHeader();
		$result = $client->getBody();

		$stop = microtime( true );
		echo "\n    catalog list: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testListHeader()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_List_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getHeader();

		$stop = microtime( true );
		echo "\n    catalog list header: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testListBody()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_List_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getBody();

		$stop = microtime( true );
		echo "\n    catalog list body: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testDetail()
	{
		// parser warm up so files are already parsed (same as APC is used)
		$client = Client_Html_Catalog_Detail_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getBody();
		$result = $client->getHeader();


		$start = microtime( true );

		$client = Client_Html_Catalog_Detail_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getHeader();
		$result = $client->getBody();

		$stop = microtime( true );
		echo "\n    catalog detail: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testDetailHeader()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_Detail_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getHeader();

		$stop = microtime( true );
		echo "\n    catalog detail header: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}


	public function testDetailBody()
	{
		$start = microtime( true );

		$client = Client_Html_Catalog_Detail_Factory::createClient( self::$_context, self::$_paths );
		$client->setView( self::$_view );
		$result = $client->getBody();

		$stop = microtime( true );
		echo "\n    catalog detail body: " . ( ( $stop - $start ) * 1000 ) . " msec\n";
	}
}
