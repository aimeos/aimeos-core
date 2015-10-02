<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Service_Provider_Decorator_Example.
 */
class MShop_Service_Provider_Decorator_ExampleTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$context = TestHelper::getContext();

		$servManager = MShop_Service_Manager_Factory::createManager( $context );
		$search = $servManager->createSearch();
		$search->setConditions( $search->compare( '==', 'service.provider', 'Default' ) );
		$result = $servManager->searchItems( $search, array( 'price' ) );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order base item found' );
		}

		$item->setConfig( array( 'default.project' => '8502_TEST' ) );

		$serviceProvider = $servManager->getProvider( $item );
		$this->object = new MShop_Service_Provider_Decorator_Example( $context, $item, $serviceProvider );
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
		$orderBaseManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() )->getSubManager( 'base' );
		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', '672.00' ) );
		$result = $orderBaseManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order base item found' );
		}

		$price = $this->object->calcPrice( $item );

		$this->assertInstanceOf( 'MShop_Price_Item_Interface', $price );
		$this->assertEquals( $price->getValue(), '12.95' );

	}


	public function testIsAvailable()
	{
		$orderBaseManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() )->getSubManager( 'base' );
		$localeManager = MShop_Locale_Manager_Factory::createManager( TestHelper::getContext() );

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
		$this->assertFalse( $this->object->isImplemented( MShop_Service_Provider_Payment_Abstract::FEAT_QUERY ) );
	}


	public function testCall()
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$criteria = $orderManager->createSearch();
		$expr = array(
			$criteria->compare( '==', 'order.type', MShop_Order_Item_Abstract::TYPE_WEB ),
			$criteria->compare( '==', 'order.statuspayment', '6' )
		);

		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$criteria->setSlice( 0, 1 );
		$items = $orderManager->searchItems( $criteria );

		if( ( $order = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No order item available for order statuspayment "%1s" and "%2s"', '6', 'web' ) );
		}

		$this->object->buildXML( $order );
	}
}