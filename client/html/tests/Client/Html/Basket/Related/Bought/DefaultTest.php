<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Basket_Related_Bought_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Client_Html_Basket_Related_Bought_Default( $this->context, $paths );
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
		$view->relatedBasket = $controller->get();

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->context );

		$basket = $controller->get();
		$basket->addProduct( $this->getOrderProductItem( 'CNC' ) );

		$view = $this->object->getView();
		$view->relatedBasket = $basket;

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="basket-related-bought', $output );
		$this->assertContains( 'Cafe Noire Expresso', $output );
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
	 * @return MShop_Order_Item_Base_Product_Interface
	 */
	protected function getOrderProductItem( $code )
	{
		$manager = MShop_Factory::createManager( $this->context, 'product' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		$manager = MShop_Factory::createManager( $this->context, 'order/base/product' );
		$orderItem = $manager->createItem();
		$orderItem->copyFrom( $item );

		return $orderItem;
	}
}
