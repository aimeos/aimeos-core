<?php

namespace Aimeos\Client\Html\Basket\Related\Bought;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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
		$this->context = \TestHelper::getContext();

		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Basket\Related\Bought\Standard( $this->context, $paths );
		$this->object->setView( \TestHelper::getView() );
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
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );

		$view = $this->object->getView();
		$view->relatedBasket = $controller->get();

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );

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
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	/**
	 * @param string $code
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface
	 */
	protected function getOrderProductItem( $code )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product' );
		$orderItem = $manager->createItem();
		$orderItem->copyFrom( $item );

		return $orderItem;
	}
}
