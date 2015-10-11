<?php

namespace Aimeos\Client\Html\Checkout\Confirm;


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
		$this->context->setEditor( 'UTC001' );

		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Checkout\Confirm\Standard( $this->context, $paths );
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
		$this->context->getSession()->set( 'aimeos/orderid', $this->getOrder( '2011-09-17 16:14:32' )->getId() );

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$this->context->getSession()->set( 'aimeos/orderid', $this->getOrder( '2011-09-17 16:14:32' )->getId() );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos checkout-confirm">', $output );
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
		$this->context->getSession()->set( 'aimeos/orderid', $this->getOrder( '2011-09-17 16:14:32' )->getId() );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, array( 'code' => 'paypalexpress' ) );
		$view->addHelper( 'param', $helper );

		$this->object->process();
	}


	public function testProcessNoCode()
	{
		$this->object->process();
	}


	/**
	 * @param string $date
	 */
	protected function getOrder( $date )
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );

		$search = $orderManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $date ) );

		$result = $orderManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Exception( 'No order found' );
		}

		return $item;
	}
}
