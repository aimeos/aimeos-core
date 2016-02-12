<?php

namespace Aimeos\Client\Html\Account\Download;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperHtml::getContext();

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Account\Download\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();
		$this->assertNull( $output );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertEquals( '', $output );
	}


	public function testGetHeaderDownload()
	{
		$fs = $this->context->getFilesystemManager()->get( 'fs-secure' );
		$fs->write( 'download/test.txt', 'test' );

		$item = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product/attribute' )->createItem();
		$item->setValue( 'download/test.txt' );
		$item->setName( 'test download' );

		$view = \TestHelperHtml::getView();
		$view->downloadFilesystem = $fs;
		$view->downloadItem = $item;

		$this->object->setView( $view );

		$output = $this->object->getHeader();
		$this->assertEquals( 'test', $output );
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


	public function testProcessNoItem()
	{
		$this->context->setUserId( '123' );
		$this->object->process();
	}


	public function testProcessOK()
	{
		$object = $this->getMockBuilder( '\Aimeos\Client\Html\Account\Download\Standard' )
			->setConstructorArgs( array( $this->context, \TestHelperHtml::getHtmlTemplatePaths() ) )
			->setMethods( array( 'checkAccess', 'checkDownload' ) )
			->getMock();
		$object->setView( \TestHelperHtml::getView() );

		$object->expects( $this->once() )->method( 'checkAccess' )->will( $this->returnValue( true ) );
		$object->expects( $this->once() )->method( 'checkDownload' )->will( $this->returnValue( true ) );


		$attrManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Product\\Attribute\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'getItem' ) )
			->getMock();

		$attrManagerStub->expects( $this->once() )->method( 'getItem' )
			->will( $this->returnValue( $attrManagerStub->createItem() ) );

		\Aimeos\MShop\Factory::injectManager( $this->context, 'order/base/product/attribute', $attrManagerStub );


		\Aimeos\MShop\Factory::setCache( true );
		$object->process();
		\Aimeos\MShop\Factory::setCache( false );
	}


	public function testProcessCheckAccess()
	{
		$class = new \ReflectionClass( '\Aimeos\Client\Html\Account\Download\Standard' );
		$method = $class->getMethod( 'checkAccess' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( 123, 321 ) );

		$this->assertFalse( $result );
	}


	public function testProcessCheckDownload()
	{
		$managerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Lists\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();
		$managerStub->expects( $this->once() )->method( 'saveItem' );

		\Aimeos\MShop\Factory::injectManager( $this->context, 'customer/lists', $managerStub );


		$class = new \ReflectionClass( '\Aimeos\Client\Html\Account\Download\Standard' );
		$method = $class->getMethod( 'checkDownload' );
		$method->setAccessible( true );

		\Aimeos\MShop\Factory::setCache( true );
		$result = $method->invokeArgs( $this->object, array( 123, 321 ) );
		\Aimeos\MShop\Factory::setCache( false );

		$this->assertTrue( $result );
	}


	public function testProcessCheckDownloadMaxCount()
	{
		$this->context->getConfig()->set( 'client/html/account/download/maxcount', 0 );

		$class = new \ReflectionClass( '\Aimeos\Client\Html\Account\Download\Standard' );
		$method = $class->getMethod( 'checkDownload' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( 123, 321 ) );

		$this->assertFalse( $result );
	}


	public function testProcessGetListItem()
	{
		$class = new \ReflectionClass( '\Aimeos\Client\Html\Account\Download\Standard' );
		$method = $class->getMethod( 'getListItem' );
		$method->setAccessible( true );

		$result = $method->invokeArgs( $this->object, array( 123, 321 ) );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Lists\Iface', $result );
	}
}