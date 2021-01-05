<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Stock\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'stock.id' => 66,
			'stock.siteid' => 99,
			'stock.productid' => '123',
			'stock.type' => 'default',
			'stock.stocklevel' => 1000,
			'stock.timeframe' => '2-3d',
			'stock.backdate' => '2010-01-01 11:55:00',
			'stock.mtime' => '2011-01-01 00:00:02',
			'stock.ctime' => '2011-01-01 00:00:01',
			'stock.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Stock\Item\Standard( $this->values );
	}


	protected function tearDown() : void
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

		$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetProductId()
	{
		$this->assertEquals( '123', $this->object->getProductId() );
	}


	public function testSetProductId()
	{
		$return = $this->object->setProductId( '456' );

		$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $return );
		$this->assertEquals( '456', $this->object->getProductId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'default', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $return );
		$this->assertEquals( 'test', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStockLevel()
	{
		$this->assertEquals( 1000, $this->object->getStockLevel() );
	}


	public function testSetStockLevel()
	{
		$return = $this->object->setStockLevel( 200 );

		$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $return );
		$this->assertEquals( 200, $this->object->getStockLevel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetStockLevelNull()
	{
		$this->object->setStockLevel( null );
		$this->assertEquals( null, $this->object->getStockLevel() );
		$this->assertTrue( $this->object->isModified() );

		$this->object->setStockLevel( '' );
		$this->assertEquals( null, $this->object->getStockLevel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDateBack()
	{
		$this->assertEquals( '2010-01-01 11:55:00', $this->object->getDateBack() );
	}


	public function testSetDateBack()
	{
		$return = $this->object->setDateBack( '2010-10-10 01:10:00' );

		$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $return );
		$this->assertEquals( '2010-10-10 01:10:00', $this->object->getDateBack() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeFrame()
	{
		$this->assertEquals( '2-3d', $this->object->getTimeFrame() );
	}


	public function testSetTimeFrame()
	{
		$return = $this->object->setTimeFrame( '1w' );

		$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $return );
		$this->assertEquals( '1w', $this->object->getTimeFrame() );
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

		$list = $entries = array(
			'stock.id' => 1,
			'stock.type' => 'default',
			'stock.stocklevel' => 10,
			'stock.timeframe' => '4-5d',
			'stock.productid' => '789',
			'stock.dateback' => '2000-01-01 00:00:00',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['stock.id'], $item->getId() );
		$this->assertEquals( $list['stock.type'], $item->getType() );
		$this->assertEquals( $list['stock.productid'], $item->getProductId() );
		$this->assertEquals( $list['stock.stocklevel'], $item->getStockLevel() );
		$this->assertEquals( $list['stock.timeframe'], $item->getTimeFrame() );
		$this->assertEquals( $list['stock.dateback'], $item->getDateBack() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['stock.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['stock.siteid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['stock.type'] );
		$this->assertEquals( $this->object->getProductId(), $arrayObject['stock.productid'] );
		$this->assertEquals( $this->object->getStockLevel(), $arrayObject['stock.stocklevel'] );
		$this->assertEquals( $this->object->getTimeFrame(), $arrayObject['stock.timeframe'] );
		$this->assertEquals( $this->object->getDateBack(), $arrayObject['stock.dateback'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['stock.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['stock.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['stock.editor'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
