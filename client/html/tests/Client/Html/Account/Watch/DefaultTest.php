<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Account_Watch_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_standard;
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Account_Watch_Default( $this->_context, $paths );
		$this->_object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos account-watch">', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( '$$$', '$$$' );
	}


	public function testProcess()
	{
		$this->_object->process();
	}


	public function testProcessAddItem()
	{
		$this->_context->setUserId( '123' );

		$view = $this->_object->getView();
		$param = array(
			'wat_action' => 'add',
			'wat_id' => 321,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );



		$listManagerStub = $this->getMockBuilder( 'MShop_Customer_Manager_List_Default' )
			->setMethods( array( 'saveItem', 'moveItem' ) )
			->setConstructorArgs( array( $this->_context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( 'MShop_Customer_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->_context ) )
			->getMock();

		$name = 'ClientHtmlAccountWatchDefaultProcess';
		$this->_context->getConfig()->set( 'classes/customer/manager/name', $name );

		MShop_Customer_Manager_Factory::injectManager( 'MShop_Customer_Manager_' . $name, $managerStub );


		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'saveItem' );
		$listManagerStub->expects( $this->once() )->method( 'moveItem' );


		$this->_object->process();
	}


	public function testProcessEditItem()
	{
		$this->_context->setUserId( '123' );

		$view = $this->_object->getView();
		$param = array(
			'wat_action' => 'edit',
			'wat_id' => 321,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );


		$listManagerStub = $this->getMockBuilder( 'MShop_Customer_Manager_List_Default' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $this->_context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( 'MShop_Customer_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->_context ) )
			->getMock();

		$name = 'ClientHtmlAccountWatchDefaultProcess';
		$this->_context->getConfig()->set( 'classes/customer/manager/name', $name );

		MShop_Customer_Manager_Factory::injectManager( 'MShop_Customer_Manager_' . $name, $managerStub );


		$item = $listManagerStub->createItem();
		$item->setRefId( 321 );

		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $item ) ) );

		$listManagerStub->expects( $this->once() )->method( 'saveItem' );


		$this->_object->process();
	}


	public function testProcessDeleteItem()
	{
		$this->_context->setUserId( '123' );

		$view = $this->_object->getView();
		$param = array(
			'wat_action' => 'delete',
			'wat_id' => 321,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );



		$listManagerStub = $this->getMockBuilder( 'MShop_Customer_Manager_List_Default' )
			->setMethods( array( 'deleteItems' ) )
			->setConstructorArgs( array( $this->_context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( 'MShop_Customer_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->_context ) )
			->getMock();

		$name = 'ClientHtmlAccountWatchDefaultProcess';
		$this->_context->getConfig()->set( 'classes/customer/manager/name', $name );

		MShop_Customer_Manager_Factory::injectManager( 'MShop_Customer_Manager_' . $name, $managerStub );


		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'deleteItems' );


		$this->_object->process();
	}
}