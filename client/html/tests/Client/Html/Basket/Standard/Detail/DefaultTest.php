<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Basket_Standard_Detail_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Client_Html_Basket_Standard_Detail_Default( $this->context, $paths );
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
		$view->standardBasket = $controller->get();

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$controller = Controller_Frontend_Basket_Factory::createController( $this->context );

		$view = $this->object->getView();
		$view->standardBasket = $controller->get();

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="common-summary-detail container">', $output );
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
}
