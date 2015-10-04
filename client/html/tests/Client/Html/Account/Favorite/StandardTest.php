<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Account_Favorite_StandardTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Client_Html_Account_Favorite_Standard( $this->context, $paths );
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
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos account-favorite">', $output );
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


	public function testProcess()
	{
		$this->object->process();
	}


	public function testProcessAddItem()
	{
		$this->context->setUserId( '123' );

		$view = $this->object->getView();
		$param = array(
			'fav_action' => 'add',
			'fav_id' => 321,
		);

		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );



		$listManagerStub = $this->getMockBuilder( 'MShop_Customer_Manager_List_Standard' )
			->setMethods( array( 'saveItem', 'moveItem' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( 'MShop_Customer_Manager_Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$name = 'ClientHtmlAccountFavoriteDefaultProcess';
		$this->context->getConfig()->set( 'classes/customer/manager/name', $name );

		MShop_Customer_Manager_Factory::injectManager( 'MShop_Customer_Manager_' . $name, $managerStub );


		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'saveItem' );
		$listManagerStub->expects( $this->once() )->method( 'moveItem' );


		$this->object->process();
	}


	public function testProcessDeleteItem()
	{
		$this->context->setUserId( '123' );

		$view = $this->object->getView();
		$param = array(
			'fav_action' => 'delete',
			'fav_id' => 321,
		);

		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );



		$listManagerStub = $this->getMockBuilder( 'MShop_Customer_Manager_List_Standard' )
			->setMethods( array( 'deleteItems' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( 'MShop_Customer_Manager_Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$name = 'ClientHtmlAccountFavoriteDefaultProcess';
		$this->context->getConfig()->set( 'classes/customer/manager/name', $name );

		MShop_Customer_Manager_Factory::injectManager( 'MShop_Customer_Manager_' . $name, $managerStub );


		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'deleteItems' );


		$this->object->process();
	}
}