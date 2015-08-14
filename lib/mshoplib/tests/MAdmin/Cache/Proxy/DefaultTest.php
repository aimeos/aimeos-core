<?php

/**
 * Test class for MAdmin_Cache_Proxy_Default.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MAdmin_Cache_Proxy_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_mock;
	private $_object;
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

		$this->_mock = $this->getMockBuilder( 'MW_Cache_DB' )
			->disableOriginalConstructor()->getMock();

		$manager = $this->getMockBuilder( 'MAdmin_Cache_Manager_Default' )
			->setConstructorArgs( array( $this->_context ) )->getMock();

		$manager->expects( $this->once() )->method( 'getCache' )
			->will( $this->returnValue( $this->_mock ) );

		$name = 'MAdminCacheProxyDefaultTest';
		$this->_context->getConfig()->set( 'classes/cache/manager/name', $name );

		MAdmin_Cache_Manager_Factory::injectManager( 'MAdmin_Cache_Manager_' . $name, $manager );

		$this->_object = new MAdmin_Cache_Proxy_Default( $this->_context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object, $this->_mock, $this->_context );
	}


	public function testDelete()
	{
		$this->_mock->expects( $this->once() )->method( 'delete' )
			->with( $this->equalTo( 'test' ) );

		$this->_object->delete( 'test' );
	}


	public function testDeleteList()
	{
		$this->_mock->expects( $this->once() )->method( 'deleteList' )
			->with( $this->equalTo( array( 'test' ) ) );

		$this->_object->deleteList( array( 'test' ) );
	}


	public function testDeleteByTags()
	{
		$this->_mock->expects( $this->once() )->method( 'deleteByTags' )
			->with( $this->equalTo( array( 'tag1', 'tag2' ) ) );

		$this->_object->deleteByTags( array( 'tag1', 'tag2' ) );
	}


	public function testFlush()
	{
		$this->_mock->expects( $this->once() )->method( 'flush' );
		$this->_object->flush();
	}


	public function testGet()
	{
		$this->_mock->expects( $this->once() )->method( 'get' )
			->with( $this->equalTo( 't:1' ) )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->_object->get( 't:1' ) );
	}


	public function testGetList()
	{
		$this->_mock->expects( $this->once() )->method( 'getList' )
			->with( $this->equalTo( array( 't:1' ) ) )
			->will( $this->returnValue( array( 't:1' => 'test' ) ) );

		$this->assertEquals( array( 't:1' => 'test' ), $this->_object->getList( array( 't:1' ) ) );
	}


	public function testGetListByTags()
	{
		$this->_mock->expects( $this->once() )->method( 'getListByTags' )
			->with( $this->equalTo( array( 'tag1', 'tag2' ) ) )
			->will( $this->returnValue( array( 't:1' => 'test1', 't:2' => 'test2' ) ) );

		$expected = array( 't:1' => 'test1', 't:2' => 'test2' );
		$result = $this->_object->getListByTags( array( 'tag1', 'tag2' ) );

		$this->assertEquals( $expected, $result );
	}


	public function testSet()
	{
		$this->_mock->expects( $this->once() )->method( 'set' )
			->with(
				$this->equalTo( 't:1' ),
				$this->equalTo( 'test 1' ),
				$this->equalTo( array( 'tag1', 'tag2' ) ),
				$this->equalTo( '2000-01-01 00:00:00' )
			);

		$this->_object->set( 't:1', 'test 1', array( 'tag1', 'tag2' ), '2000-01-01 00:00:00' );
	}


	public function testSetList()
	{
		$this->_mock->expects( $this->once() )->method( 'setList' )
			->with(
				$this->equalTo( array( 't:1' => 'test 1' ) ),
				$this->equalTo( array( 'tag1', 'tag2' ) ),
				$this->equalTo( array( 't:1' => '2000-01-01 00:00:00' ) )
			);

		$this->_object->setList( array( 't:1' => 'test 1' ), array( 'tag1', 'tag2' ), array( 't:1' => '2000-01-01 00:00:00' ) );
	}
}
