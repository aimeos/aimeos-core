<?php

namespace Aimeos\Client\Html\Checkout\Standard\Delivery;


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
		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Delivery\Standard( $this->context, $paths );
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
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = \TestHelper::getView();
		$this->object->setView( $view );
		$view->standardStepActive = 'delivery';
		$view->standardSteps = array( 'before', 'delivery', 'after' );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="checkout-standard-delivery">', $output );

		$this->assertGreaterThan( 0, count( $view->deliveryServices ) );
		$this->assertGreaterThanOrEqual( 0, count( $view->deliveryServiceAttributes ) );
	}


	public function testGetBodyOtherStep()
	{
		$view = \TestHelper::getView();
		$this->object->setView( $view );

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


	public function testProcessExistingId()
	{
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $service = reset( $result ) ) === false ) {
			throw new \Exception( 'Service item not found' );
		}

		$view = \TestHelper::getView();

		$param = array(
			'c_deliveryoption' => $service->getId(),
		);
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->object->process();

		$basket = \Aimeos\Controller\Frontend\Basket\Factory::createController( $this->context )->get();
		$this->assertEquals( 'unitcode', $basket->getService( 'delivery' )->getCode() );
	}


	public function testProcessInvalidId()
	{
		$view = \TestHelper::getView();

		$param = array( 'c_deliveryoption' => -1 );
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Service\\Exception' );
		$this->object->process();
	}


	public function testProcessNotExistingAttributes()
	{
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context );
		$search = $serviceManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.code', 'unitcode' ) );
		$result = $serviceManager->searchItems( $search );

		if( ( $service = reset( $result ) ) === false ) {
			throw new \Exception( 'Service item not found' );
		}

		$view = \TestHelper::getView();

		$param = array(
			'c_deliveryoption' => $service->getId(),
			'c_delivery' => array(
				$service->getId() => array(
					'notexisting' => 'invalid value',
				),
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $view );

		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Basket\\Exception' );
		$this->object->process();
	}
}
