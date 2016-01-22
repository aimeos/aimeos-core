<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\Admin\JQAdm\Common\Decorator;


class IndexTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $mock;


	protected function setUp()
	{
		$this->context = \TestHelperJqadm::getContext();
		$templatePaths = \TestHelperJqadm::getTemplatePaths();

		$this->mock = $this->getMockBuilder( 'Aimeos\Admin\JQAdm\Product\Standard' )
			->setMethods( array( 'save' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->object = new \Aimeos\Admin\JQAdm\Common\Decorator\Index( $this->mock, $this->context, $templatePaths );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->mock, $this->context );
	}


	public function testSave()
	{
		$view = \TestHelperJqadm::getView();
		$view->item = \Aimeos\MShop\Factory::createManager( $this->context, 'product' )->findItem( 'CNC' );

		$this->mock->expects( $this->once() )->method( 'save' )->will( $this->returnValue( 'test' ) );
		$this->object->setView( $view );

		$result = $this->object->save();

		$this->assertEquals( 'test', $result );
	}
}
