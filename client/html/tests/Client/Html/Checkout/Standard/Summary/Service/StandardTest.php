<?php

namespace Aimeos\Client\Html\Checkout\Standard\Summary\Service;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Summary\Service\Standard( $this->context, $paths );
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
		\Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->clear();
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$view = \TestHelper::getView();
		$view->standardBasket = $this->getBasket();
		$this->object->setView( $view );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = \TestHelper::getView();
		$view->standardBasket = $this->getBasket();
		$this->object->setView( $view );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="common-summary-service container">', $output );
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


	protected function getBasket()
	{
		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $this->context );
		$search = $customerManager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new \Exception( 'Customer item not found' );
		}

		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$controller->setAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, $customer->getPaymentAddress() );
		$controller->setAddress( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY, $customer->getPaymentAddress() );

		return $controller->get();
	}
}
