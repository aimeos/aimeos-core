<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class DateTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Factory::createManager( $this->context, 'service' );
		$this->servItem = $servManager->createItem()->setCode( '73' );

		$this->mockProvider = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Decorator\\Date' )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )
			->getSubManager( 'base' )->createItem();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Date( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'getConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'date.minimumdays', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'date.minimumdays' => '0',
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'null', $result['date.minimumdays'] );
	}


	public function testCheckConfigBENoConfig()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'null', $result['date.minimumdays'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array(
			'date.minimumdays' => [],
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'string', $result['date.minimumdays'] );
	}


	public function testGetConfigFE()
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$search = $orderManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_WEB ),
			$search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$orderItems = $orderManager->searchItems( $search );

		if( ( $order = reset( $orderItems ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No Order found with statuspayment "%1$s" and type "%2$s"', \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, \Aimeos\MShop\Order\Item\Base::TYPE_WEB ) );
		}


		$this->mockProvider->expects( $this->once() )->method( 'getConfigFE' )->will( $this->returnValue( [] ) );

		$basket = $orderBaseManager->load( $order->getBaseId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE );
		$config = $this->object->getConfigFE( $basket );

		$this->assertRegExp( '/[0-9][0-9][0-9][0-9]-[0-1][0-9]-[0-3][0-9]/', $config['date.value']->getDefault() );
	}


	public function testCheckConfigFE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->will( $this->returnValue( [] ) );

		$config = array( 'date.value' => date( 'Y-m-d' ) );
		$expected = array( 'date.value' => null );

		$result = $this->object->checkConfigFE( $config );

		$this->assertEquals( $expected, $result );
	}


	public function testCheckConfigFEwrongType()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->will( $this->returnValue( [] ) );

		$config = array( 'date.value' => 0 );
		$result = $this->object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'date.value', $result );
		$this->assertFalse( $result['date.value'] === null );
	}


	public function testCheckConfigFEbefore()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigFE' )
			->will( $this->returnValue( [] ) );

		$this->servItem->setConfig( ['date.minimumdays' => '1'] );

		$config = array( 'date.value' => date( 'Y-m-d' ) );
		$result = $this->object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'date.value', $result );
		$this->assertFalse( $result['date.value'] === null );
	}
}