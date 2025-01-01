<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $mock;
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$servManager = \Aimeos\MShop::create( $this->context, 'service' );
		$search = $servManager->filter();
		$search->setConditions( $search->compare( '==', 'service.provider', 'Standard' ) );

		if( ( $item = $servManager->search( $search, ['price'] )->first() ) === null ) {
			throw new \RuntimeException( 'No order base item found' );
		}

		$this->mock = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Payment\PrePay::class )
			->setConstructorArgs( array( $this->context, $item ) )
			->onlyMethods( array( 'calcPrice', 'checkConfigBE', 'checkConfigFE', 'getConfigBE',
				'getConfigFE', 'injectGlobalConfigBE', 'isAvailable', 'isImplemented', 'query',
				'cancel', 'capture', 'process', 'refund', 'repay', 'setConfigFE',
				'updateAsync', 'updatePush', 'updateSync' ) )
			->getMock();

		$this->object = new TestBase( $this->mock, $this->context, $item );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testCalcPrice()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->mock->expects( $this->once() )->method( 'calcPrice' )->willReturn( $item->getPrice() );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $this->object->calcPrice( $item ) );
	}


	public function testCheckConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'checkConfigBE' )->willReturn( [] );

		$this->assertEquals( [], $this->object->checkConfigBE( [] ) );
	}


	public function testCheckConfigFE()
	{
		$this->mock->expects( $this->once() )->method( 'checkConfigFE' )->willReturn( [] );

		$this->assertEquals( [], $this->object->checkConfigFE( [] ) );
	}


	public function testGetConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'getConfigBE' )->willReturn( [] );

		$this->assertEquals( [], $this->object->getConfigBE() );
	}


	public function testGetConfigFE()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->mock->expects( $this->once() )->method( 'getConfigFE' )->willReturn( [] );

		$this->assertEquals( [], $this->object->getConfigFE( $item ) );
	}


	public function testInjectGlobalConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'injectGlobalConfigBE' )->willReturnSelf();

		$this->object->injectGlobalConfigBE( [] );
	}


	public function testIsAvailable()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->mock->expects( $this->once() )->method( 'isAvailable' )->willReturn( true );

		$this->assertEquals( true, $this->object->isAvailable( $item ) );
	}

	public function testIsImplemented()
	{
		$this->mock->expects( $this->once() )->method( 'isImplemented' )->willReturn( true );

		$this->assertTrue( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_QUERY ) );
	}


	public function testCancel()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->mock->expects( $this->once() )->method( 'cancel' );

		$this->object->cancel( $item );
	}


	public function testCapture()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->mock->expects( $this->once() )->method( 'capture' );

		$this->object->capture( $item );
	}


	public function testProcess()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->mock->expects( $this->once() )->method( 'process' );

		$this->object->process( $item, array( 'params' ) );
	}


	public function testQuery()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->mock->expects( $this->once() )->method( 'query' );

		$this->object->query( $item );
	}


	public function testRefund()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->mock->expects( $this->once() )->method( 'refund' );

		$this->object->refund( $item );
	}


	public function testRepay()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->mock->expects( $this->once() )->method( 'repay' );

		$this->object->repay( $item );
	}


	public function testSetConfigFE()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order/service' )->create();

		$this->mock->expects( $this->once() )->method( 'setConfigFE' );

		$this->object->setConfigFE( $item, [] );
	}


	public function testUpdateAsync()
	{
		$this->mock->expects( $this->once() )->method( 'updateAsync' );

		$this->object->updateAsync();
	}


	public function testUpdatePush()
	{
		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();
		$response = $this->getMockBuilder( \Psr\Http\Message\ResponseInterface::class )->getMock();

		$this->mock->expects( $this->once() )->method( 'updatePush' )->willReturn( $response );

		$result = $this->object->updatePush( $request, $response );

		$this->assertInstanceOf( \Psr\Http\Message\ResponseInterface::class, $result );
	}


	public function testUpdateSync()
	{
		$orderItem = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();

		$this->mock->expects( $this->once() )->method( 'updateSync' )->willReturn( $orderItem );

		$result = $this->object->updateSync( $request, $orderItem );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $result );
	}
}


class TestBase
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{

}
