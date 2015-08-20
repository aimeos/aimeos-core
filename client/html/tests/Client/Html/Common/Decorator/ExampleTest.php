<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for Client_Html_Common_Decorator_Example.
 */
class Client_Html_Common_Decorator_ExampleTest extends PHPUnit_Framework_TestCase
{
	private $_client;
	private $_object;


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
		$this->_object = new Client_Html_Common_Decorator_Example( $context, array(), $this->_client );
		$this->_object->setView( TestHelper::getView());
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


	public function testModifyBody()
	{
		$this->assertEquals( 'test', $this->_object->modifyBody( 'test', 1 ) );
	}


	public function testModifyHeader()
	{
		$this->assertEquals( 'test', $this->_object->modifyHeader( 'test', 1 ) );
	}


	public function testProcess()
	{
		$this->_object->process();
	}

}