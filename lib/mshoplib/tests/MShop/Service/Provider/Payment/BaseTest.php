<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Service\Provider\Payment;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$search = $servManager->filter();
		$search->setConditions( $search->compare( '==', 'service.provider', 'Standard' ) );
		$result = $servManager->search( $search, array( 'price' ) )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order base item found' );
		}

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
		$item = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )->create();

		$this->expectException( \Aimeos\MShop\Service\Exception::class );
		$this->object->cancel( $item );
	}


	public function testCapture()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )->create();

		$this->expectException( \Aimeos\MShop\Service\Exception::class );
		$this->object->capture( $item );
	}

	public function testProcess()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )->create();
		$this->object->injectGlobalConfigBE( ['payment.url-success' => 'url'] );

		$result = $this->object->process( $item, [] );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Helper\Form\Iface::class, $result );
	}


	public function testRefund()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )->create();

		$this->expectException( \Aimeos\MShop\Service\Exception::class );
		$this->object->refund( $item );
	}


	public function testRepay()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )->create();

		$this->expectException( \Aimeos\MShop\Service\Exception::class );
		$this->object->repay( $item );
	}


	public function testSetConfigFE()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )
			->getSubManager( 'base' )->getSubManager( 'service' )->create();

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $this->object->setConfigFE( $item, [] ) );
	}


	public function testTransfer()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )->create();

		$this->expectException( \Aimeos\MShop\Service\Exception::class );
		$this->object->transfer( $item );
	}
}


class TestBase
	extends \Aimeos\MShop\Service\Provider\Payment\Base
	implements \Aimeos\MShop\Service\Provider\Payment\Iface
{

}
