<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
			'id' => 66,
			'siteid'=>99,
			'prodid' => 46677,
			'warehouseid' => 44,
			'stocklevel' => 1000,
			'backdate' => '2010-01-01 11:55:00',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
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
		$this->assertEquals( $this->values['id'], $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertTrue( $this->object->isModified() );

		$this->assertNull( $this->object->getId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetProductId()
	{
		$this->assertEquals( $this->values['prodid'], $this->object->getProductId() );
	}

	public function testSetProductId()
	{
		$this->object->setProductId( 10000 );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 10000, $this->object->getProductId() );
	}

	public function testGetWarehouseId()
	{
		$this->assertEquals( $this->values['warehouseid'], $this->object->getWarehouseId() );
	}

	public function testSetWarehouseId()
	{
		$this->object->setWarehouseId( 30000 );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 30000, $this->object->getWarehouseId() );
	}

	public function testGetStocklevel()
	{
		$this->assertEquals( $this->values['stocklevel'], $this->object->getStocklevel() );
	}

	public function testSetStocklevel()
	{
		$this->object->setStocklevel( 200 );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 200, $this->object->getStocklevel() );
	}

	public function testGetDateBack()
	{
		$this->assertEquals( $this->values['backdate'], $this->object->getDateBack() );
	}

	public function testSetDateBack()
	{
		$this->object->setDateBack( '2010-10-10 01:10:00' );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( '2010-10-10 01:10:00', $this->object->getDateBack() );
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
		$item = new \Aimeos\MShop\Product\Item\Stock\Standard();

		$list = array(
			'product.stock.id' => 1,
			'product.stock.productid' => 2,
			'product.stock.warehouseid' => 3,
			'product.stock.stocklevel' => 10,
			'product.stock.dateback' => '2000-01-01 00:00:00',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['product.stock.id'], $item->getId() );
		$this->assertEquals( $list['product.stock.productid'], $item->getProductId() );
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
		$this->assertEquals( $this->object->getProductId(), $arrayObject['product.stock.productid'] );
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
