<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2025
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class SupplierTest extends \PHPUnit\Framework\TestCase
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
		$this->servItem = $servManager->create();

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Supplier::class )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop::create( $this->context, 'order' )->create();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Supplier( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
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

		$this->assertIsArray( $config['supplier.code']->getDefault() );
		$this->assertGreaterThanOrEqual( 2, count( $config['supplier.code']->getDefault() ) );

		foreach( $config['supplier.code']->getDefault() as $string ) {
			$this->assertGreaterThan( 100, strlen( $string ) );
		}
	}


	public function testCheckConfigFE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->willReturn( [] );

		$attributes = array( 'supplier.code' => 'unitSupplier001' );
		$expected = array( 'supplier.code' => null );

		$result = $this->object->checkConfigFE( $attributes );

		$this->assertEquals( $expected, $result );
	}


	public function testCheckConfigFEwrongType()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->willReturn( [] );

		$config = array( 'supplier.code' => -1 );
		$result = $this->object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'supplier.code', $result );
		$this->assertNotNull( $result['supplier.code'] );
	}
}
