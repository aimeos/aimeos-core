<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Catalog_Lists_Items_StandardTest extends PHPUnit_Framework_TestCase
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
		$context = TestHelper::getContext();
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Catalog_Lists_Items_Standard( $context, $paths );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$catItems = $catalogManager->searchItems( $search );

		if( ( $catItem = reset( $catItems ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );
		$total = 0;

		$view = TestHelper::getView();

		$view->listProductItems = $productManager->searchItems( $search, array( 'media', 'price', 'text' ), $total );
		$view->listProductTotal = $total;
		$view->listPageSize = 100;
		$view->listPageCurr = 1;
		$view->listParams = array();
		$view->listCatPath = array( $catalogManager->createItem(), $catItem );

		$this->object->setView( $view );
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
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="catalog-list-items">', $output );

		$this->assertContains( '<div class="price-item', $output );
		$this->assertContains( '<span class="quantity">', $output );
		$this->assertContains( '<span class="value">', $output );
		$this->assertContains( '<span class="costs">', $output );
		$this->assertContains( '<span class="taxrate">', $output );
	}

	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
