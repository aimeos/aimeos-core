<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


class ManualTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $context );
		$serviceItem = $serviceManager->createItem();

		$this->object = new \Aimeos\MShop\Service\Provider\Delivery\Manual( $context, $serviceItem );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetConfigBE()
	{
		$this->assertEquals( [], $this->object->getConfigBE() );
	}


	public function testGetConfigFE()
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$basket = $orderManager->getSubManager( 'base' )->createItem();

		$this->assertEquals( [], $this->object->getConfigFE( $basket ) );
	}


	public function testProcess()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$order = $manager->createItem();
		$this->object->process( $order );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, $order->getDeliveryStatus() );
	}


	public function testSetConfigFE()
	{
		$item = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'order/base/service' )->createItem();
		$this->object->setConfigFE( $item, array( 'test.code' => 'abc', 'test.number' => 123 ) );

		$this->assertEquals( 2, count( $item->getAttributes() ) );
		$this->assertEquals( 'abc', $item->getAttribute( 'test.code', 'delivery' ) );
		$this->assertEquals( 123, $item->getAttribute( 'test.number', 'delivery' ) );
		$this->assertEquals( 'delivery', $item->getAttributeItem( 'test.code', 'delivery' )->getType() );
		$this->assertEquals( 'delivery', $item->getAttributeItem( 'test.number', 'delivery' )->getType() );
	}

}
