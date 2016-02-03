<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Product\Item\Stock;


/**
 * Test class for \Aimeos\MShop\Product\Item\Stock\Standard.
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
			'product.stock.id' => 66,
			'product.stock.siteid' => 99,
			'product.stock.parentid' => 46677,
			'product.stock.warehouseid' => 44,
			'product.stock.stocklevel' => 1000,
			'product.stock.backdate' => '2010-01-01 11:55:00',
			'product.stock.mtime' => '2011-01-01 00:00:02',
			'product.stock.ctime' => '2011-01-01 00:00:01',
			'product.stock.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Product\Item\Stock\Standard( $this->values );
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

	public function testGetId()
	{
		$this->assertEquals( 66, $this->object->getId() );
	}

	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Stock\Iface', $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetParentId()
	{
		$this->assertEquals( 46677, $this->object->getParentId() );
	}

	public function testSetParentId()
	{
		$return = $this->object->setParentId( 10000 );

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Stock\Iface', $return );
		$this->assertEquals( 10000, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetWarehouseId()
	{
		$this->assertEquals( 44, $this->object->getWarehouseId() );
	}

	public function testSetWarehouseId()
	{
		$return = $this->object->setWarehouseId( 30000 );

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Stock\Iface', $return );
		$this->assertEquals( 30000, $this->object->getWarehouseId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetStocklevel()
	{
		$this->assertEquals( 1000, $this->object->getStocklevel() );
	}

	public function testSetStocklevel()
	{
		$return = $this->object->setStocklevel( 200 );

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Stock\Iface', $return );
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

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Stock\Iface', $return );
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
		$this->assertEquals( 'product/stock', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Product\Item\Stock\Standard();

		$list = array(
			'product.stock.id' => 1,
			'product.stock.parentid' => 2,
			'product.stock.warehouseid' => 3,
			'product.stock.stocklevel' => 10,
			'product.stock.dateback' => '2000-01-01 00:00:00',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['product.stock.id'], $item->getId() );
		$this->assertEquals( $list['product.stock.parentid'], $item->getParentId() );
		$this->assertEquals( $list['product.stock.warehouseid'], $item->getWarehouseId() );
		$this->assertEquals( $list['product.stock.stocklevel'], $item->getStocklevel() );
		$this->assertEquals( $list['product.stock.dateback'], $item->getDateBack() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['product.stock.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['product.stock.siteid'] );
		$this->assertEquals( $this->object->getParentId(), $arrayObject['product.stock.parentid'] );
		$this->assertEquals( $this->object->getWarehouseId(), $arrayObject['product.stock.warehouseid'] );
		$this->assertEquals( $this->object->getStocklevel(), $arrayObject['product.stock.stocklevel'] );
		$this->assertEquals( $this->object->getDateBack(), $arrayObject['product.stock.dateback'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['product.stock.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['product.stock.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['product.stock.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
