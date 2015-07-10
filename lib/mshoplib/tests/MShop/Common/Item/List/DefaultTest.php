<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Common_Item_List_Default
 */
class MShop_Common_Item_List_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$values = array(
			'id' => 8,
			'siteid' => 99,
			'parentid' => 2,
			'domain' => 'testDomain',
			'refid' => 'unitId',
			'start' => '2005-01-01 00:00:00',
			'end' => '2010-12-31 00:00:00',
			'config' => array( 'cnt'=>'40' ),
			'pos' => 7,
			'status' => 1,
			'typeid' => 8,
			'type' => 'test',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Common_Item_List_Default( 'common.list.', $values );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testGetId()
	{
		$this->assertEquals( 8, $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId( null );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertNull( $this->_object->getId() );
	}

	public function testGetParentId()
	{
		$this->assertEquals( 2, $this->_object->getParentId() );
	}

	public function testSetParentId()
	{
		$this->_object->setParentId( 5 );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 5, $this->_object->getParentId() );
	}

	public function testGetDomain()
	{
		$this->assertEquals( 'testDomain', $this->_object->getDomain() );
	}

	public function testSetDomain()
	{
		$this->_object->setDomain( 'newDom' );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 'newDom', $this->_object->getDomain() );
	}

	public function testGetRefId()
	{
		$this->assertEquals( 'unitId', $this->_object->getRefId() );
	}

	public function testSetRefId()
	{
		$this->_object->setRefId( 'unitReference' );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 'unitReference', $this->_object->getRefId() );
	}

	public function testGetDateStart()
	{
		$this->assertEquals( '2005-01-01 00:00:00', $this->_object->getDateStart() );
	}

	public function testSetDateStart()
	{
		$this->_object->setDateStart( '2002-01-01 00:00:00' );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( '2002-01-01 00:00:00', $this->_object->getDateStart() );

		$this->setExpectedException('MShop_Exception');
		$this->_object->setDateStart('2008-34-12');
	}

	public function testGetDateEnd()
	{
		$this->assertEquals( '2010-12-31 00:00:00', $this->_object->getDateEnd() );
	}

	public function testSetDateEnd()
	{
		$this->_object->setDateEnd( '4400-12-31 00:00:00' );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( '4400-12-31 00:00:00', $this->_object->getDateEnd() );

		$this->setExpectedException('MShop_Exception');
		$this->_object->setDateEnd('2008-34-12');
	}

	public function testSetPosition()
	{
		$this->_object->setPosition( 1234 );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 1234, $this->_object->getPosition() );
	}

	public function testGetPosition()
	{
		$this->assertEquals( 7, $this->_object->getPosition() );
	}

	public function testSetStatus()
	{
		$this->_object->setStatus( 0 );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 0, $this->_object->getStatus() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->_object->getStatus() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 8, $this->_object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$this->_object->setTypeId( 18 );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 18, $this->_object->getTypeId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->_object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->_object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}

	public function testGetConfig()
	{
		$this->assertEquals( array('cnt'=>'40'), $this->_object->getConfig() );
	}


	public function testSetConfig()
	{
		$this->_object->setConfig( array('new value'=>'20.00') );
		$this->assertEquals( array('new value'=>'20.00'), $this->_object->getConfig() );
		$this->assertEquals( true, $this->_object->isModified() );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}

	public function testGetRefItem()
	{
		$this->assertEquals( null, $this->_object->getRefItem() );
	}

	public function testSetRefItem()
	{
		$obj = new MShop_Common_Item_List_Default( 'reftest', array() );

		$this->_object->setRefItem( $obj );
		$this->assertFalse( $this->_object->isModified() );
		$this->assertSame( $obj, $this->_object->getRefItem() );
	}


	public function testFromArray()
	{
		$item = new MShop_Common_Item_List_Default('common.list.');

		$list = array(
			'common.list.id' => 8,
			'common.list.parentid' => 2,
			'common.list.typeid' => 8,
			'common.list.domain' => 'testDomain',
			'common.list.refid' => 'unitId',
			'common.list.config' => array( 'cnt' => '40' ),
			'common.list.position' => 7,
			'common.list.status' => 1,
		);

		$unknown = $item->fromArray($list);

		$this->assertEquals(array(), $unknown);

		$this->assertEquals($list['common.list.id'], $item->getId());
		$this->assertEquals($list['common.list.parentid'], $item->getParentId());
		$this->assertEquals($list['common.list.typeid'], $item->getTypeId());
		$this->assertEquals($list['common.list.domain'], $item->getDomain());
		$this->assertEquals($list['common.list.refid'], $item->getRefId());
		$this->assertEquals($list['common.list.config'], $item->getConfig());
		$this->assertEquals($list['common.list.position'], $item->getPosition());
		$this->assertEquals($list['common.list.status'], $item->getStatus());
	}


	public function testToArray()
	{
		$expected = array(
			'common.list.id' => 8,
			'common.list.siteid' => 99,
			'common.list.parentid' => 2,
			'common.list.typeid' => 8,
			'common.list.type' => 'test',
			'common.list.domain' => 'testDomain',
			'common.list.refid' => 'unitId',
			'common.list.datestart' => '2005-01-01 00:00:00',
			'common.list.dateend' => '2010-12-31 00:00:00',
			'common.list.config' => array( 'cnt' => '40' ),
			'common.list.position' => 7,
			'common.list.status' => 1,
			'common.list.ctime' => '2011-01-01 00:00:01',
			'common.list.mtime' => '2011-01-01 00:00:02',
			'common.list.editor' => 'unitTestUser',
		);

		$this->assertEquals( $expected, $this->_object->toArray() );
	}
}
