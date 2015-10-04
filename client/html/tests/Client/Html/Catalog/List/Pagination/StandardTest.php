<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Catalog_List_Pagination_StandardTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Client_Html_Catalog_List_Pagination_Standard( $context, $paths );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$catItems = $catalogManager->searchItems( $search );

		if( ( $catItem = reset( $catItems ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$view = TestHelper::getView();

		$view->listProductItems = array();
		$view->listProductTotal = 100;
		$view->listPageSize = 10;
		$view->listPageCurr = 2;
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

		$this->assertContains( '<link rel="prev"', $output );
		$this->assertContains( '<link rel="next prefetch"', $output );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="catalog-list-pagination', $output );
	}

	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
