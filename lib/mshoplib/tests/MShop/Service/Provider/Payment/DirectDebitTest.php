<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Service\Provider\Payment;


class DirectDebitTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $ordServItem;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();

		$this->ordServItem = \Aimeos\MShop::create( $context, 'order/base/service' )->createItem();
		$serviceItem = \Aimeos\MShop::create( $context, 'service' )->createItem();
		$serviceItem->setCode( 'test' );

		$this->object = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Payment\DirectDebit::class )
			->setMethods( array( 'getOrder', 'getOrderBase', 'saveOrder', 'saveOrderBase' ) )
			->setConstructorArgs( array( $context, $serviceItem ) )
			->getMock();
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetConfigBE()
	{
		$this->assertEquals( 0, count( $this->object->getConfigBE() ) );
	}


	public function testCheckConfigBE()
	{
		$result = $this->object->checkConfigBE( array( 'payment.url-success' => 'http://returnUrl' ) );

		$this->assertEquals( 0, count( $result ) );
	}


	public function testGetConfigFE()
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$search = $orderManager->createSearch();
		$expr = array(
			$search->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_WEB ),
			$search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$orderItems = $orderManager->searchItems( $search )->toArray();

		if( ( $order = reset( $orderItems ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No Order found with statuspayment "%1$s" and type "%2$s"', \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, \Aimeos\MShop\Order\Item\Base::TYPE_WEB ) );
		}

		$basket = $orderBaseManager->load( $order->getBaseId() );

		$config = $this->object->getConfigFE( $basket );

		$this->assertArrayHasKey( 'directdebit.accountowner', $config );
		$this->assertArrayHasKey( 'directdebit.accountno', $config );
		$this->assertArrayHasKey( 'directdebit.bankcode', $config );
		$this->assertArrayHasKey( 'directdebit.bankname', $config );
		$this->assertEquals( 'Our Unittest', $config['directdebit.accountowner']->getDefault() );
	}


	public function testCheckConfigFE()
	{
		$config = array(
			'directdebit.accountowner' => 'test user',
			'directdebit.accountno' => '123456789',
			'directdebit.bankcode' => '1000000',
			'directdebit.bankname' => 'Federal reserve',
		);

		$result = $this->object->checkConfigFE( $config );

		$expected = array(
			'directdebit.accountowner' => null,
			'directdebit.accountno' => null,
			'directdebit.bankcode' => null,
			'directdebit.bankname' => null,
		);

		$this->assertEquals( $expected, $result );
	}


	public function testCheckConfigFEwrongType()
	{
		$config = array(
			'directdebit.accountowner' => 123,
			'directdebit.accountno' => 0.1,
			'directdebit.bankcode' => '1000000',
			'directdebit.bankname' => 'Federal reserve',
		);

		$result = $this->object->checkConfigFE( $config );

		$this->assertArrayHasKey( 'directdebit.accountowner', $result );
		$this->assertArrayHasKey( 'directdebit.accountno', $result );

		$this->assertFalse( $result['directdebit.accountowner'] === null );
		$this->assertFalse( $result['directdebit.accountno'] === null );
		$this->assertTrue( $result['directdebit.bankcode'] === null );
		$this->assertTrue( $result['directdebit.bankname'] === null );
	}


	public function testSetConfigFE()
	{
		$this->object->setConfigFE( $this->ordServItem, array( 'directdebit.accountno' => '123456' ) );

		$attrItem = $this->ordServItem->getAttributeItem( 'directdebit.accountno', 'payment' );
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface::class, $attrItem );
		$this->assertEquals( 'XXX456', $attrItem->getValue() );

		$attrItem = $this->ordServItem->getAttributeItem( 'directdebit.accountno', 'payment/hidden' );
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface::class, $attrItem );
		$this->assertEquals( '123456', $attrItem->getValue() );
	}


	public function testUpdateSync()
	{
		$orderItem = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() )->createItem();
		$request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();

		$this->object->expects( $this->once() )->method( 'saveOrder' )->will( $this->returnArgument( 0 ) );

		$result = $this->object->updateSync( $request, $orderItem );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $result );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_AUTHORIZED, $result->getPaymentStatus() );
	}


	public function testIsImplemented()
	{
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_QUERY ) );
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CAPTURE ) );
		$this->assertFalse( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_CANCEL ) );
	}
}
