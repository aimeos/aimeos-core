<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Basket_Standard_Coupon_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Client_Html_Basket_Standard_Coupon_Default( $this->_context, $paths );
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
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );

		$view = $this->_object->getView();
		$view->standardBasket = $controller->get();

		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );

		$view = $this->_object->getView();
		$view->standardBasket = $controller->get();

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="basket-standard-coupon', $output );
	}


	public function testGetBodyAddCoupon()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$controller->addProduct( $this->_getProductItem( 'CNC' )->getId(), 1, array(), array(), array(), array(), 'default' );

		$view = $this->_object->getView();

		$param = array( 'b_coupon' => '90AB' );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();

		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$view->standardBasket = $controller->get();
		$output = $this->_object->getBody();

		$this->assertRegExp( '#<li class="attr-item">.*90AB.*</li>#smU', $output );
	}


	public function testGetBodyDeleteCoupon()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$controller->addProduct( $this->_getProductItem( 'CNC' )->getId(), 1, array(), array(), array(), array(), 'default' );

		$view = $this->_object->getView();

		$param = array( 'b_coupon' => '90AB' );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();


		$param = array( 'b_action' => 'coupon-delete', 'b_coupon' => '90AB' );

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();

		$controller = Controller_Frontend_Basket_Factory::createController( $this->_context );
		$view->standardBasket = $controller->get();
		$output = $this->_object->getBody();

		$this->assertNotRegExp( '#<ul class="attr-list">#smU', $output );
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
