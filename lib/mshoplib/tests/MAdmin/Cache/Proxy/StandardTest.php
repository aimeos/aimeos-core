<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MAdmin\Cache\Proxy;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $mock;
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();

		$this->mock = $this->getMockBuilder( '\\Aimeos\\MW\\Cache\\DB' )
			->disableOriginalConstructor()->getMock();

		$manager = $this->getMockBuilder( '\\Aimeos\\MAdmin\\Cache\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )->getMock();

		$manager->expects( $this->once() )->method( 'getCache' )
			->will( $this->returnValue( $this->mock ) );

		$name = 'MAdminCacheProxyDefaultTest';
		$this->context->getConfig()->set( 'madmin/cache/manager/name', $name );

		\Aimeos\MAdmin\Cache\Manager\Factory::injectManager( '\\Aimeos\\MAdmin\\Cache\\Manager\\' . $name, $manager );

		$this->object = new \Aimeos\MAdmin\Cache\Proxy\Standard( $this->context );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->mock, $this->context );
	}


	public function testDelete()
	{
		$this->mock->expects( $this->once() )->method( 'delete' )
			->with( $this->equalTo( 'test' ) );

		$this->object->delete( 'test' );
	}


	public function testDeleteMultiple()
	{
		$this->mock->expects( $this->once() )->method( 'deleteMultiple' )
			->with( $this->equalTo( array( 'test' ) ) );

		$this->object->deleteMultiple( array( 'test' ) );
	}


	public function testDeleteByTags()
	{
		$this->mock->expects( $this->once() )->method( 'deleteByTags' )
			->with( $this->equalTo( array( 'tag1', 'tag2' ) ) );

		$this->object->deleteByTags( array( 'tag1', 'tag2' ) );
	}


	public function testClear()
	{
		$this->mock->expects( $this->once() )->method( 'clear' );
		$this->object->clear();
	}


	public function testGet()
	{
		$this->mock->expects( $this->once() )->method( 'get' )
			->with( $this->equalTo( 't:1' ) )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->get( 't:1' ) );
	}


	public function testGetMultiple()
	{
		$this->mock->expects( $this->once() )->method( 'getMultiple' )
			->with( $this->equalTo( array( 't:1' ) ) )
			->will( $this->returnValue( array( 't:1' => 'test' ) ) );

		$this->assertEquals( array( 't:1' => 'test' ), $this->object->getMultiple( array( 't:1' ) ) );
	}


	public function testGetMultipleByTags()
	{
		$this->mock->expects( $this->once() )->method( 'getMultipleByTags' )
			->with( $this->equalTo( array( 'tag1', 'tag2' ) ) )
			->will( $this->returnValue( array( 't:1' => 'test1', 't:2' => 'test2' ) ) );

		$expected = array( 't:1' => 'test1', 't:2' => 'test2' );
		$result = $this->object->getMultipleByTags( array( 'tag1', 'tag2' ) );

		$this->assertEquals( $expected, $result );
	}


	public function testSet()
	{
		$this->mock->expects( $this->once() )->method( 'set' )
			->with(
				$this->equalTo( 't:1' ),
				$this->equalTo( 'test 1' ),
				$this->equalTo( '2000-01-01 00:00:00' ),
				$this->equalTo( array( 'tag1', 'tag2' ) )
			);

		$this->object->set( 't:1', 'test 1', '2000-01-01 00:00:00', array( 'tag1', 'tag2' ) );
	}


	public function testSetMultiple()
	{
		$this->mock->expects( $this->once() )->method( 'setMultiple' )
			->with(
				$this->equalTo( array( 't:1' => 'test 1' ) ),
				$this->equalTo( array( 't:1' => '2000-01-01 00:00:00' ) ),
				$this->equalTo( array( 'tag1', 'tag2' ) )
			);

		$this->object->setMultiple( array( 't:1' => 'test 1' ), array( 't:1' => '2000-01-01 00:00:00' ), array( 'tag1', 'tag2' ) );
	}
}
