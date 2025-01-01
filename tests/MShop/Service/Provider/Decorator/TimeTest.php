<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2025
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class TimeTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$servManager = \Aimeos\MShop::create( $this->context, 'service' );
		$this->servItem = $servManager->create()->setCode( 'unitdeliverycode' );

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Time::class )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Time( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'getConfigBE' )
			->willReturn( [] );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'time.start', $result );
		$this->assertArrayHasKey( 'time.end', $result );
		$this->assertArrayHasKey( 'time.weekdays', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$attributes = array(
			'time.start' => '00:00',
			'time.end' => '23:59',
			'time.weekdays' => '1,2,3,4,5,6,7',
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 3, count( $result ) );
		$this->assertNull( $result['time.start'] );
		$this->assertNull( $result['time.end'] );
		$this->assertNull( $result['time.weekdays'] );
	}


	public function testCheckConfigBENoConfig()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 3, count( $result ) );
		$this->assertNull( $result['time.start'] );
		$this->assertNull( $result['time.end'] );
		$this->assertNull( $result['time.weekdays'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$attributes = array(
			'time.start' => [],
			'time.end' => 1,
			'time.weekdays' => new \stdClass(),
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 3, count( $result ) );
		$this->assertIsString( $result['time.start'] );
		$this->assertIsString( $result['time.end'] );
		$this->assertIsString( $result['time.weekdays'] );
	}


	public function testGetConfigFE()
	{
		$orderManager = \Aimeos\MShop::create( \TestHelper::context(), 'order' );
		$search = $orderManager->filter();
		$expr = array(
			$search->compare( '==', 'order.channel', 'web' ),
			$search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED )
		);
		$search->setConditions( $search->and( $expr ) );
		$orderItems = $orderManager->search( $search )->toArray();

		if( ( $order = reset( $orderItems ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No Order found with statuspayment "%1$s" and type "%2$s"', \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, 'web' ) );
		}


		$this->mockProvider->expects( $this->once() )->method( 'getConfigFE' )->willReturn( [] );

		$basket = $orderManager->get( $order->getId(), ['order/service'] );
		$config = $this->object->getConfigFE( $basket );

		$this->assertMatchesRegularExpression( '/[0-2][0-9]:[0-5][0-9]/', $config['time.hourminute']->getDefault() );
	}


	public function testCheckConfigFE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->willReturn( [] );

		$config = array( 'time.hourminute' => '12:00' );
		$expected = array( 'time.hourminute' => null );

		$result = $this->object->checkConfigFE( $config );

		$this->assertEquals( $expected, $result );
	}


	public function testCheckConfigFEshort()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->willReturn( [] );

		$config = array( 'time.hourminute' => '9:00' );
		$expected = array( 'time.hourminute' => null );

		$result = $this->object->checkConfigFE( $config );

		$this->assertEquals( $expected, $result );
	}


	public function testCheckConfigFEwrongType()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->willReturn( [] );

		$config = array( 'time.hourminute' => 0 );
		$result = $this->object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'time.hourminute', $result );
		$this->assertFalse( $result['time.hourminute'] === null );
	}


	public function testCheckConfigFEbefore()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->willReturn( [] );

		$this->servItem->setConfig( ['time.start' => '10:00'] );

		$config = array( 'time.hourminute' => '09:00' );
		$result = $this->object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'time.hourminute', $result );
		$this->assertFalse( $result['time.hourminute'] === null );
	}


	public function testCheckConfigFEafter()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->willReturn( [] );

		$this->servItem->setConfig( ['time.end' => '18:00'] );

		$config = array( 'time.hourminute' => '19:00' );
		$result = $this->object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'time.hourminute', $result );
		$this->assertFalse( $result['time.hourminute'] === null );
	}
}
