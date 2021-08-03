<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Item\Base;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $locale;
	private $object;
	private $values;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();

		$this->values = array(
			'order.base.id' => 1,
			'order.base.siteid' => 99,
			'order.base.customerid' => 'testuser',
			'order.base.customerref' => 'ABC-1234',
			'order.base.comment' => 'this is a comment from unittest',
			'order.base.mtime' => '2011-01-01 00:00:02',
			'order.base.ctime' => '2011-01-01 00:00:01',
			'order.base.editor' => 'unitTestUser'
		);

		$price = \Aimeos\MShop\Price\Manager\Factory::create( $context )->create()->setValue( 0 );
		$this->locale = \Aimeos\MShop\Locale\Manager\Factory::create( $context )->create();
		$this->object = new \Aimeos\MShop\Order\Item\Base\Standard( $price, $this->locale, $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->locale, $this->object, $this->values );
	}


	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 5 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $return );
		$this->assertEquals( 5, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetCustomerId()
	{
		$this->assertEquals( 'testuser', $this->object->getCustomerId() );
	}


	public function testSetCustomerId()
	{
		$return = $this->object->setCustomerId( '44' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $return );
		$this->assertEquals( '44', $this->object->getCustomerId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCustomerReference()
	{
		$this->assertEquals( 'ABC-1234', $this->object->getCustomerReference() );
	}


	public function testSetCustomerReference()
	{
		$return = $this->object->setCustomerReference( 'XYZ-9876' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $return );
		$this->assertEquals( 'XYZ-9876', $this->object->getCustomerReference() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLocale()
	{
		$this->assertEquals( $this->locale, $this->object->getLocale() );
	}


	public function testSetLocale()
	{
		$locale = \Aimeos\MShop\Locale\Manager\Factory::create( \TestHelperMShop::getContext() )->create();
		$return = $this->object->setLocale( $locale );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $return );
		$this->assertEquals( $locale, $this->object->getLocale() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPrice()
	{
		$priceItem = $this->object->getPrice();

		$this->assertEquals( $priceItem->getCurrencyId(), 'EUR' );
		$this->assertEquals( $priceItem->getTaxRate(), '0.00' );
		$this->assertEquals( $priceItem->getRebate(), '0.00' );
		$this->assertEquals( $priceItem->getCosts(), '0.00' );
		$this->assertEquals( $priceItem->getValue(), '0.00' );
	}


	public function testGetComment()
	{
		$this->assertEquals( 'this is a comment from unittest', $this->object->getComment() );
	}


	public function testSetComment()
	{
		$return = $this->object->setComment( 'New unit test comment' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $return );
		$this->assertEquals( 'New unit test comment', $this->object->getComment() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Base\Standard( new \Aimeos\MShop\Price\Item\Standard(), new \Aimeos\MShop\Locale\Item\Standard() );

		$list = $entries = array(
			'order.base.id' => 1,
			'order.base.customerref' => 'testref',
			'order.base.comment' => 'test comment',
			'order.base.languageid' => 'de',
			'order.base.customerid' => 3,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['order.base.id'], $item->getId() );
		$this->assertEquals( $list['order.base.customerid'], $item->getCustomerId() );
		$this->assertEquals( $list['order.base.languageid'], $item->getLocale()->getLanguageId() );
		$this->assertEquals( $list['order.base.customerref'], $item->getCustomerReference() );
		$this->assertEquals( $list['order.base.comment'], $item->getComment() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );
		$price = $this->object->getPrice();

		$this->assertEquals( $this->object->getId(), $list['order.base.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['order.base.siteid'] );
		$this->assertEquals( $this->object->getCustomerId(), $list['order.base.customerid'] );
		$this->assertEquals( $this->object->getLocale()->getLanguageId(), $list['order.base.languageid'] );
		$this->assertEquals( $this->object->getCustomerReference(), $list['order.base.customerref'] );
		$this->assertEquals( $this->object->getComment(), $list['order.base.comment'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['order.base.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['order.base.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $list['order.base.editor'] );

		$this->assertEquals( $price->getValue(), $list['order.base.price'] );
		$this->assertEquals( $price->getCosts(), $list['order.base.costs'] );
		$this->assertEquals( $price->getRebate(), $list['order.base.rebate'] );
		$this->assertEquals( $price->getCurrencyId(), $list['order.base.currencyid'] );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testSetModified()
	{
		$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Base\Iface', $this->object->setModified() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testFinish()
	{
		$return = $this->object->finish();

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $return );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'order/base', $this->object->getResourceType() );
	}


	public function testSerialize()
	{
		$this->assertTrue( is_string( serialize( $this->object ) ) );
	}
}
