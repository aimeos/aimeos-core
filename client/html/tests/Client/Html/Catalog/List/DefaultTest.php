<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Catalog_List_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_List_Default( $this->_context, $paths );
		$this->_object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f_catid' => $this->_getCatalogItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getHeader( 1, $tags, $expire );

		$this->assertStringStartsWith( '	<title>Kaffee</title>', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 4, count( $tags ) );
	}


	public function testGetHeaderSearch()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f_search' => '<b>Search result</b>' ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getHeader( 1, $tags, $expire );

		$this->assertRegexp( '#<title>[^>]*Search result[^<]*</title>#', $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testGetBody()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f_catid' => $this->_getCatalogItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<section class="aimeos catalog-list home categories coffee">', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 4, count( $tags ) );
	}


	public function testGetBodyNoDefaultCat()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array() );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos catalog-list">', $output );
		$this->assertNotRegExp( '#.*U:TESTPSUB01.*#smu', $output );
		$this->assertNotRegExp( '#.*U:TESTSUB03.*#smu', $output );
		$this->assertNotRegExp( '#.*U:TESTSUB04.*#smu', $output );
		$this->assertNotRegExp( '#.*U:TESTSUB05.*#smu', $output );
	}


	public function testGetBodyDefaultCat()
	{
		$context = clone $this->_context;
		$context->getConfig()->set( 'client/html/catalog/list/catid-default', $this->_getCatalogItem()->getId() );

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_List_Default( $context, $paths );
		$this->_object->setView( TestHelper::getView() );

		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array() );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos catalog-list home categories coffee">', $output );
	}


	public function testGetBodyCategoryLevels()
	{
		$context = clone $this->_context;
		$context->getConfig()->set( 'client/html/catalog/lists/levels', MW_Tree_Manager_Abstract::LEVEL_TREE );

		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->_object = new \Client_Html_Catalog_List_Default( $context, $paths );
		$this->_object->setView( \TestHelper::getView() );

		$view = $this->_object->getView();
		$helper = new \MW_View_Helper_Parameter_Default( $view, array( 'f_catid' => $this->_getCatalogItem( 'root' )->getId() ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertRegExp( '#.*Cafe Noire Cappuccino.*#smu', $output );
		$this->assertRegExp( '#.*Cafe Noire Expresso.*#smu', $output );
		$this->assertRegExp( '#.*Unittest: Bundle.*#smu', $output );
		$this->assertRegExp( '#.*Unittest: Test priced Selection.*#smu', $output );
	}


	public function testGetBodySearchText()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f_search' => 'Kaffee' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos catalog-list">', $output );
	}


	public function testGetBodySearchAttribute()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f_attrid' => array( -1, -2 ) ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos catalog-list">', $output );
	}


	public function testGetSubClient()
	{
		$client = $this->_object->getSubClient( 'items', 'Default' );
		$this->assertInstanceOf( 'Client_HTML_Interface', $client );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( '$$$', '$$$' );
	}


	protected function _getCatalogItem( $code = 'cafe' )
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', $code ) );
		$items = $catalogManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No catalog item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
