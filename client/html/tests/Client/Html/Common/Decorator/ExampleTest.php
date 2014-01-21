<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Client_Html_Common_Decorator_Example.
 */
class Client_Html_Common_Decorator_ExampleTest extends MW_Unittest_Testcase
{
	private $_client;
	private $_object;
	private $_view;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$methods = array( 'getHeader', 'getBody' );

		$this->_client = $this->getMock( 'Client_Html_Catalog_Filter_Default', $methods, array( $context, array() ) );
		$this->_object = new Client_Html_Common_Decorator_Example( $context, $this->_client );
		$this->_object->setView( new MW_View_Default() );
	}


	public function testCall()
	{
		$this->assertInternalType( 'boolean', $this->_object->additionalMethod() );
	}


	public function testGetSubClient()
	{
		$this->assertInstanceOf( 'Client_Html_Interface', $this->_object->getSubClient( 'tree' ) );
	}


	public function testGetHeader()
	{
		$this->_client->expects( $this->once() )->method( 'getHeader' )->will( $this->returnValue( 'header' ) );
		$this->assertEquals( 'header', $this->_object->getHeader() );
	}


	public function testGetBody()
	{
		$this->_client->expects( $this->once() )->method( 'getBody' )->will( $this->returnValue( 'body' ) );
		$this->assertEquals( 'body', $this->_object->getBody() );
	}


	public function testGetView()
	{
		$this->assertInstanceOf( 'MW_View_Interface', $this->_object->getView() );
	}


	public function testSetView()
	{
		$view = new MW_View_Default();
		$this->_object->setView( $view );

		$this->assertSame( $view, $this->_object->getView() );
	}


	public function testIsCachable()
	{
		$this->assertInternalType( 'boolean', $this->_object->isCachable( Client_Html_Abstract::CACHE_BODY ) );
	}


	public function testProcess()
	{
		$this->_object->process();
	}

}