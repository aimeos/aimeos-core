<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Catalog_Detail_DefaultTest extends MW_Unittest_Testcase
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

		$this->_object = new Client_Html_Catalog_Detail_Default( $this->_context, $paths );
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
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'd_prodid' => $this->_getProductItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getHeader( 1, $tags, $expire );

		$this->assertStringStartsWith( '	<title>Cafe Noire Cappuccino</title>', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 5, count( $tags ) );
	}


	public function testGetBody()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'd_prodid' => $this->_getProductItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->_object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<section class="aimeos catalog-detail">', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 5, count( $tags ) );
	}


	public function testGetSubClient()
	{
		$client = $this->_object->getSubClient( 'basic', 'Default' );
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


	public function testProcess()
	{
		$this->_object->process();
	}


	protected function _getProductItem()
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No product item with code "CNC" found' );
		}

		return $item;
	}
}
