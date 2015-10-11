<?php

namespace Aimeos\Client\Html\Checkout\Standard\Summary\Option\Terms;


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
		$this->object = new \Aimeos\Client\Html\Checkout\Standard\Summary\Option\Terms\Standard( $this->context, $paths );
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
		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="checkout-standard-summary-option-terms">', $output );
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
		$this->assertEquals( null, $this->object->getView()->get( 'standardStepActive' ) );
	}


	public function testProcessOK()
	{
		$view = $this->object->getView();

		$param = array(
			'cs_option_terms' => '1',
			'cs_option_terms_value' => '1',
		);

		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$this->assertEquals( null, $view->get( 'standardStepActive' ) );
	}


	public function testProcessInvalid()
	{
		$view = $this->object->getView();

		$param = array(
			'cs_option_terms' => '1',
		);

		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();
		$this->assertEquals( 'summary', $view->get( 'standardStepActive' ) );
		$this->assertEquals( true, $view->get( 'termsError' ) );
	}
}
