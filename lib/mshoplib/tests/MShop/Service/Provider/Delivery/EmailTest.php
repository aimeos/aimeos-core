<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


class EmailTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );

		$serviceItem = $serviceManager->create()->setConfig( [
			'email.from' => 'test@localhost',
			'email.to' => 'erp@localhost',
			'email.subject' => 'New orders',
		] );

		$this->object = new \Aimeos\MShop\Service\Provider\Delivery\Email( $this->context, $serviceItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testCheckConfigBE()
	{
		$attributes = [
			'email.from' => 'test@localhost',
			'email.to' => 'erp@localhost',
			'email.subject' => 'New orders',
			'email.template' => '/test.php',
			'email.order-template' => '/test-order.php',
		];

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 5, count( $result ) );
		$this->assertEquals( null, $result['email.from'] );
		$this->assertEquals( null, $result['email.to'] );
		$this->assertEquals( null, $result['email.subject'] );
		$this->assertEquals( null, $result['email.template'] );
		$this->assertEquals( null, $result['email.order-template'] );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertEquals( 5, count( $result ) );

		foreach( $result as $key => $item ) {
			$this->assertInstanceOf( 'Aimeos\MW\Criteria\Attribute\Iface', $item );
		}
	}


	public function testProcess()
	{
		$order = $this->object->process( $this->getOrderItem() );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, $order->getStatusDelivery() );
	}


	public function testProcessBatch()
	{
		$orders = $this->object->processBatch( [$this->getOrderItem()] );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, $orders->getStatusDelivery()->first() );
	}


	protected function getOrderItem()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );

		$result = $manager->search( $search )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No order item for payment date "%1$s" found', '2008-02-15 12:34:56' ) );
		}

		return $item;
	}
}
