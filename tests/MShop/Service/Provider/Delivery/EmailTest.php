<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


class EmailTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$serviceManager = \Aimeos\MShop::create( $this->context, 'service' );

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
			$this->assertInstanceOf( 'Aimeos\Base\Criteria\Attribute\Iface', $item );
		}
	}


	public function testPush()
	{
		$orders = $this->object->push( [$this->getOrderItem()] );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, $orders->getStatusDelivery()->first() );
	}


	protected function getOrderItem()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order' );

		$search = $manager->filter()->add( 'order.datepayment', '==', '2008-02-15 12:34:56' );
		$ref = ['order/address', 'order/coupon', 'order/product', 'order/service'];

		return $manager->search( $search, $ref )
			->first( new \RuntimeException( sprintf( 'No order item for payment date "%1$s" found', '2008-02-15 12:34:56' ) ) );
	}
}
