<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class CouponTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();
		$plugin = \Aimeos\MShop::create( $context, 'plugin' )->createItem();
		$this->order = \Aimeos\MShop::create( $context, 'order/base' )->createItem()->off(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\Coupon( $context, $plugin );
	}


	protected function tearDown()
	{
		unset( $this->order, $this->object );
	}


	public function testRegister()
	{
		$this->object->register( $this->order );
	}


	public function testUpdate()
	{
		$this->order->addCoupon( 'OPQR' );
		$this->assertEquals( null, $this->object->update( $this->order, 'test' ) );
	}


	public function testUpdateInvalidObject()
	{
		$this->setExpectedException( \Aimeos\MW\Common\Exception::class );
		$this->object->update( new TestPublisher(), 'test' );
	}
}


class TestPublisher implements \Aimeos\MW\Observer\Publisher\Iface
{
	use \Aimeos\MW\Observer\Publisher\Traits;
}
