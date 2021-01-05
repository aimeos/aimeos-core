<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class WeightTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$this->servItem = $servManager->create();

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Example::class )
			->disableOriginalConstructor()->getMock();

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Weight( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\Aimeos\MShop\Order\Manager\StandardMock', null );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'weight.min', $result );
		$this->assertArrayHasKey( 'weight.max', $result );
	}


	public function testCheckConfigBEOK()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'weight.min' => '10.0' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['weight.min'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'weight.min' => [] );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['weight.min'] );
	}


	public function testIsAvailableNoConfig()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->getOrderBaseItem() ) );
	}


	public function testIsAvailableOK()
	{
		$this->servItem->setConfig( array( 'weight.min' => '12.75', 'weight.max' => '12.75' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->getOrderBaseItem() ) );
	}


	public function testIsAvailableMinFailure()
	{
		$this->servItem->setConfig( array( 'weight.min' => '15' ) );

		$this->assertFalse( $this->object->isAvailable( $this->getOrderBaseItem() ) );
	}


	public function testIsAvailableMaxFailure()
	{
		$this->servItem->setConfig( array( 'weight.max' => '10' ) );

		$this->assertFalse( $this->object->isAvailable( $this->getOrderBaseItem() ) );
	}


	/**
	 * @return \Aimeos\MShop\Order\Item\Base\Iface
	 */
	protected function getOrderBaseItem()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );
		$result = $manager->search( $search )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order item found' );
		}

		$baseManager = \Aimeos\MShop::create( $this->context, 'order/base' );
		return $baseManager->load( $item->getBaseId() );
	}
}
