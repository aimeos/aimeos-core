<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class BasketLimitsTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $products;
	private $order;


	protected function setUp()
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners

		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );
		$search = $orderBaseProductManager->createSearch();

		$search->setConditions( $search->combine( '&&', array(
			$search->compare( '==', 'order.base.product.prodcode', array( 'CNE', 'CNC' ) ),
			$search->compare( '==', 'order.base.product.price', array( '600.00', '36.00' ) )
		) ) );
		$items = $orderBaseProductManager->searchItems( $search );

		if( count( $items ) < 2 ) {
			throw new \RuntimeException( 'Please fix the test data in your database.' );
		}

		foreach( $items as $item ) {
			$this->products[$item->getProductCode()] = $item;
		}

		$this->products['CNE']->setQuantity( 2 );
		$this->products['CNC']->setQuantity( 1 );

		$config = array(
			'min-value'=> array( 'EUR' => '75.00' ),
			'max-value'=> array( 'EUR' => '625.00' ),
			'min-products' => '2',
			'max-products' => 5
		);

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$plugin = $pluginManager->createItem();
		$plugin->setTypeId( 2 );
		$plugin->setProvider( 'BasketLimits' );
		$plugin->setConfig( $config );
		$plugin->setStatus( '1' );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\BasketLimits( \TestHelperMShop::getContext(), $plugin );
	}


	protected function tearDown()
	{
		unset( $this->object );
		unset( $this->order );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'min-products' => '10',
			'max-products' => '100',
			'min-value' => ['EUR' => '100.00'],
			'max-value' => ['EUR' => '1000.00'],
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertEquals( null, $result['min-products'] );
		$this->assertEquals( null, $result['max-products'] );
		$this->assertEquals( null, $result['min-value'] );
		$this->assertEquals( null, $result['max-value'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 4, count( $list ) );
		$this->assertArrayHasKey( 'min-products', $list );
		$this->assertArrayHasKey( 'max-products', $list );
		$this->assertArrayHasKey( 'min-value', $list );
		$this->assertArrayHasKey( 'max-value', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( '\Aimeos\MW\Criteria\Attribute\Iface', $entry );
		}
	}


	public function testRegister()
	{
		$this->object->register( $this->order );
	}


	public function testUpdate()
	{
		$this->products['CNE']->setQuantity( 4 );
		$this->order->addProduct( $this->products['CNE'] );

		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) );
	}


	public function testUpdateMinProductsFails()
	{
		$this->order->addProduct( $this->products['CNC'] );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
	}


	public function testUpdateMaxProductsFails()
	{
		$this->products['CNE']->setQuantity( 6 );
		$this->order->addProduct( $this->products['CNE'] );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
	}


	public function testUpdateMinValueFails()
	{
		$this->order->addProduct( $this->products['CNE'] );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
	}


	public function testUpdateMaxValueFails()
	{
		$this->products['CNC']->setQuantity( 2 );
		$this->order->addProduct( $this->products['CNC'] );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
	}
}
