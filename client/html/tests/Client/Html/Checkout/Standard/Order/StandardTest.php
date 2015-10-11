<?php

namespace Aimeos\Client\Html\Checkout\Standard\Order;


/**
 * @copyright Metaways Infosystems GmbH, 2013
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
		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Order\Standard( $this->context, $paths );
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
		$view->standardStepActive = 'order';
		$this->object->setView( $view );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetHeaderOtherStep()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = \TestHelper::getView();
		$view->standardStepActive = 'order';
		$view->paymentForm = new \Aimeos\MShop\Common\Item\Helper\Form\Standard( '', 'POST', array() );
		$this->object->setView( $view );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-order">', $output );
	}


	public function testGetBodyOtherStep()
	{
		$output = $this->object->getBody();
		$this->assertEquals( '', $output );
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


	public function testProcess()
	{
		$this->object->process();
	}


	public function testProcessOK()
	{
		$controller = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context );
		$baseManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->getSubManager( 'base' );
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context );


		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitpaymentcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $serviceItem = reset( $result ) ) === false ) {
			throw new \Exception( 'No service item found' );
		}

		$controller->setService( 'payment', $serviceItem->getId() );
		$controller->setAddress( 'payment', array( 'order.base.address.languageid' => 'en' ) );
		$this->context->setUserId( '-1' );


		$view = \TestHelper::getView();

		$param = array( 'cs_order' => 1 );
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );
		$this->object->process();


		$search = $baseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.customerid', '-1' ) );
		$result = $baseManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Exception( 'No order placed' );
		}

		$baseManager->deleteItem( $item->getId() );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Iface', $view->orderItem );
		$this->assertEquals( $item->getId(), $view->orderItem->getBaseId() );
	}
}
