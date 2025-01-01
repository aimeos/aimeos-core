<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2025
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class QuantityTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$servManager = \Aimeos\MShop::create( $this->context, 'service' );
		$this->servItem = $servManager->create();

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Example::class )
			->disableOriginalConstructor()->getMock();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Quantity( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->servItem, $this->mockProvider );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )->method( 'getConfigBE' )->willReturn( [] );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'quantity.packagesize', $result );
		$this->assertArrayHasKey( 'quantity.packagecosts', $result );
	}


	public function testCheckConfigBEOK()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$attributes = array( 'quantity.packagecosts' => '10.0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['quantity.packagecosts'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->willReturn( [] );

		$attributes = array( 'quantity.packagecosts' => [] );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['quantity.packagecosts'] );
	}


	public function testCalcPrice()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$price = $manager->create();

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->willReturn( $price );

		$this->servItem->setConfig( array( 'quantity.packagecosts' => '1' ) );

		$this->assertEquals( '14.00', $this->object->calcPrice( $this->getOrderBaseItem( '2008-02-15 12:34:56' ) )->getCosts() );
	}


	public function testCalcPriceBundle()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$price = $manager->create();

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->willReturn( $price );

		$this->servItem->setConfig( array( 'quantity.packagecosts' => '1.0' ) );

		$this->assertEquals( '4.00', $this->object->calcPrice( $this->getOrderBaseItem( '2009-03-18 16:14:32' ) )->getCosts() );
	}


	public function testCalcPricePackageHalf()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$price = $manager->create();

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->willReturn( $price );

		$this->servItem->setConfig( array( 'quantity.packagesize' => '5', 'quantity.packagecosts' => '2.50' ) );

		$this->assertEquals( '7.50', $this->object->calcPrice( $this->getOrderBaseItem( '2008-02-15 12:34:56' ) )->getCosts() );
	}


	public function testCalcPricePackageFull()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$price = $manager->create();

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->willReturn( $price );

		$this->servItem->setConfig( array( 'quantity.packagesize' => '7', 'quantity.packagecosts' => '5.00' ) );

		$this->assertEquals( '10.00', $this->object->calcPrice( $this->getOrderBaseItem( '2008-02-15 12:34:56' ) )->getCosts() );
	}


	/**
	 * @return \Aimeos\MShop\Order\Item\Iface
	 */
	protected function getOrderBaseItem( $paydate )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order' );
		$filter = $manager->filter()->add( 'order.datepayment', '==', $paydate );

		return $manager->search( $filter, ['order/product'] )->first( new \RuntimeException( 'No order item found' ) );
	}
}
