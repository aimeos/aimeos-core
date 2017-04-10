<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Stock\Item;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	protected function setUp()
	{
		$this->values = array(
			'stock.id' => 66,
			'stock.siteid' => 99,
			'stock.productcode' => 'CNC',
			'stock.typeid' => 44,
			'stock.type' => 'default',
			'stock.typename' => 'Standard',
			'stock.stocklevel' => 1000,
			'stock.backdate' => '2010-01-01 11:55:00',
			'stock.mtime' => '2011-01-01 00:00:02',
			'stock.ctime' => '2011-01-01 00:00:01',
			'stock.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Stock\Item\Standard( $this->values );
	}

	protected function tearDown()
	{
		$this->object = null;
	}

	public function testGetId()
	{
		$this->assertEquals( 66, $this->object->getId() );
	}

	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Stock\Item\Iface', $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetProductCode()
	{
		$this->assertEquals( 'CNC', $this->object->getProductCode() );
	}

	public function testSetProductCode()
	{
		$return = $this->object->setProductCode( 'CNE' );

		$this->assertInstanceOf( '\Aimeos\MShop\Stock\Item\Iface', $return );
		$this->assertEquals( 'CNE', $this->object->getProductCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 44, $this->object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$return = $this->object->setTypeId( 30000 );

		$this->assertInstanceOf( '\Aimeos\MShop\Stock\Item\Iface', $return );
		$this->assertEquals( 30000, $this->object->getTypeId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetStocklevel()
	{
		$this->assertEquals( 1000, $this->object->getStocklevel() );
	}

	public function testSetStocklevel()
	{
		$return = $this->object->setStocklevel( 200 );

		$this->assertInstanceOf( '\Aimeos\MShop\Stock\Item\Iface', $return );
		$this->assertEquals( 200, $this->object->getStocklevel() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testSetStocklevelNull()
	{
		$this->object->setStocklevel( null );
		$this->assertEquals( null, $this->object->getStocklevel() );

		$this->object->setStocklevel( '' );
		$this->assertEquals( null, $this->object->getStocklevel() );
	}

	public function testGetDateBack()
	{
		$this->assertEquals( '2010-01-01 11:55:00', $this->object->getDateBack() );
	}

	public function testSetDateBack()
	{
		$return = $this->object->setDateBack( '2010-10-10 01:10:00' );

		$this->assertInstanceOf( '\Aimeos\MShop\Stock\Item\Iface', $return );
		$this->assertEquals( '2010-10-10 01:10:00', $this->object->getDateBack() );
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
		$this->assertEquals( 'stock', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Stock\Item\Standard();

		$list = array(
			'stock.id' => 1,
			'stock.typeid' => 3,
			'stock.stocklevel' => 10,
			'stock.productcode' => 'CNC',
			'stock.dateback' => '2000-01-01 00:00:00',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['stock.id'], $item->getId() );
		$this->assertEquals( $list['stock.productcode'], $item->getProductCode() );
		$this->assertEquals( $list['stock.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['stock.stocklevel'], $item->getStocklevel() );
		$this->assertEquals( $list['stock.dateback'], $item->getDateBack() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['stock.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['stock.siteid'] );
		$this->assertEquals( $this->object->getProductCode(), $arrayObject['stock.productcode'] );
		$this->assertEquals( $this->object->getTypeId(), $arrayObject['stock.typeid'] );
		$this->assertEquals( $this->object->getStocklevel(), $arrayObject['stock.stocklevel'] );
		$this->assertEquals( $this->object->getDateBack(), $arrayObject['stock.dateback'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['stock.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['stock.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['stock.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
