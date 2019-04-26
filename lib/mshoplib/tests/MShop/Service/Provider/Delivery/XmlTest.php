<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


class XmlTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		file_exists( 'tmp' ) ?: mkdir( 'tmp' );

		$this->context = \TestHelperMShop::getContext();
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context );
		$serviceItem = $serviceManager->createItem()->setConfig( ['filepath' => 'tmp/order-export_%%d.xml'] );

		$this->object = new \Aimeos\MShop\Service\Provider\Delivery\Xml( $this->context, $serviceItem );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertEquals( 2, count( $result ) );

		foreach( $result as $key => $item ) {
			$this->assertInstanceOf( 'Aimeos\MW\Criteria\Attribute\Iface', $item );
		}
	}


	public function testCheckConfigBE()
	{
		$attributes = [
			'xml.filepath' => 'order-%T.xml',
			'xml.template' => 'body.xml',
		];

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( null, $result['xml.filepath'] );
		$this->assertEquals( null, $result['xml.template'] );
	}


	public function testProcess()
	{
		$order = $this->object->process( $this->getOrderItem() );
		$xml = simplexml_load_file( 'tmp/order-export_0.xml' );
		unlink( 'tmp/order-export_0.xml' );

		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, $order->getDeliveryStatus() );
		$this->assertEquals( '2008-02-15 12:34:56', $xml->orderitem[0]->invoice->{'order.datepayment'} );
		$this->assertEquals( 'unittest', $xml->orderitem[0]->base->{'order.base.sitecode'} );
		$this->assertEquals( 'payment', $xml->orderitem[0]->address->addressitem[0]['type'] );
		$this->assertEquals( 0, (string) $xml->orderitem[0]->address->addressitem[0]['position'] );
		$this->assertEquals( 1, (string) $xml->orderitem[0]->product->productitem[0]['position'] );
		$this->assertEquals( 3, $xml->orderitem[0]->product->productitem[0]->attribute->attributeitem->count() );
		$this->assertEquals( 'payment', $xml->orderitem[0]->service->serviceitem[0]['type'] );
		$this->assertEquals( 0, (string) $xml->orderitem[0]->service->serviceitem[0]['position'] );
		$this->assertEquals( 9, $xml->orderitem[0]->service->serviceitem[0]->attribute->attributeitem->count() );
		$this->assertEquals( 2, $xml->orderitem[0]->coupon->couponitem->count() );
	}


	public function testProcessBatch()
	{
		$orders = $this->object->processBatch( [$this->getOrderItem()] );
		$xml = simplexml_load_file( 'tmp/order-export_0.xml' );
		unlink( 'tmp/order-export_0.xml' );

		$this->assertEquals( 1, count( $orders ) );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, current( $orders )->getDeliveryStatus() );
		$this->assertEquals( '2008-02-15 12:34:56', $xml->orderitem[0]->invoice->{'order.datepayment'} );
		$this->assertEquals( 'unittest', $xml->orderitem[0]->base->{'order.base.sitecode'} );
		$this->assertEquals( 'payment', $xml->orderitem[0]->address->addressitem[0]['type'] );
		$this->assertEquals( 0, (string) $xml->orderitem[0]->address->addressitem[0]['position'] );
		$this->assertEquals( 1, (string) $xml->orderitem[0]->product->productitem[0]['position'] );
		$this->assertEquals( 3, $xml->orderitem[0]->product->productitem[0]->attribute->attributeitem->count() );
		$this->assertEquals( 'payment', $xml->orderitem[0]->service->serviceitem[0]['type'] );
		$this->assertEquals( 0, (string) $xml->orderitem[0]->service->serviceitem[0]['position'] );
		$this->assertEquals( 9, $xml->orderitem[0]->service->serviceitem[0]->attribute->attributeitem->count() );
		$this->assertEquals( 2, $xml->orderitem[0]->coupon->couponitem->count() );
	}


	protected function getOrderItem()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No order item for payment date "%1$s" found', '2008-02-15 12:34:56' ) );
		}

		return $item;
	}

}
