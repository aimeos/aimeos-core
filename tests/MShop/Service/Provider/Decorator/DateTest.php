<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2025
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class DateTest extends \PHPUnit\Framework\TestCase
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

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Date::class )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Date( $this->mockProvider, $this->context, $this->servItem );
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

		$this->assertArrayHasKey( 'date.minimumdays', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$attributes = array(
			'date.minimumdays' => '0',
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNull( $result['date.minimumdays'] );
	}


	public function testCheckConfigBENoConfig()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNull( $result['date.minimumdays'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$attributes = array(
			'date.minimumdays' => [],
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertIsString( $result['date.minimumdays'] );
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

		$this->assertMatchesRegularExpression( '/[0-9][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]/', $config['date.value']->getDefault() );
	}


	public function testCheckConfigFE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->willReturn( [] );

		$config = array( 'date.value' => date( 'Y-m-d' ) );
		$expected = array( 'date.value' => null );

		$result = $this->object->checkConfigFE( $config );

		$this->assertEquals( $expected, $result );
	}


	public function testCheckConfigFEwrongType()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->willReturn( [] );

		$config = array( 'date.value' => 0 );
		$result = $this->object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'date.value', $result );
		$this->assertFalse( $result['date.value'] === null );
	}


	public function testCheckConfigFEbefore()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->willReturn( [] );

		$this->servItem->setConfig( ['date.minimumdays' => '1'] );

		$config = array( 'date.value' => date( 'Y-m-d' ) );
		$result = $this->object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'date.value', $result );
		$this->assertFalse( $result['date.value'] === null );
	}
}
