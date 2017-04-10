<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Order\Item\Base;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $locale;
	private $object;
	private $values;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$this->values = array(
			'order.base.id' => 1,
			'order.base.siteid' => 99,
			'order.base.customerid' => 'testuser',
			'order.base.comment' => 'this is a comment from unittest',
			'order.base.status' => 0,
			'order.base.mtime' => '2011-01-01 00:00:02',
			'order.base.ctime' => '2011-01-01 00:00:01',
			'order.base.editor' => 'unitTestUser'
		);

		$price = \Aimeos\MShop\Price\Manager\Factory::createManager( $context )->createItem();
		$this->locale = \Aimeos\MShop\Locale\Manager\Factory::createManager( $context )->createItem();
		$this->object = new \Aimeos\MShop\Order\Item\Base\Standard( $price, $this->locale, $this->values );
	}


	protected function tearDown()
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

		$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Base\Iface', $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 5 );

		$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Base\Iface', $return );
		$this->assertEquals( 5, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setId( 6 );
	}


	public function testSetId2()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setId( 'test' );
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

		$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Base\Iface', $return );
		$this->assertEquals( '44', $this->object->getCustomerId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLocale()
	{
		$this->assertEquals( $this->locale, $this->object->getLocale() );
	}


	public function testSetLocale()
	{
		$locale = \Aimeos\MShop\Locale\Manager\Factory::createManager( \TestHelperMShop::getContext() )->createItem();
		$return = $this->object->setLocale( $locale );

		$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Base\Iface', $return );
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

		$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Base\Iface', $return );
		$this->assertEquals( 'New unit test comment', $this->object->getComment() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 0, $this->object->getStatus() );
	}

	public function testSetStatus()
	{
		$return = $this->object->setStatus( 1 );

		$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Base\Iface', $return );
		$this->assertEquals( 1, $this->object->getStatus() );
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

		$list = array(
			'order.base.id' => 1,
			'order.base.comment' => 'test comment',
			'order.base.languageid' => 'de',
			'order.base.customerid' => 3,
			'order.base.status' => 4,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['order.base.id'], $item->getId() );
		$this->assertEquals( $list['order.base.customerid'], $item->getCustomerId() );
		$this->assertEquals( $list['order.base.languageid'], $item->getLocale()->getLanguageId() );
		$this->assertEquals( $list['order.base.comment'], $item->getComment() );
		$this->assertEquals( $list['order.base.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );
		$price = $this->object->getPrice();

		$this->assertEquals( $this->object->getId(), $list['order.base.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['order.base.siteid'] );
		$this->assertEquals( $this->object->getCustomerId(), $list['order.base.customerid'] );
		$this->assertEquals( $this->object->getLocale()->getLanguageId(), $list['order.base.languageid'] );
		$this->assertEquals( $this->object->getComment(), $list['order.base.comment'] );
		$this->assertEquals( $this->object->getStatus(), $list['order.base.status'] );
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


	public function testFinish()
	{
		$return = $this->object->finish();

		$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Base\Iface', $return );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'order/base', $this->object->getResourceType() );
	}
}