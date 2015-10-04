<?php

/**
 * Test class for MAdmin_Cache_Proxy_Standard.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MAdmin_Cache_Proxy_StandardTest extends PHPUnit_Framework_TestCase
{
	private $mock;
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

		$this->mock = $this->getMockBuilder( 'MW_Cache_DB' )
			->disableOriginalConstructor()->getMock();

		$manager = $this->getMockBuilder( 'MAdmin_Cache_Manager_Standard' )
			->setConstructorArgs( array( $this->context ) )->getMock();

		$manager->expects( $this->once() )->method( 'getCache' )
			->will( $this->returnValue( $this->mock ) );

		$name = 'MAdminCacheProxyDefaultTest';
		$this->context->getConfig()->set( 'classes/cache/manager/name', $name );

		MAdmin_Cache_Manager_Factory::injectManager( 'MAdmin_Cache_Manager_' . $name, $manager );

		$this->object = new MAdmin_Cache_Proxy_Standard( $this->context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
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


	public function testDeleteList()
	{
		$this->mock->expects( $this->once() )->method( 'deleteList' )
			->with( $this->equalTo( array( 'test' ) ) );

		$this->object->deleteList( array( 'test' ) );
	}


	public function testDeleteByTags()
	{
		$this->mock->expects( $this->once() )->method( 'deleteByTags' )
			->with( $this->equalTo( array( 'tag1', 'tag2' ) ) );

		$this->object->deleteByTags( array( 'tag1', 'tag2' ) );
	}


	public function testFlush()
	{
		$this->mock->expects( $this->once() )->method( 'flush' );
		$this->object->flush();
	}


	public function testGet()
	{
		$this->mock->expects( $this->once() )->method( 'get' )
			->with( $this->equalTo( 't:1' ) )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->get( 't:1' ) );
	}


	public function testGetList()
	{
		$this->mock->expects( $this->once() )->method( 'getList' )
			->with( $this->equalTo( array( 't:1' ) ) )
			->will( $this->returnValue( array( 't:1' => 'test' ) ) );

		$this->assertEquals( array( 't:1' => 'test' ), $this->object->getList( array( 't:1' ) ) );
	}


	public function testGetListByTags()
	{
		$this->mock->expects( $this->once() )->method( 'getListByTags' )
			->with( $this->equalTo( array( 'tag1', 'tag2' ) ) )
			->will( $this->returnValue( array( 't:1' => 'test1', 't:2' => 'test2' ) ) );

		$expected = array( 't:1' => 'test1', 't:2' => 'test2' );
		$result = $this->object->getListByTags( array( 'tag1', 'tag2' ) );

		$this->assertEquals( $expected, $result );
	}


	public function testSet()
	{
		$this->mock->expects( $this->once() )->method( 'set' )
			->with(
				$this->equalTo( 't:1' ),
				$this->equalTo( 'test 1' ),
				$this->equalTo( array( 'tag1', 'tag2' ) ),
				$this->equalTo( '2000-01-01 00:00:00' )
			);

		$this->object->set( 't:1', 'test 1', array( 'tag1', 'tag2' ), '2000-01-01 00:00:00' );
	}


	public function testSetList()
	{
		$this->mock->expects( $this->once() )->method( 'setList' )
			->with(
				$this->equalTo( array( 't:1' => 'test 1' ) ),
				$this->equalTo( array( 'tag1', 'tag2' ) ),
				$this->equalTo( array( 't:1' => '2000-01-01 00:00:00' ) )
			);

		$this->object->setList( array( 't:1' => 'test 1' ), array( 'tag1', 'tag2' ), array( 't:1' => '2000-01-01 00:00:00' ) );
	}
}
