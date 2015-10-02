<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Catalog_Session_FactoryTest extends PHPUnit_Framework_TestCase
{
	private $context;
	private $templatePaths;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = TestHelper::getContext();
		$this->templatePaths = TestHelper::getHtmlTemplatePaths();
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


	public function testCreateClient()
	{
		$client = Client_Html_Catalog_Session_Factory::createClient( $this->context, $this->templatePaths );
		$this->assertInstanceOf( 'Client_Html_Interface', $client );
	}


	public function testCreateClientName()
	{
		$client = Client_Html_Catalog_Session_Factory::createClient( $this->context, $this->templatePaths, 'Default' );
		$this->assertInstanceOf( 'Client_Html_Interface', $client );
	}


	public function testCreateClientNameInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		Client_Html_Catalog_Session_Factory::createClient( $this->context, $this->templatePaths, '$$$' );
	}


	public function testCreateClientNameNotFound()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		Client_Html_Catalog_Session_Factory::createClient( $this->context, $this->templatePaths, 'notfound' );
	}

}
