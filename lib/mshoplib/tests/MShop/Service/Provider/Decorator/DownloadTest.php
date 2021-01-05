<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class DownloadTest extends \PHPUnit\Framework\TestCase
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

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Download( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\Aimeos\MShop\Order\Manager\StandardMock', null );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'download.all', $result );
	}


	public function testCheckConfigBEOK()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'download.all' => '1' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNull( $result['download.all'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'download.all' => [] );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertIsString( $result['download.all'] );
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
		$this->servItem->setConfig( array( 'download.all' => '0' ) );

		$this->mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isAvailable( $this->getOrderBaseItem() ) );
	}


	public function testIsAvailableFailure()
	{
		$this->servItem->setConfig( array( 'download.all' => '1' ) );

		$this->assertFalse( $this->object->isAvailable( $this->getOrderBaseItem() ) );
	}


	public function testIsAvailableFailureNoArticle()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order/base' );
		$this->servItem->setConfig( array( 'download.all' => '0' ) );

		$this->assertFalse( $this->object->isAvailable( $manager->create() ) );
	}


	/**
	 * Returns an order base item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item
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
