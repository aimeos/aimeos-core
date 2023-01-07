<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Service\Provider\Payment;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$servManager = \Aimeos\MShop::create( $this->context, 'service' );
		$search = $servManager->filter()->add( ['service.provider' => 'Standard'] );
		$item = $servManager->search( $search, ['price'] )->first( new \RuntimeException( 'No order base item found' ) );

		$this->object = new TestBase( $this->context, $item );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testCheckConfigBE()
	{
		$result = $this->object->checkConfigBE( array( 'payment.url-success' => true ) );

		$this->assertEquals( 0, count( $result ) );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertEquals( 0, count( $result ) );
		$this->assertIsArray( $result );
	}


	public function testCancel()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$this->assertSame( $item, $this->object->cancel( $item ) );
	}


	public function testCapture()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$this->assertSame( $item, $this->object->capture( $item ) );
	}

	public function testProcess()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$this->object->injectGlobalConfigBE( ['payment.url-success' => 'url'] );

		$result = $this->object->process( $item, [] );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Helper\Form\Iface::class, $result );
	}


	public function testRefund()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$this->assertSame( $item, $this->object->refund( $item ) );
	}


	public function testRepay()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$this->assertSame( $item, $this->object->repay( $item ) );
	}


	public function testSetConfigFE()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order/service' )->create();

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $this->object->setConfigFE( $item, [] ) );
	}


	public function testTransfer()
	{
		$item = \Aimeos\MShop::create( $this->context, 'order' )->create();
		$this->assertSame( $item, $this->object->transfer( $item ) );
	}
}


class TestBase
	extends \Aimeos\MShop\Service\Provider\Payment\Base
	implements \Aimeos\MShop\Service\Provider\Payment\Iface
{

}
