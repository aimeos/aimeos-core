<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Basket_Mini_Main_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->_object = new Client_Html_Basket_Mini_Main_Default( $this->_context, $paths );
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
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );

		$view = $this->_object->getView();
		$view->miniBasket = $controller->get();

		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );

		$view = $this->_object->getView();
		$view->miniBasket = $controller->get();

		$output = $this->_object->getBody();
		$this->assertContains( '<div class="basket-mini-main">', $output );
	}


	public function testGetBodyAddedOneProduct()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );

		$productItem = $this->_getProductItem( 'CNE' );

		$view = $this->_object->getView();

		$controller->addProduct( $productItem->getId(), 9, array(), array(), array(), array(), array(), 'default' );
		$view->miniBasket = $controller->get();

		$output = $this->_object->getBody();

		$controller->clear();

		$this->assertContains( '<div class="basket-mini-main">', $output );
		$this->assertRegExp( '#9#smU', $output );
		$this->assertRegExp( '#171.00#smU', $output );
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
		$items = $manager->searchItems( $search, array( 'price' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
