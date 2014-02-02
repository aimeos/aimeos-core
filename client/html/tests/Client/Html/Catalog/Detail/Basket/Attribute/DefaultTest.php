<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Catalog_Detail_Basket_Attribute_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Catalog_Detail_Basket_Attribute_DefaultTest');
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
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_Detail_Basket_Attribute_Default( TestHelper::getContext(), $paths );
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
		Controller_Frontend_Factory::clear();
		MShop_Factory::clear();
	}


	public function testGetHeader()
	{
		$view = $this->_object->getView();
		$view->detailProductItem = $this->_getProductItem();

		$output = $this->_object->getHeader();
		$this->assertEquals( '', $output );
	}


	public function testGetBody()
	{
		$view = $this->_object->getView();
		$view->detailProductItem = $this->_getProductItem();

		$configAttr = $view->detailProductItem->getRefItems( 'attribute', null, 'config' );
		$hiddenAttr = $view->detailProductItem->getRefItems( 'attribute', null, 'hidden' );

		$this->assertGreaterThan( 0, count( $configAttr ) );
		$this->assertGreaterThan( 0, count( $hiddenAttr ) );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="catalog-detail-basket-attribute', $output );

		foreach( $configAttr as $id => $item ) {
			$this->assertRegexp( '#<option value="' . $id . '">#', $output );
		}

		foreach( $hiddenAttr as $id => $item ) {
			$this->assertRegexp( '#<input type="hidden" .* value="' . $id . '" />#', $output );
		}
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
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTP' ) );
		$items = $manager->searchItems( $search, array( 'attribute' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No product item with code "U:TESTP" found' );
		}

		return $item;
	}
}
