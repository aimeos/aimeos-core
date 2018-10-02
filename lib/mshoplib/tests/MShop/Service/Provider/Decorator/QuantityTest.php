<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class QuantityTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context );
		$this->servItem = $servManager->createItem();

		$this->mockProvider = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Decorator\\Example' )
			->disableOriginalConstructor()->getMock();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Quantity( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->context, $this->servItem, $this->mockProvider );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'quantity.packagesize', $result );
		$this->assertArrayHasKey( 'quantity.packagecosts', $result );
	}


	public function testCheckConfigBEOK()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'quantity.packagecosts' => '10.0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'null', $result['quantity.packagecosts'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'quantity.packagecosts' => [] );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'string', $result['quantity.packagecosts'] );
	}


	public function testCalcPrice()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'price' );
		$price = $manager->createItem();

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $price ) );

		$this->servItem->setConfig( array( 'quantity.packagecosts' => '1' ) );

		$this->assertEquals( '14.00', $this->object->calcPrice( $this->getOrderBaseItem( '2008-02-15 12:34:56' ) )->getCosts() );
	}


	public function testCalcPriceBundle()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'price' );
		$price = $manager->createItem();

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $price ) );

		$this->servItem->setConfig( array( 'quantity.packagecosts' => '1.0' ) );

		$this->assertEquals( '4.00', $this->object->calcPrice( $this->getOrderBaseItem( '2009-03-18 16:14:32' ) )->getCosts() );
	}


	public function testCalcPricePackageHalf()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'price' );
		$price = $manager->createItem();

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $price ) );

		$this->servItem->setConfig( array( 'quantity.packagesize' => '5', 'quantity.packagecosts' => '2.50' ) );

		$this->assertEquals( '7.50', $this->object->calcPrice( $this->getOrderBaseItem( '2008-02-15 12:34:56' ) )->getCosts() );
	}


	public function testCalcPricePackageFull()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'price' );
		$price = $manager->createItem();

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $price ) );

		$this->servItem->setConfig( array( 'quantity.packagesize' => '7', 'quantity.packagecosts' => '5.00' ) );

		$this->assertEquals( '10.00', $this->object->calcPrice( $this->getOrderBaseItem( '2008-02-15 12:34:56' ) )->getCosts() );
	}


	/**
	 * @return \Aimeos\MShop\Order\Item\Base\Iface
	 */
	protected function getOrderBaseItem( $paydate )
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'order' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $paydate ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order item found' );
		}

		$baseManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' );
		return $baseManager->load( $item->getBaseId() );
	}
}