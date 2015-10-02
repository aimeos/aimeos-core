<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Basket_Standard_Coupon_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Basket_Standard_Coupon_Default( $this->context, $paths );
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
		$controller = Controller_Frontend_Basket_Factory::createController( $this->context );

		$view = $this->object->getView();
		$view->standardBasket = $controller->get();

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->context );

		$view = $this->object->getView();
		$view->standardBasket = $controller->get();

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="basket-standard-coupon', $output );
	}


	public function testGetBodyAddCoupon()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->context );
		$controller->addProduct( $this->getProductItem( 'CNC' )->getId(), 1, array(), array(), array(), array(), array(), 'default' );

		$view = $this->object->getView();

		$param = array( 'b_coupon' => '90AB' );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$controller = Controller_Frontend_Basket_Factory::createController( $this->context );
		$view->standardBasket = $controller->get();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<li class="attr-item">.*90AB.*</li>#smU', $output );
	}


	public function testGetBodyDeleteCoupon()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->context );
		$controller->addProduct( $this->getProductItem( 'CNC' )->getId(), 1, array(), array(), array(), array(), array(), 'default' );

		$view = $this->object->getView();

		$param = array( 'b_coupon' => '90AB' );
		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();


		$param = array( 'b_action' => 'coupon-delete', 'b_coupon' => '90AB' );

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$controller = Controller_Frontend_Basket_Factory::createController( $this->context );
		$view->standardBasket = $controller->get();
		$output = $this->object->getBody();

		$this->assertNotRegExp( '#<ul class="attr-list">#smU', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	/**
	 * @param string $code
	 */
	protected function getProductItem( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
