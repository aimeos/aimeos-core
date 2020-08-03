<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Service\Provider;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$serviceItem = \Aimeos\MShop\Service\Manager\Factory::create( $this->context )->createItem()->setId( -1 );

		$this->object = $this->getMockBuilder( TestBase::class )
			->setConstructorArgs( [$this->context, $serviceItem] )
			->setMethods( ['test'] )
			->getMock();

		\Aimeos\MShop::cache( true );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );

		unset( $this->object );
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
		$item = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )->createItem();

		$this->expectException( \Aimeos\MShop\Service\Exception::class );
		$this->object->query( $item );
	}


	public function testUpdateAsync()
	{
		$this->assertFalse( $this->object->updateAsync() );
	}


	public function testUpdatePush()
	{
		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();

		$response->expects( $this->once() )->method( 'withStatus' )->will( $this->returnValue( $response ) );

		$result = $this->object->updatePush( $request, $response );

		$this->assertInstanceOf( \Psr\Http\Message\ResponseInterface::class, $result );
	}


	public function testUpdateSync()
	{
		$orderItem = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )->createItem();
		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();

		$result = $this->object->updateSync( $request, $orderItem );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $result );
	}


	public function testCheckConfig()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );

		$args = [['key' => ['code' => 'key', 'type' => 'invalid', 'required' => true]], ['key' => 'abc']];
		$this->access( 'checkConfig' )->invokeArgs( $this->object, $args );
	}


	public function testGetCustomerData()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'customer' );
		$customerId = $manager->findItem( 'test@example.com' )->getId();

		$this->assertNull( $this->access( 'getCustomerData' )->invokeArgs( $this->object, [$customerId, 'token'] ) );
	}


	public function testSetCustomerData()
	{
		$stub = $this->getMockBuilder( \Aimeos\MShop\Customer\Manager\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['saveItem'] )
			->getMock();

		\Aimeos\MShop::inject( 'customer', $stub );

		$stub->expects( $this->once() )->method( 'saveItem' );

		$manager = \Aimeos\MShop::create( $this->context, 'customer' );
		$customerId = $manager->findItem( 'test@example.com' )->getId();

		$this->access( 'setCustomerData' )->invokeArgs( $this->object, [$customerId, 'token', 'abcd'] );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Service\Provider\Base::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}


class TestBase
	extends \Aimeos\MShop\Service\Provider\Base
	implements \Aimeos\MShop\Service\Provider\Iface
{
	public function setConfigFE( \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem,
		array $attributes ) : \Aimeos\MShop\Order\Item\Base\Service\Iface
	{
		return $orderServiceItem;
	}
}
