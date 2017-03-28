<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Test class for \Aimeos\MShop\Service\Provider\Decorator\Weight.
 */
class WeightTest extends \PHPUnit_Framework_TestCase
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

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Weight( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\StandardMock', null );
	}


	public function testGetConfigBE()
	{
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
		$this->assertInternalType( 'null', $result['weight.min'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'weight.min' => [] );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'string', $result['weight.min'] );
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