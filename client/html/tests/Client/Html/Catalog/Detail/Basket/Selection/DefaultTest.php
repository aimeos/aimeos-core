<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Catalog_Detail_Basket_Selection_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Client_Html_Catalog_Detail_Basket_Selection_Default( TestHelper::getContext(), $paths );
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
		$view->detailProductItem = $this->_getProductItem( 'U:TESTP' );

		$tags = array();
		$expire = null;
		$output = $this->_object->getHeader( 1, $tags, $expire );

		$this->assertEquals( '', $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testGetBody()
	{
		$view = $this->_object->getView();
		$view->detailProductItem = $this->_getProductItem( 'U:TEST' );

		$variantAttr1 = $this->_getProductItem( 'U:TESTSUB02' )->getRefItems( 'attribute', null, 'variant' );
		$variantAttr2 = $this->_getProductItem( 'U:TESTSUB04' )->getRefItems( 'attribute', null, 'variant' );

		$this->assertGreaterThan( 0, count( $variantAttr1 ) );
		$this->assertGreaterThan( 0, count( $variantAttr2 ) );

		$tags = array();
		$expire = null;
		$output = $this->_object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<div class="catalog-detail-basket-selection', $output );

		foreach( $variantAttr1 as $id => $item ) {
			$this->assertRegexp( '#<option class="select-option" value="' . $id . '">#', $output );
		}

		foreach( $variantAttr2 as $id => $item ) {
			$this->assertRegexp( '#<option class="select-option" value="' . $id . '">#', $output );
		}

		$this->assertEquals( null, $expire );
		$this->assertEquals( 2, count( $tags ) );
	}

	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	/**
	 * @param string $code
	 */
	protected function _getProductItem( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search, array( 'attribute', 'price', 'product' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
