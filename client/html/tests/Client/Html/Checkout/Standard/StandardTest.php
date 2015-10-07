<?php

namespace Aimeos\Client\Html\Checkout\Standard;


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
		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Standard( $this->context, $paths );
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
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'address';

		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, array( 'c_step' => 'payment' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="aimeos checkout-standard">', $output );
		$this->assertRegExp( '#<ol class="steps">.*<li class="step.*>.*</li>.*</ol>#smU', $output );
		$this->assertContains( '<section class="checkout-standard-address', $output );
		$this->assertNotContains( '<section class="checkout-standard-delivery', $output );
		$this->assertNotContains( '<section class="checkout-standard-payment', $output );
		$this->assertNotContains( '<section class="checkout-standard-summary', $output );
		$this->assertNotContains( '<section class="checkout-standard-order', $output );
	}


	public function testGetBodyOnepage()
	{
		$view = $this->object->getView();

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/onepage', array( 'address', 'delivery', 'payment', 'summary' ) );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$output = $this->object->getBody();

		$this->assertContains( '<section class="checkout-standard-address', $output );
		$this->assertContains( '<section class="checkout-standard-delivery', $output );
		$this->assertContains( '<section class="checkout-standard-payment', $output );
		$this->assertContains( '<section class="checkout-standard-summary', $output );
		$this->assertNotContains( '<section class="checkout-standard-order', $output );
	}


	public function testGetBodyOnepagePartitial()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'delivery';

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/onepage', array( 'delivery', 'payment' ) );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$output = $this->object->getBody();

		$this->assertContains( '<section class="checkout-standard-delivery', $output );
		$this->assertContains( '<section class="checkout-standard-payment', $output );
		$this->assertNotContains( '<section class="checkout-standard-address', $output );
		$this->assertNotContains( '<section class="checkout-standard-summary', $output );
		$this->assertNotContains( '<section class="checkout-standard-order', $output );
	}


	public function testGetBodyOnepageDifferentStep()
	{
		$view = $this->object->getView();
		$view->standardStepActive = 'address';

		$config = $this->context->getConfig();
		$config->set( 'client/html/checkout/standard/onepage', array( 'delivery', 'payment' ) );

		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $config );
		$view->addHelper( 'config', $helper );

		$output = $this->object->getBody();

		$this->assertContains( '<section class="checkout-standard-address', $output );
		$this->assertNotContains( '<section class="checkout-standard-delivery', $output );
		$this->assertNotContains( '<section class="checkout-standard-payment', $output );
		$this->assertNotContains( '<section class="checkout-standard-summary', $output );
		$this->assertNotContains( '<section class="checkout-standard-order', $output );
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'address', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\HTML\\Iface', $client );
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
}
