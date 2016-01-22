<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\Admin\JQAdm\Product\Category\Decorator;


class CacheTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $mock;
	private $cache;


	protected function setUp()
	{
		$this->cache = $this->getMockBuilder( 'Aimeos\MW\Cache\None' )
			->setMethods( array( 'deleteByTags' ) )
			->disableOriginalConstructor()
			->getMock();

		$this->mock = $this->getMockBuilder( 'Aimeos\Admin\JQAdm\Product\Category\Standard' )
			->setMethods( array( 'save' ) )
			->disableOriginalConstructor()
			->getMock();

		$templatePaths = \TestHelperJqadm::getTemplatePaths();
		$this->context = \TestHelperJqadm::getContext();
		$this->context->setCache( $this->cache );

		$this->object = new \Aimeos\Admin\JQAdm\Product\Category\Decorator\Cache( $this->mock, $this->context, $templatePaths );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->mock, $this->context, $this->cache );
	}


	public function testSave()
	{
		$view = \TestHelperJqadm::getView();
		$tags = array( 'catalog', 'catalog-1', 'catalog-2' );

		$param = array( 'category' => array( 'catalog.id' => array( '1', '2' ) ) );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->cache->expects( $this->once() )->method( 'deleteByTags' )->with( $this->equalTo( $tags ) );
		$this->mock->expects( $this->once() )->method( 'save' )->will( $this->returnValue( 'test' ) );

		$this->object->setView( $view );
		$result = $this->object->save();

		$this->assertEquals( 'test', $result );
	}
}
