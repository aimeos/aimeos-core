<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Supplier\Item;


/**
 * Test class for \Aimeos\MShop\Supplier\Item\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$values = array(
			'supplier.id' => 541,
			'supplier.siteid' => 99,
			'supplier.label' => 'unitObject',
			'supplier.code' => 'unitCode',
			'supplier.status' => 4,
			'supplier.mtime' => '2011-01-01 00:00:02',
			'supplier.ctime' => '2011-01-01 00:00:01',
			'supplier.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Supplier\Item\Standard( $values );
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
		$this->assertEquals( 541, $this->object->getId() );
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

	public function testGetLabel()
	{
		$this->assertEquals( 'unitObject', $this->object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->object->setLabel( 'newName' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'newName', $this->object->getLabel() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'unitCode', $this->object->getCode() );
	}

	public function testSetCode()
	{
		$this->object->setCode( 'newCode' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'newCode', $this->object->getCode() );
	}



	public function testGetStatus()
	{
		$this->assertEquals( 4, $this->object->getStatus() );
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

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'supplier', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Supplier\Item\Standard();

		$list = array(
			'supplier.id' => 1,
			'supplier.code' => 'test',
			'supplier.label' => 'test item',
			'supplier.status' => 0,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['supplier.id'], $item->getId() );
		$this->assertEquals( $list['supplier.code'], $item->getCode() );
		$this->assertEquals( $list['supplier.label'], $item->getLabel() );
		$this->assertEquals( $list['supplier.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();

		$this->assertEquals( $this->object->getId(), $arrayObject['supplier.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['supplier.siteid'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['supplier.label'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['supplier.code'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['supplier.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['supplier.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['supplier.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['supplier.editor'] );
	}
}
