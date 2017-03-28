<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class DownloadTest extends \PHPUnit_Framework_TestCase
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

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Download( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\StandardMock', null );
	}


	public function testGetConfigBE()
	{
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
		$this->assertInternalType( 'null', $result['download.all'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'download.all' => [] );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'string', $result['download.all'] );
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


	/**
	 * Returns an order base item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item
	 */
	protected function getOrderBaseItem()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'order' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order item found' );
		}

		$baseManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' );
		return $baseManager->load( $item->getBaseId() );
	}
}