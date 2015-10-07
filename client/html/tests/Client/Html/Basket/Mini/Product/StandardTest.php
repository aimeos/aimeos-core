<?php

namespace Aimeos\Client\Html\Basket\Mini\Product;


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
		$this->object = new \Aimeos\Client\Html\Basket\Mini\Product\Standard( $this->context, $paths );
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
		\Aimeos\Controller\Frontend\Factory::clear();
		\Aimeos\MShop\Factory::clear();
	}


	public function testGetHeader()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );

		$view = $this->object->getView();
		$view->miniBasket = $controller->get();

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );

		$view = $this->object->getView();
		$view->miniBasket = $controller->get();

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="basket-mini-product">', $output );
	}


	public function testGetBodyAddedOneProduct()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );

		$productItem = $this->getProductItem( 'CNE' );

		$view = $this->object->getView();

		$controller->addProduct( $productItem->getId(), 9, array(), array(), array(), array(), array(), 'default' );
		$view->miniBasket = $controller->get();

		$output = $this->object->getBody();

		$controller->clear();

		$this->assertStringStartsWith( '<div class="basket-mini-product">', $output );
		$this->assertRegExp( '#9#smU', $output );
		$this->assertRegExp( '#171.00#smU', $output );
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
	 */
	protected function getProductItem( $code )
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search, array( 'price' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
