<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\Admin\JQAdm\Common\Decorator;


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

		$this->mock = $this->getMockBuilder( 'Aimeos\Admin\JQAdm\Product\Standard' )
			->setMethods( array( 'save' ) )
			->disableOriginalConstructor()
			->getMock();

		$templatePaths = \TestHelperJqadm::getTemplatePaths();
		$this->context = \TestHelperJqadm::getContext();
		$this->context->setCache( $this->cache );

		$this->object = new \Aimeos\Admin\JQAdm\Common\Decorator\Cache( $this->mock, $this->context, $templatePaths );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->mock, $this->context, $this->cache );
	}


	public function testSave()
	{
		$item = \Aimeos\MShop\Factory::createManager( $this->context, 'product' )->findItem( 'CNC' );

		$tags = array( 'product', 'product-' . $item->getId() );
		$view = \TestHelperJqadm::getView();
		$view->item = $item;

		$this->cache->expects( $this->once() )->method( 'deleteByTags' )->with( $this->equalTo( $tags ) );
		$this->mock->expects( $this->once() )->method( 'save' )->will( $this->returnValue( 'test' ) );

		$this->object->setView( $view );
		$result = $this->object->save();

		$this->assertEquals( 'test', $result );
	}
}
