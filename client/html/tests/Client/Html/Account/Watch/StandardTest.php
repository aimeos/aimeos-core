<?php

namespace Aimeos\Client\Html\Account\Watch;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
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
		$this->object = new \Aimeos\Client\Html\Account\Watch\Standard( $this->context, $paths );
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
		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos account-watch">', $output );
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


	public function testProcessAddItem()
	{
		$this->context->setUserId( '123' );

		$view = $this->object->getView();
		$param = array(
			'wat_action' => 'add',
			'wat_id' => 321,
		);

		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );



		$listManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Lists\\Standard' )
			->setMethods( array( 'saveItem', 'moveItem' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$name = 'ClientHtmlAccountWatchDefaultProcess';
		$this->context->getConfig()->set( 'classes/customer/manager/name', $name );

		\Aimeos\MShop\Customer\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Customer\\Manager\\' . $name, $managerStub );


		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'saveItem' );
		$listManagerStub->expects( $this->once() )->method( 'moveItem' );


		$this->object->process();
	}


	public function testProcessEditItem()
	{
		$this->context->setUserId( '123' );

		$view = $this->object->getView();
		$param = array(
			'wat_action' => 'edit',
			'wat_id' => 321,
		);

		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );


		$listManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Lists\\Standard' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$name = 'ClientHtmlAccountWatchDefaultProcess';
		$this->context->getConfig()->set( 'classes/customer/manager/name', $name );

		\Aimeos\MShop\Customer\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Customer\\Manager\\' . $name, $managerStub );


		$item = $listManagerStub->createItem();
		$item->setRefId( 321 );

		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $item ) ) );

		$listManagerStub->expects( $this->once() )->method( 'saveItem' );


		$this->object->process();
	}


	public function testProcessDeleteItem()
	{
		$this->context->setUserId( '123' );

		$view = $this->object->getView();
		$param = array(
			'wat_action' => 'delete',
			'wat_id' => 321,
		);

		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, $param );
		$view->addHelper( 'param', $helper );



		$listManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Lists\\Standard' )
			->setMethods( array( 'deleteItems' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$managerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$name = 'ClientHtmlAccountWatchDefaultProcess';
		$this->context->getConfig()->set( 'classes/customer/manager/name', $name );

		\Aimeos\MShop\Customer\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Customer\\Manager\\' . $name, $managerStub );


		$managerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $listManagerStub ) );

		$listManagerStub->expects( $this->once() )->method( 'deleteItems' );


		$this->object->process();
	}
}