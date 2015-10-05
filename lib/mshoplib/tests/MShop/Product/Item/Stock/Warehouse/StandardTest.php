<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MShop\Product\Item\Stock\Warehouse;


/**
 * Test class for \Aimeos\MShop\Product\Item\Stock\Warehouse\Standard.
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
			'id' => 44,
			'siteid'=>99,
			'code' => 'unit_label',
			'label' => 'label',
			'status' => 1,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Product\Item\Stock\Warehouse\Standard( $this->values );
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

	public function testGetCode()
	{
		$this->assertEquals( $this->values['code'], $this->object->getCode() );
	}

	public function testSetCode()
	{
		$this->object->setCode( 'test_label' );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 'test_label', $this->object->getCode() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( $this->values['label'], $this->object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->object->setLabel( 'label123' );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 'label123', $this->object->getLabel() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( $this->values['status'], $this->object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->object->setStatus( 0 );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 0, $this->object->getStatus() );
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
		$item = new \Aimeos\MShop\Product\Item\Stock\Warehouse\Standard();

		$list = array(
			'product.stock.warehouse.id' => 1,
			'product.stock.warehouse.code' => 'test',
			'product.stock.warehouse.label' => 'test item',
			'product.stock.warehouse.status' => 0,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['product.stock.warehouse.id'], $item->getId() );
		$this->assertEquals( $list['product.stock.warehouse.code'], $item->getCode() );
		$this->assertEquals( $list['product.stock.warehouse.label'], $item->getLabel() );
		$this->assertEquals( $list['product.stock.warehouse.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['product.stock.warehouse.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['product.stock.warehouse.siteid'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['product.stock.warehouse.code'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['product.stock.warehouse.label'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['product.stock.warehouse.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['product.stock.warehouse.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['product.stock.warehouse.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['product.stock.warehouse.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
