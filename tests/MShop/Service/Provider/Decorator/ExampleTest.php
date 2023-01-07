<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class ExampleTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$context = \TestHelper::context();

		$servManager = \Aimeos\MShop::create( $context, 'service' );
		$search = $servManager->filter();
		$search->setConditions( $search->compare( '==', 'service.provider', 'Standard' ) );
		$result = $servManager->search( $search, array( 'price' ) )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No service item found' );
		}

		$serviceProvider = $servManager->getProvider( $item, $item->getType() );
		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Example( $serviceProvider, $context, $item );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetConfigBE()
	{
		$this->assertArrayHasKey( 'country', $this->object->getConfigBE() );
	}


	public function testCheckConfigBE()
	{
		$result = $this->object->checkConfigBE( ['country' => 'DE'] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNull( $result['country'] );

		$result = $this->object->checkConfigBE( ['country' => ''] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertIsString( $result['country'] );
	}


	public function testCalcPrice()
	{
		$orderManager = \Aimeos\MShop::create( \TestHelper::context(), 'order' );
		$search = $orderManager->filter();
		$search->setConditions( $search->compare( '==', 'order.price', '672.00' ) );
		$result = $orderManager->search( $search )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order item found' );
		}

		$price = $this->object->calcPrice( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $price );
		$this->assertEquals( '12.95', $price->getValue() );
	}


	public function testIsAvailable()
	{
		$orderManager = \Aimeos\MShop::create( \TestHelper::context(), 'order' );
		$localeManager = \Aimeos\MShop::create( \TestHelper::context(), 'locale' );

		$localeItem = $localeManager->create();

		$orderDeItem = $orderManager->create();
		$localeItem->setLanguageId( 'de' );
		$orderDeItem->setLocale( $localeItem );

		$orderEnItem = $orderManager->create();
		$localeItem->setLanguageId( 'en' );
		$orderEnItem->setLocale( $localeItem );

		$this->assertFalse( $this->object->isAvailable( $orderDeItem ) );
		$this->assertTrue( $this->object->isAvailable( $orderEnItem ) );
	}

	public function testIsImplemented()
	{
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_QUERY ) );
	}
}
