<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Catalog_List_Promo_DefaultTest extends MW_Unittest_Testcase
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
		$context = TestHelper::getContext();
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_List_Promo_Default( $context, $paths );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$catItems = $catalogManager->searchItems( $search, array( 'product' ) );

		if( ( $catItem = reset( $catItems ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$view = TestHelper::getView();
		$view->listParams = array();
		$view->listCurrentCatItem = $catItem;
		$this->_object->setView( $view );
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
		$tags = array();
		$expire = null;
		$output = $this->_object->getHeader( 1, $tags, $expire );

		$this->assertStringStartsWith( '<script type="text/javascript"', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testGetBody()
	{
		$tags = array();
		$expire = null;
		$output = $this->_object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<section class="catalog-list-promo">', $output );
		$this->assertRegExp( '/.*Expresso.*Cappuccino.*/smu', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 1, count( $tags ) );
	}

	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcess()
	{
		$this->_object->process();
	}
}
