<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Checkout_Standard_Summary_Detail_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Client_Html_Checkout_Standard_Summary_Detail_Default( $this->_context, $paths );
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
		Controller_Frontend_Basket_Factory::createController( $this->_context )->clear();
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );

		$view = TestHelper::getView();
		$view->standardBasket = $controller->get();
		$this->_object->setView( $view );

		$this->_object->getHeader();
	}


	public function testGetBody()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$controller->addProduct( $this->_getProductItem( 'CNE' )->getId(), 1, array(), array(), array(), array(), 'unit_warehouse1' );

		$view = TestHelper::getView();
		$view->standardBasket = $controller->get();
		$this->_object->setView( $view );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="common-summary-detail container">', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="tax">.*<td class="price">3.03 .+</td>.*.*</tfoot>#smU', $output );
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


	/**
	 * @param string $code
	 */
	protected function _getProductItem( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
