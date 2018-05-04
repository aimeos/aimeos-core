<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Service\Provider;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$serviceItem = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context )->createItem();

		$this->object = $this->getMockBuilder( '\Aimeos\MShop\Service\Provider\Base' )
			->setConstructorArgs( [$this->context, $serviceItem] )
			->setMethods( null )
			->getMock();

		\Aimeos\MShop\Factory::setCache( true );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );

		unset( $this->object );
	}


	public function testCalcDateLimit()
	{
		$this->assertEquals( '2013-10-15', $this->access( 'calcDateLimit' )->invokeArgs( $this->object, [1382100000, 3] ) );
	}


	public function testCalcDateLimitWeekdays()
	{
		$this->assertEquals( '2013-10-18', $this->access( 'calcDateLimit' )->invokeArgs( $this->object, [1382186400, 0, true] ) );
		$this->assertEquals( '2013-10-18', $this->access( 'calcDateLimit' )->invokeArgs( $this->object, [1382272800, 0, true] ) );
	}


	public function testCalcDateLimitHolidays()
	{
		$result = $this->access( 'calcDateLimit' )->invokeArgs( $this->object, [1382100000, 0, false, '2013-10-17, 2013-10-18'] );
		$this->assertEquals( '2013-10-16', $result );
	}


	public function testCheckConfigBE()
	{
		$this->assertEquals( [], $this->object->checkConfigBE( [] ) );
	}


	public function testGetConfigValue()
	{
		$this->object->injectGlobalConfigBE( ['payment.url-success' => 'https://url.to/ok'] );
		$result = $this->access( 'getConfigValue' )->invokeArgs( $this->object, ['payment.url-success'] );

		$this->assertEquals( 'https://url.to/ok', $result );
	}


	public function testQuery()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->setExpectedException( '\\Aimeos\\MShop\\Service\\Exception' );
		$this->object->query( $item );
	}


	public function testUpdateAsync()
	{
		$this->assertFalse( $this->object->updateAsync() );
	}


	public function testUpdatePush()
	{
		$request = $this->getMockBuilder( '\Psr\Http\Message\ServerRequestInterface' )->getMock();
		$response = $this->getMockBuilder( '\Psr\Http\Message\ResponseInterface' )->getMock();

		$response->expects( $this->once() )->method( 'withStatus' )->will( $this->returnValue( $response ) );

		$result = $this->object->updatePush( $request, $response );

		$this->assertInstanceOf( '\Psr\Http\Message\ResponseInterface', $result );
	}


	public function testUpdateSync()
	{
		$orderItem = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();
		$request = $this->getMockBuilder( '\Psr\Http\Message\ServerRequestInterface' )->getMock();

		$result = $this->object->updateSync( $request, $orderItem );

		$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Iface', $result );
	}


	public function testCheckConfigBoolean()
	{
		$args = array( array( 'key' => array( 'type' => 'boolean', 'required' => true ) ), array( 'key' => '0' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigBooleanInvalid()
	{
		$args = array( array( 'key' => array( 'type' => 'boolean', 'required' => true ) ), array( 'key' => 'a' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a true/false value' ), $result );
	}


	public function testCheckConfigString()
	{
		$args = array( array( 'key' => array( 'type' => 'string', 'required' => true ) ), array( 'key' => 'abc' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigStringInvalid()
	{
		$args = array( array( 'key' => array( 'type' => 'string', 'required' => true ) ), array( 'key' => new \stdClass() ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a string' ), $result );
	}


	public function testCheckConfigText()
	{
		$args = array( array( 'key' => array( 'type' => 'text', 'required' => true ) ), array( 'key' => 'abc' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigTextInvalid()
	{
		$args = array( array( 'key' => array( 'type' => 'text', 'required' => true ) ), array( 'key' => new \stdClass() ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a string' ), $result );
	}


	public function testCheckConfigInteger()
	{
		$args = array( array( 'key' => array( 'type' => 'integer', 'required' => true ) ), array( 'key' => '123' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigIntegerInvalid()
	{
		$args = array( array( 'key' => array( 'type' => 'integer', 'required' => true ) ), array( 'key' => 'abc' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not an integer number' ), $result );
	}


	public function testCheckConfigNumber()
	{
		$args = array( array( 'key' => array( 'type' => 'number', 'required' => true ) ), array( 'key' => '10.25' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigNumberInvalid()
	{
		$args = array( array( 'key' => array( 'type' => 'number', 'required' => true ) ), array( 'key' => 'abc' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a number' ), $result );
	}


	public function testCheckConfigDate()
	{
		$args = array( array( 'key' => array( 'type' => 'date', 'required' => true ) ), array( 'key' => '2000-01-01' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigDateInvalid()
	{
		$args = array( array( 'key' => array( 'type' => 'date', 'required' => true ) ), array( 'key' => '01/01/2000' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a date' ), $result );
	}


	public function testCheckConfigDatetime()
	{
		$args = array( array( 'key' => array( 'type' => 'datetime', 'required' => true ) ), array( 'key' => '2000-01-01 00:00:00' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigDatetimeInvalid()
	{
		$args = array( array( 'key' => array( 'type' => 'datetime', 'required' => true ) ), array( 'key' => '01/01/2000' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a date and time' ), $result );
	}


	public function testCheckConfigSelectList()
	{
		$args = [['key' => ['type' => 'select', 'required' => true, 'default' => ['test' => 'val']]], ['key' => 'test']];
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigSelectInvalid()
	{
		$args = array( array( 'key' => array( 'type' => 'select', 'required' => true, 'default' => array( 'test' ) ) ), array( 'key' => 'test2' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a listed value' ), $result );
	}


	public function testCheckConfigMap()
	{
		$args = array( array( 'key' => array( 'type' => 'map', 'required' => true ) ), array( 'key' => array( 'a' => 'test' ) ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigMapInvalid()
	{
		$args = array( array( 'key' => array( 'type' => 'map', 'required' => true ) ), array( 'key' => 'test' ) );
		$result = $this->access( 'checkConfig' )->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a key/value map' ), $result );
	}


	public function testCheckConfigInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Service\Exception' );

		$args = array( array( 'key' => array( 'type' => 'invalid', 'required' => true ) ), array( 'key' => 'abc' ) );
		$this->access( 'checkConfig' )->invokeArgs( $this->object, $args );
	}


	public function testGetCustomerData()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'customer' );
		$customerId = $manager->findItem( 'UTC001' )->getId();

		$this->assertNull( $this->access( 'getCustomerData' )->invokeArgs( $this->object, [$customerId, 'token'] ) );
	}


	public function testSetCustomerData()
	{
		$stub = $this->getMockBuilder( '\Aimeos\MShop\Customer\Manager\Lists\Standard' )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['saveItem'] )
			->getMock();

		\Aimeos\MShop\Factory::injectManager( $this->context, 'customer/lists', $stub );

		$stub->expects( $this->once() )->method( 'saveItem' );

		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'customer' );
		$customerId = $manager->findItem( 'UTC001' )->getId();

		$this->access( 'setCustomerData' )->invokeArgs( $this->object, [$customerId, 'token', 'abcd'] );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
