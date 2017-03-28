<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Test class for \Aimeos\MShop\Service\Provider\Decorator\OrderCheck.
 */
class OrderCheckTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->context->setUserId( null );

		$servManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context );
		$this->servItem = $servManager->createItem();

		$this->mockProvider = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Decorator\\OrderCheck' )
			->disableOriginalConstructor()->getMock();

		$this->basket = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )
			->getSubManager( 'base' )->createItem();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\OrderCheck( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\StandardMock', null );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'ordercheck.total-number-min', $result );
		$this->assertArrayHasKey( 'ordercheck.limit-days-pending', $result );
	}


	public function testCheckConfigBETotal()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'ordercheck.total-number-min' => '0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'null', $result['ordercheck.total-number-min'] );
	}


	public function testCheckConfigBETotalFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'ordercheck.total-number-min' => 'nope' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'string', $result['ordercheck.total-number-min'] );
	}


	public function testCheckConfigBELimit()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'ordercheck.limit-days-pending' => '0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'null', $result['ordercheck.limit-days-pending'] );
	}


	public function testCheckConfigBELimitFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'ordercheck.limit-days-pending' => 'nope' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'string', $result['ordercheck.limit-days-pending'] );
	}


	public function testIsAvailableNoUserId()
	{
		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableNoConfig()
	{
		$this->context->setUserId( 1 );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableTotal()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'mshop/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.total-number-min' => 1 ) );

		$mock = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'searchItems' )
			->will( $this->returnValue( array( $mock->createItem() ) ) );

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\StandardMock', $mock );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableTotalNotEnough()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'mshop/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.total-number-min' => 1 ) );

		$mock = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'searchItems' )
			->will( $this->returnValue( [] ) );

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\StandardMock', $mock );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableLimit()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'mshop/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.limit-days-pending' => 1 ) );

		$mock = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'searchItems' )
			->will( $this->returnValue( [] ) );

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\StandardMock', $mock );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->basket ) );
	}


	public function testIsAvailableLimitTooMuch()
	{
		$this->context->setUserId( 1 );
		$this->context->getConfig()->set( 'mshop/order/manager/name', 'StandardMock' );
		$this->servItem->setConfig( array( 'ordercheck.limit-days-pending' => 1 ) );

		$mock = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$mock->expects( $this->once() )
			->method( 'searchItems' )
			->will( $this->returnValue( array( $mock->createItem() ) ) );

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\StandardMock', $mock );

		$this->assertFalse( $this->object->isAvailable( $this->basket ) );
	}

}