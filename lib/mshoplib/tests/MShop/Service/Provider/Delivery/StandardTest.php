<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$serviceItem = $serviceManager->create();

		$this->object = new \Aimeos\MShop\Service\Provider\Delivery\Standard( $this->context, $serviceItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetConfigBE()
	{
		$this->assertEquals( [], $this->object->getConfigBE() );
	}


	public function testGetConfigFE()
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$basket = $orderManager->getSubManager( 'base' )->create();

		$this->assertEquals( [], $this->object->getConfigFE( $basket ) );
	}


	public function testProcess()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$order = $manager->create();

		$result = $this->object->process( $order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PENDING, $result->getStatusDelivery() );
	}


	public function testProcessBatch()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$order = $manager->create();

		$result = $this->object->processBatch( [$order] );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PENDING, $result->getStatusDelivery()->first() );
	}


	public function testSetConfigFE()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order/base/service' )->create();
		$this->object->setConfigFE( $item, array( 'test.code' => 'abc', 'test.number' => 123 ) );

		$this->assertEquals( 2, count( $item->getAttributeItems() ) );
		$this->assertEquals( 'abc', $item->getAttribute( 'test.code', 'delivery' ) );
		$this->assertEquals( 123, $item->getAttribute( 'test.number', 'delivery' ) );
		$this->assertEquals( 'delivery', $item->getAttributeItem( 'test.code', 'delivery' )->getType() );
		$this->assertEquals( 'delivery', $item->getAttributeItem( 'test.number', 'delivery' )->getType() );
	}

}
