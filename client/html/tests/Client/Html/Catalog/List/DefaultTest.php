<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Catalog_List_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Catalog_List_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


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
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-catalog-id' => $this->_getCatalogItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getHeader( 1, $tags, $expire );

		$this->assertStringStartsWith( '<title>Kaffee</title>', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 6, count( $tags ) );
	}


	public function testGetHeaderSearch()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-search-text' => '<b>Search result</b>' ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getHeader( 1, $tags, $expire );

		$this->assertRegexp( '#<title>[^>]*Search result[^<]*</title>#', $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 0, count( $tags ) );
	}


	public function testGetBody()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-catalog-id' => $this->_getCatalogItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<section class="arcavias catalog-list home categories coffee">', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 6, count( $tags ) );
	}


	public function testGetBodyNoDefaultCat()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array() );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="arcavias catalog-list">', $output );
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
		$this->assertStringStartsWith( '<section class="arcavias catalog-list home categories coffee">', $output );
	}


	public function testGetBodySearchText()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-search-text' => 'Kaffee' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="arcavias catalog-list">', $output );
	}


	public function testGetBodySearchAttributeList()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-attr-id' => array( -1, -2 ) ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="arcavias catalog-list">', $output );
	}


	public function testGetBodySearchAttributeString()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f-attr-id' => '-1 -2' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="arcavias catalog-list">', $output );
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


	protected function _getCatalogItem()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$items = $catalogManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No catalog item with code "cafe" found' );
		}

		return $item;
	}
}
