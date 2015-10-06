<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Catalog_List_Quote_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Client_Html_Catalog_List_Quote_Default( $context, $paths );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$catItems = $catalogManager->searchItems( $search, array( 'text' ) );

		if( ( $catItem = reset( $catItems ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$view = TestHelper::getView();

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
		$quoteItems = $this->object->getView()->get( 'quoteItems', array() );

		if( ( $quoteItem = reset( $quoteItems ) ) === false ) {
			throw new Exception( 'No quote item found' );
		}

		$this->assertEquals( 'Kaffee Bewertungen', $quoteItem->getContent() );
		$this->assertStringStartsWith( '<div class="catalog-list-quote">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcess()
	{
		$this->object->process();
	}
}
