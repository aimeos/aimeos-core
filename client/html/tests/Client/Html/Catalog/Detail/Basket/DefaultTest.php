<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Catalog_Detail_Basket_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_Detail_Basket_Default( TestHelper::getContext(), $paths );
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
		$view->detailProductItem = $this->_getProductItem();

		$output = $this->_object->getHeader();
		$this->assertStringStartsWith( '<script type="text/javascript" defer="defer" src="http://baseurl/catalog/stock/?s-product-id', $output );
	}


	public function testGetBody()
	{
		$view = $this->_object->getView();
		$view->detailProductItem = $this->_getProductItem();

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="catalog-detail-basket', $output );
	}

	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	protected function _getProductItem()
	{
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No product item with code "CNC" found' );
		}

		return $item;
	}
}
