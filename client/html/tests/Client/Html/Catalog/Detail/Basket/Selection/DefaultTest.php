<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Catalog_Detail_Basket_Selection_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Catalog_Detail_Basket_Selection_Default( TestHelper::getContext(), $paths );
		$this->object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$view = $this->object->getView();
		$view->detailProductItem = $this->getProductItem( 'U:TESTP' );

		$tags = array();
		$expire = null;
		$output = $this->object->getHeader( 1, $tags, $expire );

		$this->assertNotNull( $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->detailProductItem = $this->getProductItem( 'U:TEST' );

		$variantAttr1 = $this->getProductItem( 'U:TESTSUB02' )->getRefItems( 'attribute', null, 'variant' );
		$variantAttr2 = $this->getProductItem( 'U:TESTSUB04' )->getRefItems( 'attribute', null, 'variant' );

		$this->assertGreaterThan( 0, count( $variantAttr1 ) );
		$this->assertGreaterThan( 0, count( $variantAttr2 ) );

		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

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
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	/**
	 * @param string $code
	 */
	protected function getProductItem( $code )
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
