<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Provider;


class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$serviceItem = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context )->createItem();

		$this->object = new TestBase( $this->context, $serviceItem );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCalcDateLimit()
	{
		$this->assertEquals( '2013-10-15', $this->object->calcDateLimitPublic( 1382100000, 3 ) );
	}


	public function testCalcDateLimitWeekdays()
	{
		$this->assertEquals( '2013-10-18', $this->object->calcDateLimitPublic( 1382186400, 0, true ) );
		$this->assertEquals( '2013-10-18', $this->object->calcDateLimitPublic( 1382272800, 0, true ) );
	}


	public function testCalcDateLimitHolidays()
	{
		$this->assertEquals( '2013-10-16', $this->object->calcDateLimitPublic( 1382100000, 0, false, '2013-10-17, 2013-10-18' ) );
	}


	public function testCheckConfigBE()
	{
		$this->assertEquals( [], $this->object->checkConfigBE( [] ) );
	}


	public function testGetConfigValue()
	{
		$this->object->injectGlobalConfigBE( array( 'payment.url-success' => 'https://url.to/ok' ) );
		$this->assertEquals( 'https://url.to/ok', $this->object->getConfigValuePublic( array( 'payment.url-success' ) ) );
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


	public function testUpdateSync()
	{
		$response = null; $header = [];
		$result = $this->object->updateSync( [], 'body', $response, $header );

		$this->assertEquals( null, $result );
	}


	public function testCheckConfigBoolean()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'boolean', 'required' => true ) ), array( 'key' => '0' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigBooleanInvalid()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'boolean', 'required' => true ) ), array( 'key' => 'a' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a true/false value' ), $result );
	}


	public function testCheckConfigString()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'string', 'required' => true ) ), array( 'key' => 'abc' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigStringInvalid()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'string', 'required' => true ) ), array( 'key' => new \stdClass() ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a string' ), $result );
	}


	public function testCheckConfigInteger()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'integer', 'required' => true ) ), array( 'key' => '123' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigIntegerInvalid()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'integer', 'required' => true ) ), array( 'key' => 'abc' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not an integer number' ), $result );
	}


	public function testCheckConfigNumber()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'number', 'required' => true ) ), array( 'key' => '10.25' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigNumberInvalid()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'number', 'required' => true ) ), array( 'key' => 'abc' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a number' ), $result );
	}


	public function testCheckConfigDate()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'date', 'required' => true ) ), array( 'key' => '2000-01-01' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigDateInvalid()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'date', 'required' => true ) ), array( 'key' => '01/01/2000' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a date' ), $result );
	}


	public function testCheckConfigDatetime()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'datetime', 'required' => true ) ), array( 'key' => '2000-01-01 00:00:00' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigDatetimeInvalid()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'datetime', 'required' => true ) ), array( 'key' => '01/01/2000' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a date and time' ), $result );
	}


	public function testCheckConfigSelect()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'select', 'required' => true, 'default' => array( 'test' ) ) ), array( 'key' => 'test' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigSelectInvalid()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'select', 'required' => true, 'default' => array( 'test' ) ) ), array( 'key' => 'test2' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a listed value' ), $result );
	}


	public function testCheckConfigMap()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'map', 'required' => true ) ), array( 'key' => array( 'a' => 'test' ) ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => null ), $result );
	}


	public function testCheckConfigMapInvalid()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$args = array( array( 'key' => array( 'type' => 'map', 'required' => true ) ), array( 'key' => 'test' ) );
		$result = $method->invokeArgs( $this->object, $args );

		$this->assertEquals( array( 'key' => 'Not a key/value map' ), $result );
	}


	public function testCheckConfigInvalid()
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Service\Provider\Base' );
		$method = $class->getMethod( 'checkConfig' );
		$method->setAccessible( true );

		$this->setExpectedException( '\Aimeos\MShop\Service\Exception' );

		$args = array( array( 'key' => array( 'type' => 'invalid', 'required' => true ) ), array( 'key' => 'abc' ) );
		$method->invokeArgs( $this->object, $args );
	}
}


class TestBase extends \Aimeos\MShop\Service\Provider\Base
{
	public function calcDateLimitPublic( $ts, $days = 0, $bd = false, $hd = '' )
	{
		return $this->calcDateLimit( $ts, $days, $bd, $hd );
	}

	public function getConfigValuePublic( array $keys )
	{
		return $this->getConfigValue( $keys );
	}

	public function setConfigFE( \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem, array $attributes )
	{
	}
}
