<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Product_Item_Default.
 */
class MShop_Product_Item_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_values = array(
			'id' => 1,
			'siteid'=>99,
			'typeid' => 2,
			'type' => 'test',
			'status' => 0,
			'code' => 'TEST',
			'suppliercode' => 'unitSupplier',
			'label' => 'testproduct',
			'start' => null,
			'end' => null,
			'ctime' => '2011-01-19 17:04:32',
			'mtime' => '2011-01-19 18:04:32',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Product_Item_Default( $this->_values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
		unset( $this->textListItems );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}


	public function testGetTypeId()
	{
		$this->assertEquals( 2, $this->_object->getTypeId() );
	}


	public function testSetTypeId()
	{
		$this->assertFalse( $this->_object->isModified() );

		$this->_object->setTypeId( 1 );
		$this->assertEquals( 1, $this->_object->getTypeId() );

		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'test', $this->_object->getType() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'TEST', $this->_object->getCode() );
	}


	public function testSetCode()
	{
		$this->assertFalse( $this->_object->isModified() );

		$this->_object->setCode( 'NEU' );
		$this->assertEquals( 'NEU', $this->_object->getCode() );

		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}


	public function testGetSupplierCode()
	{
		$this->assertEquals( 'unitSupplier', $this->_object->getSupplierCode() );
	}


	public function testSetSupplierCode()
	{
		$this->assertFalse( $this->_object->isModified() );

		$this->_object->setSupplierCode( 'unitTestSupplier' );
		$this->assertEquals( 'unitTestSupplier', $this->_object->getSupplierCode() );

		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetId()
	{
		$this->assertEquals( '1', $this->_object->getId() );
	}


	public function testSetId()
	{
		$this->_object->setId( 1 );
		$this->assertEquals( '1', $this->_object->getId() );

		$this->setExpectedException( 'MShop_Exception' );

		$this->_object->setId( 2 );
		$this->assertEquals( '2', $this->_object->getId() );
		$this->assertTrue( $this->_object->isModified() );

		$this->_object->setId( null );
		$this->assertNull( $this->_object->getId() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 0, $this->_object->getStatus() );
	}


	public function testSetStatus()
	{
		$this->_object->setStatus( 8 );
		$this->assertEquals( 8, $this->_object->getStatus() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'testproduct', $this->_object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->_object->setLabel( 'editproduct' );
		$this->assertEquals( 'editproduct', $this->_object->getLabel() );
	}


	public function testGetDateStart()
	{
		$this->assertEquals( null, $this->_object->getDateStart() );
	}


	public function testSetDateStart()
	{
		$this->_object->setDateStart( '2010-04-22 06:22:22' );
		$this->assertEquals( '2010-04-22 06:22:22', $this->_object->getDateStart() );
	}


	public function testGetDateEnd()
	{
		$this->assertEquals( null, $this->_object->getDateEnd() );
	}


	public function testSetDateEnd()
	{
		$this->_object->setDateEnd( '2010-05-22 06:22:22' );
		$this->assertEquals( '2010-05-22 06:22:22', $this->_object->getDateEnd() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-19 18:04:32', $this->_object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-19 17:04:32', $this->_object->getTimeCreated() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}


	public function testIsModifiedTrue()
	{
		$this->_object->setLabel( 'reeditProduct' );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testFromArray()
	{
		$item = new MShop_Product_Item_Default();

		$list = array(
			'product.id' => 1,
			'product.typeid' => 2,
			'product.label' => 'test item',
			'product.code' => 'test',
			'product.suppliercode' => 'testsup',
			'product.datestart' => '2000-01-01 00:00:00',
			'product.dateend' => '2001-01-01 00:00:00',
			'product.status' => 0,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['product.id'], $item->getId() );
		$this->assertEquals( $list['product.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['product.code'], $item->getCode() );
		$this->assertEquals( $list['product.label'], $item->getLabel() );
		$this->assertEquals( $list['product.suppliercode'], $item->getSuppliercode() );
		$this->assertEquals( $list['product.datestart'], $item->getDateStart() );
		$this->assertEquals( $list['product.dateend'], $item->getDateEnd() );
		$this->assertEquals( $list['product.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['product.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['product.siteid'] );
		$this->assertEquals( $this->_object->getCode(), $arrayObject['product.code'] );
		$this->assertEquals( $this->_object->getTypeId(), $arrayObject['product.typeid'] );
		$this->assertEquals( $this->_object->getType(), $arrayObject['product.type'] );
		$this->assertEquals( $this->_object->getLabel(), $arrayObject['product.label'] );
		$this->assertEquals( $this->_object->getStatus(), $arrayObject['product.status'] );
		$this->assertEquals( $this->_object->getSuppliercode(), $arrayObject['product.suppliercode'] );
		$this->assertEquals( $this->_object->getDateStart(), $arrayObject['product.datestart'] );
		$this->assertEquals( $this->_object->getDateEnd(), $arrayObject['product.dateend'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['product.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['product.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['product.editor'] );
	}

}
