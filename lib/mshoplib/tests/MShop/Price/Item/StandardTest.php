<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MShop\Price\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'price.id' => 199,
			'price.siteid' => 99,
			'price.type' => 'default',
			'price.currencyid' => 'EUR',
			'price.domain' => 'product',
			'price.label' => 'Price label',
			'price.quantity' => 15,
			'price.value' => '195.50',
			'price.costs' => '19.95',
			'price.rebate' => '10.00',
			'price.taxvalue' => '34.3995',
			'price.taxrates' => ['' => '19.00', 'local' => '5.00'],
			'price.taxflag' => true,
			'price.status' => true,
			'price.mtime' => '2011-01-01 00:00:02',
			'price.ctime' => '2011-01-01 00:00:01',
			'price.editor' => 'unitTestUser',
			'.currencyid' => 'EUR',
		);

		$this->object = new \Aimeos\MShop\Price\Item\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAddItem()
	{
		$price = new \Aimeos\MShop\Price\Item\Standard( $this->values );
		$return = $this->object->addItem( $price );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( '391.00', $this->object->getValue() );
		$this->assertEquals( '39.90', $this->object->getCosts() );
		$this->assertEquals( '20.00', $this->object->getRebate() );
		$this->assertEquals( '68.7990', $this->object->getTaxValue() );
		$this->assertEquals( 1, $this->object->getQuantity() );
	}


	public function testAddItemSelf()
	{
		$return = $this->object->addItem( $this->object );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( '391.00', $this->object->getValue() );
		$this->assertEquals( '39.90', $this->object->getCosts() );
		$this->assertEquals( '20.00', $this->object->getRebate() );
		$this->assertEquals( '68.7990', $this->object->getTaxValue() );
		$this->assertEquals( 1, $this->object->getQuantity() );
	}


	public function testAddItemWrongCurrency()
	{
		$values = $this->values;
		$values['price.currencyid'] = 'USD';

		$price = new \Aimeos\MShop\Price\Item\Standard( $values );

		$this->expectException( \Aimeos\MShop\Price\Exception::class );
		$this->object->addItem( $price );
	}


	public function testClear()
	{
		$result = $this->object->clear();

		$this->assertInstanceOf( 'Aimeos\MShop\Price\Item\Iface', $result );
		$this->assertEquals( '0.00', $this->object->getValue() );
		$this->assertEquals( '0.00', $this->object->getCosts() );
		$this->assertEquals( '0.00', $this->object->getRebate() );
		$this->assertEquals( '0.0000', $this->object->getTaxValue() );
		$this->assertEquals( true, $this->object->getTaxFlag() );
		$this->assertEquals( 1, $this->object->getQuantity() );
	}


	public function testCompare()
	{
		$price = new \Aimeos\MShop\Price\Item\Standard( $this->values );
		$this->assertTrue( $this->object->compare( $price ) );
	}


	public function testCompareFail()
	{
		$values = $this->values;
		$values['price.value'] = '200.00';

		$price = new \Aimeos\MShop\Price\Item\Standard( $values );
		$this->assertFalse( $this->object->compare( $price ) );
	}


	public function testGetPrecision()
	{
		$this->assertEquals( 2, $this->object->getPrecision() );
	}


	public function testGetId()
	{
		$this->assertEquals( 199, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'default', $this->object->getType() );
	}


	public function testSetType()
	{
		$this->object->setType( 'test' );
		$this->assertEquals( 'test', $this->object->getType() );

		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCurrencyId()
	{
		$this->assertEquals( 'EUR', $this->object->getCurrencyId() );
	}


	public function testSetCurrencyId()
	{
		$return = $this->object->setCurrencyId( 'USD' );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( 'USD', $this->object->getCurrencyId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetCurrencyIdInvalid()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setCurrencyId( 'usd' );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'product', $this->object->getDomain() );
	}


	public function testSetDomain()
	{
		$return = $this->object->setDomain( 'service' );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( 'service', $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'Price label', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'special price' );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( 'special price', $this->object->getlabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetQuantity()
	{
		$this->assertEquals( 15, $this->object->getQuantity() );
	}


	public function testSetQuantity()
	{
		$return = $this->object->setQuantity( 20 );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( 20, $this->object->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetValue()
	{
		$this->assertEquals( '195.50', $this->object->getValue() );
	}


	public function testSetValue()
	{
		$return = $this->object->setValue( 199.00 );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( 199.00, $this->object->getValue() );
		$this->assertTrue( $this->object->isModified() );

		$this->expectException( \Aimeos\MShop\Price\Exception::class );
		$this->object->setValue( '190,90' );
	}


	public function testSetValueEmpty()
	{
		$return = $this->object->setValue( '' );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertNull( $this->object->getValue() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetValueNull()
	{
		$return = $this->object->setValue( null );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertNull( $this->object->getValue() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCosts()
	{
		$this->assertEquals( '19.95', $this->object->getCosts() );
	}


	public function testSetCosts()
	{
		$return = $this->object->setValue( '20.00' );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( 20.00, $this->object->getValue() );
		$this->assertTrue( $this->object->isModified() );

		$this->expectException( \Aimeos\MShop\Price\Exception::class );
		$this->object->setValue( '19,90' );
	}


	public function testGetRebate()
	{
		$this->assertEquals( '10.00', $this->object->getRebate() );
	}


	public function testSetRebate()
	{
		$return = $this->object->setRebate( '20.00' );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( 20.00, $this->object->getRebate() );
		$this->assertTrue( $this->object->isModified() );

		$this->expectException( \Aimeos\MShop\Price\Exception::class );
		$this->object->setValue( '19,90' );
	}


	public function testGetTaxRate()
	{
		$this->assertEquals( '19.00', $this->object->getTaxRate() );
	}


	public function testGetTaxRates()
	{
		$this->assertEquals( ['' => '19.00', 'local' => '5.00'], $this->object->getTaxRates() );
	}


	public function testSetTaxRate()
	{
		$return = $this->object->setTaxRate( '22.00' );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( '22.00', $this->object->getTaxRate() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetTaxRates()
	{
		$value = ['' => '22.00', 'local' => '10.00'];
		$return = $this->object->setTaxRates( $value );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( ['' => '22.00', 'local' => '10.00'], $this->object->getTaxRates() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTaxFlag()
	{
		$this->assertEquals( true, $this->object->getTaxFlag() );
	}


	public function testSetTaxFlag()
	{
		$return = $this->object->setTaxFlag( false );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( false, $this->object->getTaxFlag() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTaxValue()
	{
		$this->assertEquals( '34.3995', $this->object->getTaxValue() );
	}


	public function testGetTaxValueFromNetprice()
	{
		$values = array(
			'price.quantity' => 10,
			'price.value' => 195.50,
			'price.costs' => 19.95,
			'price.taxrates' => ['' => '19.00', 'local' => '5.00'],
			'price.taxflag' => false,
		);

		$object = new \Aimeos\MShop\Price\Item\Standard( $values );
		$this->assertEquals( '51.7080', $object->getTaxValue() );


		$values['price.taxflag'] = true;

		$object = new \Aimeos\MShop\Price\Item\Standard( $values );
		$this->assertEquals( '41.7000', $object->getTaxValue() );
	}


	public function testSetTaxValue()
	{
		$return = $this->object->setTaxValue( '100.00' );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( '100.0000', $this->object->getTaxValue() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getStatus() );
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


	public function testGetResourceType()
	{
		$this->assertEquals( 'price', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Price\Item\Standard();

		$list = $entries = array(
			'price.id' => 1,
			'price.type' => 'test',
			'price.label' => 'test item',
			'price.currencyid' => 'EUR',
			'price.quantity' => 3,
			'price.value' => '10.00',
			'price.costs' => '5.00',
			'price.rebate' => '2.00',
			'price.taxvalue' => '3.0000',
			'price.taxrates' => ['' => '20.00'],
			'price.taxrate' => '20.00',
			'price.taxflag' => false,
			'price.status' => 0,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['price.id'], $item->getId() );
		$this->assertEquals( $list['price.type'], $item->getType() );
		$this->assertEquals( $list['price.label'], $item->getLabel() );
		$this->assertEquals( $list['price.currencyid'], $item->getCurrencyId() );
		$this->assertEquals( $list['price.quantity'], $item->getQuantity() );
		$this->assertEquals( $list['price.value'], $item->getValue() );
		$this->assertEquals( $list['price.costs'], $item->getCosts() );
		$this->assertEquals( $list['price.rebate'], $item->getRebate() );
		$this->assertEquals( $list['price.taxvalue'], $item->getTaxValue() );
		$this->assertEquals( $list['price.taxrates'], $item->getTaxRates() );
		$this->assertEquals( $list['price.taxrate'], $item->getTaxRate() );
		$this->assertEquals( $list['price.taxflag'], $item->getTaxFlag() );
		$this->assertEquals( $list['price.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['price.id'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['price.type'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['price.siteid'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['price.label'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['price.domain'] );
		$this->assertEquals( $this->object->getCurrencyId(), $arrayObject['price.currencyid'] );
		$this->assertEquals( $this->object->getQuantity(), $arrayObject['price.quantity'] );
		$this->assertEquals( $this->object->getValue(), $arrayObject['price.value'] );
		$this->assertEquals( $this->object->getCosts(), $arrayObject['price.costs'] );
		$this->assertEquals( $this->object->getRebate(), $arrayObject['price.rebate'] );
		$this->assertEquals( $this->object->getTaxValue(), $arrayObject['price.taxvalue'] );
		$this->assertEquals( $this->object->getTaxRates(), $arrayObject['price.taxrates'] );
		$this->assertEquals( $this->object->getTaxRate(), $arrayObject['price.taxrate'] );
		$this->assertEquals( $this->object->getTaxFlag(), $arrayObject['price.taxflag'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['price.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['price.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['price.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['price.editor'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setAvailable( false );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsAvailableOnStatus()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setStatus( 0 );
		$this->assertFalse( $this->object->isAvailable() );
		$this->object->setStatus( -1 );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
