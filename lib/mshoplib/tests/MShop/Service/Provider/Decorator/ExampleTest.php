<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class ExampleTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Service\Manager\Factory::create( $context );
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
		$orderBaseManager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() )->getSubManager( 'base' );
		$search = $orderBaseManager->filter();
		$search->setConditions( $search->compare( '==', 'order.base.price', '672.00' ) );
		$result = $orderBaseManager->search( $search )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order base item found' );
		}

		$price = $this->object->calcPrice( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $price );
		$this->assertEquals( '12.95', $price->getValue() );
	}


	public function testIsAvailable()
	{
		$orderBaseManager = \Aimeos\MShop::create( \TestHelperMShop::getContext(), 'order/base' );
		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::create( \TestHelperMShop::getContext() );

		$localeItem = $localeManager->create();

		$orderBaseDeItem = $orderBaseManager->create();
		$localeItem->setLanguageId( 'de' );
		$orderBaseDeItem->setLocale( $localeItem );

		$orderBaseEnItem = $orderBaseManager->create();
		$localeItem->setLanguageId( 'en' );
		$orderBaseEnItem->setLocale( $localeItem );

		$this->assertFalse( $this->object->isAvailable( $orderBaseDeItem ) );
		$this->assertTrue( $this->object->isAvailable( $orderBaseEnItem ) );
	}

	public function testIsImplemented()
	{
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_QUERY ) );
	}
}
