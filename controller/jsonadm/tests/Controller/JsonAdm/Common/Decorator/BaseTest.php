<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\JsonAdm\Common\Decorator;


class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $stub;
	private $object;


	protected function setUp()
	{
		$context = \TestHelperJadm::getContext();
		$view = $context->getView();

		$this->stub = $this->getMockBuilder( '\\Aimeos\\Controller\\JsonAdm\\Standard' )
			->setConstructorArgs( array( $context, $view, array(), 'attribute' ) )
			->getMock();

		$this->object = new TestBase( $this->stub, $context, $view, array(), 'attribute' );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->stub );
	}


	public function testDelete()
	{
		$status = 0;
		$header = array();

		$this->stub->expects( $this->once() )
			->method( 'delete' )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->delete( '', $header, $status ) );
	}


	public function testGet()
	{
		$status = 0;
		$header = array();

		$this->stub->expects( $this->once() )
			->method( 'get' )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->get( '', $header, $status ) );
	}


	public function testPatch()
	{
		$status = 0;
		$header = array();

		$this->stub->expects( $this->once() )
			->method( 'patch' )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->patch( '', $header, $status ) );
	}


	public function testPost()
	{
		$status = 0;
		$header = array();

		$this->stub->expects( $this->once() )
			->method( 'post' )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->post( '', $header, $status ) );
	}


	public function testPut()
	{
		$status = 0;
		$header = array();

		$this->stub->expects( $this->once() )
			->method( 'put' )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->put( '', $header, $status ) );
	}


	public function testOptions()
	{
		$status = 0;
		$header = array();

		$this->stub->expects( $this->once() )
			->method( 'options' )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->options( '', $header, $status ) );
	}


	public function testGetContext()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Context\\Item\\Iface', $this->object->getContextPublic() );
	}


	public function testGetTemplatePaths()
	{
		$this->assertEquals( array(), $this->object->getTemplatePathsPublic() );
	}


	public function testGetPath()
	{
		$this->assertEquals( 'attribute', $this->object->getPathPublic() );
	}


	public function testCall()
	{
		$this->markTestIncomplete( 'PHP warning is triggered instead of exception' );
	}

}


class TestBase
	extends \Aimeos\Controller\JsonAdm\Common\Decorator\Base
{
	public function getContextPublic()
	{
		return $this->getContext();
	}

	public function getTemplatePathsPublic()
	{
		return $this->getTemplatePaths();
	}

	public function getPathPublic()
	{
		return $this->getPath();
	}
}
