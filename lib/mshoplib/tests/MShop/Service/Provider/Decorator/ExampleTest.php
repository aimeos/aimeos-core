<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class ExampleTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $context );
		$search = $servManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.provider', 'Standard' ) );
		$result = $servManager->searchItems( $search, array( 'price' ) );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No service item found' );
		}

		$serviceProvider = $servManager->getProvider( $item, $item->getType() );
		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Example( $serviceProvider, $context, $item );
	}


	protected function tearDown()
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
		$this->assertInternalType( 'null', $result['country'] );

		$result = $this->object->checkConfigBE( ['country' => ''] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'string', $result['country'] );
	}


	public function testCalcPrice()
	{
		$orderBaseManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() )->getSubManager( 'base' );
		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', '672.00' ) );
		$result = $orderBaseManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order base item found' );
		}

		$price = $this->object->calcPrice( $item );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Price\\Item\\Iface', $price );
		$this->assertEquals( $price->getValue(), '12.95' );

	}


	public function testIsAvailable()
	{
		$orderBaseManager = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'order/base' );
		$localeManager = \Aimeos\MShop\Locale\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$localeItem = $localeManager->createItem();

		$orderBaseDeItem = $orderBaseManager->createItem();
		$localeItem->setLanguageId( 'de' );
		$orderBaseDeItem->setLocale( $localeItem );

		$orderBaseEnItem = $orderBaseManager->createItem();
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