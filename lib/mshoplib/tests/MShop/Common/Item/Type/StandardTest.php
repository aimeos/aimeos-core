<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Common_Item_Type_Standard.
 */
class MShop_Common_Item_Type_StandardTest extends PHPUnit_Framework_TestCase
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
			'id'   => 1,
			'code' => 'code',
			'domain' => 'domain',
			'label' => 'label',
			'status' => 1,
			'siteid' => 1,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new MShop_Common_Item_Type_Standard( '', $this->values );
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
		$this->assertEquals( 1, $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertTrue( $this->object->isModified() );
		$this->assertNull( $this->object->getId() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'code', $this->object->getCode() );
	}

	public function testSetCode()
	{
		$this->object->setCode( 'code' );
		$this->assertFalse( $this->object->isModified() );
		$this->assertEquals( 'code', $this->object->getCode() );
	}

	public function testGetDomain()
	{
		$this->assertEquals( 'domain', $this->object->getDomain() );
	}

	public function testSetDomain()
	{
		$this->object->setDomain( 'domain' );
		$this->assertFalse( $this->object->isModified() );
		$this->assertEquals( 'domain', $this->object->getDomain() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'label', $this->object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->object->setLabel( 'label' );
		$this->assertFalse( $this->object->isModified() );
		$this->assertEquals( 'label', $this->object->getLabel() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->object->setStatus( 1 );
		$this->assertFalse( $this->object->isModified() );
		$this->assertEquals( 1, $this->object->getStatus() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 1, $this->object->getSiteId() );
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
		$item = new MShop_Common_Item_Type_Standard( 'common.type.' );

		$list = array(
			'common.type.id' => 8,
			'common.type.code' => 'test',
			'common.type.domain' => 'testDomain',
			'common.type.label' => 'test item',
			'common.type.status' => 1,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['common.type.id'], $item->getId() );
		$this->assertEquals( $list['common.type.code'], $item->getCode() );
		$this->assertEquals( $list['common.type.domain'], $item->getDomain() );
		$this->assertEquals( $list['common.type.label'], $item->getLabel() );
		$this->assertEquals( $list['common.type.status'], $item->getStatus() );
	}

	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['id'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['code'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['domain'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['label'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['status'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['siteid'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
