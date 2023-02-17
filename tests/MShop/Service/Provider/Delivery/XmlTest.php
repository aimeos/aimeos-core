<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2023
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


class XmlTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		file_exists( 'tmp' ) ?: mkdir( 'tmp' );

		$this->context = \TestHelper::context();
		$serviceManager = \Aimeos\MShop::create( $this->context, 'service' );
		$serviceItem = $serviceManager->create()->setConfig( [
			'xml.exportpath' => 'tmp/order-export_%d.xml',
			'xml.updatedir' => __DIR__ . '/_tests',
		] );

		$this->object = new \Aimeos\MShop\Service\Provider\Delivery\Xml( $this->context, $serviceItem );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertEquals( 4, count( $result ) );

		foreach( $result as $key => $item ) {
			$this->assertInstanceOf( 'Aimeos\Base\Criteria\Attribute\Iface', $item );
		}
	}


	public function testCheckConfigBE()
	{
		$attributes = [
			'xml.backupdir' => '/backup',
			'xml.exportpath' => 'order-%H:%i:%s.xml',
			'xml.template' => 'body.xml',
			'xml.updatedir' => '/',
		];

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertEquals( null, $result['xml.backupdir'] );
		$this->assertEquals( null, $result['xml.exportpath'] );
		$this->assertEquals( null, $result['xml.template'] );
		$this->assertEquals( null, $result['xml.updatedir'] );
	}


	public function testPush()
	{
		$orders = $this->object->push( [$this->getOrderItem()] );
		$file = 'tmp/order-export_' . date( 'd' ) . '.xml';
		$xml = simplexml_load_file( $file );
		unlink( $file );

		$this->assertEquals( 1, count( $orders ) );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, $orders->getStatusDelivery()->first() );
		$this->assertEquals( '2008-02-15 12:34:56', (string) $xml->orderitem[0]->{'order.datepayment'} );
		$this->assertEquals( 'unittest', (string) $xml->orderitem[0]->{'order.sitecode'} );
		$this->assertEquals( 'payment', (string) $xml->orderitem[0]->address->addressitem[0]['type'] );
		$this->assertEquals( 0, (string) $xml->orderitem[0]->address->addressitem[0]['position'] );
		$this->assertEquals( 1, (string) $xml->orderitem[0]->product->productitem[0]['position'] );
		$this->assertEquals( 3, (string) $xml->orderitem[0]->product->productitem[0]->attribute->attributeitem->count() );
		$this->assertEquals( 'payment', (string) $xml->orderitem[0]->service->serviceitem[0]['type'] );
		$this->assertEquals( 0, (string) $xml->orderitem[0]->service->serviceitem[0]['position'] );
		$this->assertEquals( 9, (string) $xml->orderitem[0]->service->serviceitem[0]->attribute->attributeitem->count() );
		$this->assertEquals( 2, (string) $xml->orderitem[0]->coupon->couponitem->count() );
	}


	public function testUpdateAsync()
	{
		\Aimeos\MShop::cache( true );

		$price = \Aimeos\MShop::create( $this->context, 'price' )->create();
		$locale = \Aimeos\MShop::create( $this->context, 'locale' )->create();

		$itemMock = $this->getMockBuilder( \Aimeos\MShop\Order\Item\Standard::class )
			->onlyMethods( ['setStatusDelivery', 'setStatusPayment', 'setDateDelivery', 'setDatePayment'] )
			->setConstructorArgs( [$price, $locale, []] )
			->getMock();

		$itemMock->expects( $this->once() )->method( 'setStatusDelivery' )->will( $this->returnSelf() );
		$itemMock->expects( $this->once() )->method( 'setStatusPayment' )->will( $this->returnSelf() );
		$itemMock->expects( $this->once() )->method( 'setDateDelivery' )->will( $this->returnSelf() );
		$itemMock->expects( $this->once() )->method( 'setDatePayment' )->will( $this->returnSelf() );

		$mock = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->onlyMethods( ['save', 'search'] )
			->setConstructorArgs( [$this->context] )
			->getMock();

		$mock->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( ['123' => $itemMock] ) ) );

		$mock->expects( $this->once() )->method( 'save' );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Standard::class, $mock );

		$this->object->updateAsync();
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
