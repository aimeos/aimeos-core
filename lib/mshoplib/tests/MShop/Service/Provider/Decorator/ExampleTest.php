<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Test class for \Aimeos\MShop\Service\Provider\Decorator\Example.
 */
class ExampleTest extends \PHPUnit_Framework_TestCase
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
			throw new \RuntimeException( 'No order base item found' );
		}

		$item->setConfig( array( 'default.project' => '8502_TEST' ) );

		$serviceProvider = $servManager->getProvider( $item );
		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Example( $serviceProvider, $context, $item );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetConfigBE()
	{
		$this->assertArrayHasKey( 'country', $this->object->getConfigBE() );
		$this->assertArrayHasKey( 'default.url', $this->object->getConfigBE() );
	}


	public function testCheckConfigBE()
	{
		$attributes = array( 'country' => 'DE', 'default.project' => 'Unit', 'default.url' => 'http://unittest.com' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 6, count( $result ) );
		$this->assertInternalType( 'null', $result['country'] );
		$this->assertInternalType( 'null', $result['default.project'] );
		$this->assertInternalType( 'null', $result['default.username'] );
		$this->assertInternalType( 'null', $result['default.password'] );
		$this->assertInternalType( 'null', $result['default.url'] );
		$this->assertInternalType( 'null', $result['default.ssl'] );


		$attributes = array( 'country' => '', 'default.project' => 'Unit', 'default.url' => 'http://unittest.com' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 6, count( $result ) );
		$this->assertInternalType( 'string', $result['country'] );
		$this->assertInternalType( 'null', $result['default.project'] );
		$this->assertInternalType( 'null', $result['default.username'] );
		$this->assertInternalType( 'null', $result['default.password'] );
		$this->assertInternalType( 'null', $result['default.url'] );
		$this->assertInternalType( 'null', $result['default.ssl'] );
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
		$orderBaseManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() )->getSubManager( 'base' );
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


	public function testCall()
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$criteria = $orderManager->createSearch();
		$expr = array(
			$criteria->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_WEB ),
			$criteria->compare( '==', 'order.statuspayment', '6' )
		);

		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$criteria->setSlice( 0, 1 );
		$items = $orderManager->searchItems( $criteria );

		if( ( $order = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No order item available for order statuspayment "%1s" and "%2s"', '6', 'web' ) );
		}

		$this->object->buildXML( $order );
	}
}