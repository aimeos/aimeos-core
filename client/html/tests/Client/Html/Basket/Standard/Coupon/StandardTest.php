<?php

namespace Aimeos\Client\Html\Basket\Standard\Coupon;


/**
 * @copyright Metaways Infosystems GmbH, 2014
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
		$this->object = new \Aimeos\Client\Html\Basket\Standard\Coupon\Standard( $this->context, $paths );
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
		$view->standardBasket = $controller->get();

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );

		$view = $this->object->getView();
		$view->standardBasket = $controller->get();

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="basket-standard-coupon', $output );
	}


	public function testGetBodyAddCoupon()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$controller->addProduct( $this->getProductItem( 'CNC' )->getId(), 1, array(), array(), array(), array(), array(), 'default' );

		$view = $this->object->getView();

		$param = array( 'b_coupon' => '90AB' );
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$view->standardBasket = $controller->get();
		$output = $this->object->getBody();

		$this->assertRegExp( '#<li class="attr-item">.*90AB.*</li>#smU', $output );
	}


	public function testGetBodyDeleteCoupon()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$controller->addProduct( $this->getProductItem( 'CNC' )->getId(), 1, array(), array(), array(), array(), array(), 'default' );

		$view = $this->object->getView();

		$param = array( 'b_coupon' => '90AB' );
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();


		$param = array( 'b_action' => 'coupon-delete', 'b_coupon' => '90AB' );

		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$view->standardBasket = $controller->get();
		$output = $this->object->getBody();

		$this->assertNotRegExp( '#<ul class="attr-list">#smU', $output );
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
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
