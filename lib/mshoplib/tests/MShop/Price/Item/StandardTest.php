<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

namespace Aimeos\MShop\Price\Item;


/**
 * Test class for \Aimeos\MShop\Price\Item\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->values = array(
			'id' => 199,
			'siteid'=>99,
			'typeid' => 2,
			'type' => 'default',
			'currencyid' => 'EUR',
			'domain' => 'product',
			'label' => 'Price label',
			'quantity' => 1500,
			'value' => 195.50,
			'costs' => 19.95,
			'rebate' => 10.00,
			'taxrate' => 19.00,
			'status' => true,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Price\Item\Standard( $this->values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}

	public function testAddItem()
	{
		$price = new \Aimeos\MShop\Price\Item\Standard( $this->values );
		$this->object->addItem( $price );

		$this->assertEquals( '391.00', $this->object->getValue() );
		$this->assertEquals( '39.90', $this->object->getCosts() );
		$this->assertEquals( '20.00', $this->object->getRebate() );
	}

	public function testAddItemWrongCurrency()
	{
		$values = $this->values;
		$values['currencyid'] = 'USD';

		$price = new \Aimeos\MShop\Price\Item\Standard( $values );

		$this->setExpectedException( '\\Aimeos\\MShop\\Price\\Exception' );
		$this->object->addItem( $price );
	}

	public function testCompare()
	{
		$price = new \Aimeos\MShop\Price\Item\Standard( $this->values );
		$this->assertTrue( $this->object->compare( $price ) );
	}

	public function testCompareFail()
	{
		$values = $this->values;
		$values['value'] = '200.00';

		$price = new \Aimeos\MShop\Price\Item\Standard( $values );
		$this->assertFalse( $this->object->compare( $price ) );
	}

	public function testGetId()
	{
		$this->assertEquals( 199, $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
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

	public function testGetTypeId()
	{
		$this->assertEquals( 2, $this->object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$this->object->setTypeId( 99 );
		$this->assertEquals( 99, $this->object->getTypeId() );

		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetCurrencyId()
	{
		$this->assertEquals( 'EUR', $this->object->getCurrencyId() );
	}

	public function testSetCurrencyId()
	{
		$this->object->setCurrencyId( 'USD' );
		$this->assertEquals( 'USD', $this->object->getCurrencyId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testSetCurrencyIdNull()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setCurrencyId( null );
	}

	public function testSetCurrencyIdInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setCurrencyId( 'usd' );
	}

	public function testGetDomain()
	{
		$this->assertEquals( 'product', $this->object->getDomain() );
	}

	public function testSetDomain()
	{
		$this->object->setDomain( 'service' );
		$this->assertEquals( 'service', $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'Price label', $this->object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->object->setLabel( 'special price' );
		$this->assertEquals( 'special price', $this->object->getlabel() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetQuantity()
	{
		$this->assertEquals( 1500, $this->object->getQuantity() );
	}

	public function testSetQuantity()
	{
		$this->object->setQuantity( 2000 );
		$this->assertEquals( 2000, $this->object->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetPrice()
	{
		$this->assertEquals( '195.50', $this->object->getValue() );
	}

	public function testSetPrice()
	{
		$this->object->setValue( 199.00 );
		$this->assertEquals( 199.00, $this->object->getValue() );
		$this->assertTrue( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Price\\Exception' );
		$this->object->setValue( '190,90' );
	}

	public function testGetCosts()
	{
		$this->assertEquals( '19.95', $this->object->getCosts() );
	}

	public function testSetCosts()
	{
		$this->object->setValue( '20.00' );
		$this->assertEquals( 20.00, $this->object->getValue() );
		$this->assertTrue( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Price\\Exception' );
		$this->object->setValue( '19,90' );
	}

	public function testGetRebate()
	{
		$this->assertEquals( '10.00', $this->object->getRebate() );
	}

	public function testSetRebate()
	{
		$this->object->setRebate( '20.00' );
		$this->assertEquals( 20.00, $this->object->getRebate() );
		$this->assertTrue( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Price\\Exception' );
		$this->object->setValue( '19,90' );
	}

	public function testgetTaxRate()
	{
		$this->assertEquals( '19.00', $this->object->getTaxRate() );
	}

	public function testsetTaxRate()
	{
		$this->object->setTaxRate( '22.00' );
		$this->assertEquals( 22.00, $this->object->getTaxRate() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->object->setStatus( 0 );
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


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Price\Item\Standard();

		$list = array(
			'price.id' => 1,
			'price.typeid' => 2,
			'price.label' => 'test item',
			'price.currencyid' => 'EUR',
			'price.quantity' => 3,
			'price.value' => '10.00',
			'price.costs' => '5.00',
			'price.rebate' => '2.00',
			'price.taxrate' => '20.00',
			'price.status' => 0,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['price.id'], $item->getId() );
		$this->assertEquals( $list['price.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['price.label'], $item->getLabel() );
		$this->assertEquals( $list['price.currencyid'], $item->getCurrencyId() );
		$this->assertEquals( $list['price.quantity'], $item->getQuantity() );
		$this->assertEquals( $list['price.value'], $item->getValue() );
		$this->assertEquals( $list['price.costs'], $item->getCosts() );
		$this->assertEquals( $list['price.rebate'], $item->getRebate() );
		$this->assertEquals( $list['price.taxrate'], $item->getTaxrate() );
		$this->assertEquals( $list['price.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['price.id'] );
		$this->assertEquals( $this->object->getTypeId(), $arrayObject['price.typeid'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['price.siteid'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['price.label'] );
		$this->assertEquals( $this->object->getCurrencyId(), $arrayObject['price.currencyid'] );
		$this->assertEquals( $this->object->getQuantity(), $arrayObject['price.quantity'] );
		$this->assertEquals( $this->object->getValue(), $arrayObject['price.value'] );
		$this->assertEquals( $this->object->getCosts(), $arrayObject['price.costs'] );
		$this->assertEquals( $this->object->getRebate(), $arrayObject['price.rebate'] );
		$this->assertEquals( $this->object->getTaxrate(), $arrayObject['price.taxrate'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['price.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['price.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['price.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['price.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
