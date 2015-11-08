<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Common\Item\Lists;


/**
 * Test class for \Aimeos\MShop\Common\Item\Lists\Standard
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


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

		$this->object = new \Aimeos\MShop\Common\Item\Lists\Standard( 'common.lists.', $values );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( 8, $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertTrue( $this->object->isModified() );
		$this->assertNull( $this->object->getId() );
	}

	public function testGetParentId()
	{
		$this->assertEquals( 2, $this->object->getParentId() );
	}

	public function testSetParentId()
	{
		$this->object->setParentId( 5 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 5, $this->object->getParentId() );
	}

	public function testGetDomain()
	{
		$this->assertEquals( 'testDomain', $this->object->getDomain() );
	}

	public function testSetDomain()
	{
		$this->object->setDomain( 'newDom' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'newDom', $this->object->getDomain() );
	}

	public function testGetRefId()
	{
		$this->assertEquals( 'unitId', $this->object->getRefId() );
	}

	public function testSetRefId()
	{
		$this->object->setRefId( 'unitReference' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'unitReference', $this->object->getRefId() );
	}

	public function testGetDateStart()
	{
		$this->assertEquals( '2005-01-01 00:00:00', $this->object->getDateStart() );
	}

	public function testSetDateStart()
	{
		$this->object->setDateStart( '2002-01-01 00:00:00' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( '2002-01-01 00:00:00', $this->object->getDateStart() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setDateStart( '2008-34-12' );
	}

	public function testGetDateEnd()
	{
		$this->assertEquals( '2010-12-31 00:00:00', $this->object->getDateEnd() );
	}

	public function testSetDateEnd()
	{
		$this->object->setDateEnd( '4400-12-31 00:00:00' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( '4400-12-31 00:00:00', $this->object->getDateEnd() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setDateEnd( '2008-34-12' );
	}

	public function testSetPosition()
	{
		$this->object->setPosition( 1234 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 1234, $this->object->getPosition() );
	}

	public function testGetPosition()
	{
		$this->assertEquals( 7, $this->object->getPosition() );
	}

	public function testSetStatus()
	{
		$this->object->setStatus( 0 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 0, $this->object->getStatus() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 8, $this->object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$this->object->setTypeId( 18 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 18, $this->object->getTypeId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
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

	public function testGetConfig()
	{
		$this->assertEquals( array( 'cnt'=>'40' ), $this->object->getConfig() );
	}


	public function testSetConfig()
	{
		$this->object->setConfig( array( 'new value'=>'20.00' ) );
		$this->assertEquals( array( 'new value'=>'20.00' ), $this->object->getConfig() );
		$this->assertEquals( true, $this->object->isModified() );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}

	public function testGetRefItem()
	{
		$this->assertEquals( null, $this->object->getRefItem() );
	}

	public function testSetRefItem()
	{
		$obj = new \Aimeos\MShop\Common\Item\Lists\Standard( 'reftest', array() );

		$this->object->setRefItem( $obj );
		$this->assertFalse( $this->object->isModified() );
		$this->assertSame( $obj, $this->object->getRefItem() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'common/lists', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Common\Item\Lists\Standard( 'common.lists.' );

		$list = array(
			'common.lists.id' => 8,
			'common.lists.parentid' => 2,
			'common.lists.typeid' => 8,
			'common.lists.domain' => 'testDomain',
			'common.lists.refid' => 'unitId',
			'common.lists.config' => array( 'cnt' => '40' ),
			'common.lists.position' => 7,
			'common.lists.status' => 1,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['common.lists.id'], $item->getId() );
		$this->assertEquals( $list['common.lists.parentid'], $item->getParentId() );
		$this->assertEquals( $list['common.lists.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['common.lists.domain'], $item->getDomain() );
		$this->assertEquals( $list['common.lists.refid'], $item->getRefId() );
		$this->assertEquals( $list['common.lists.config'], $item->getConfig() );
		$this->assertEquals( $list['common.lists.position'], $item->getPosition() );
		$this->assertEquals( $list['common.lists.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$expected = array(
			'common.lists.id' => 8,
			'common.lists.siteid' => 99,
			'common.lists.parentid' => 2,
			'common.lists.typeid' => 8,
			'common.lists.type' => 'test',
			'common.lists.domain' => 'testDomain',
			'common.lists.refid' => 'unitId',
			'common.lists.datestart' => '2005-01-01 00:00:00',
			'common.lists.dateend' => '2010-12-31 00:00:00',
			'common.lists.config' => array( 'cnt' => '40' ),
			'common.lists.position' => 7,
			'common.lists.status' => 1,
			'common.lists.ctime' => '2011-01-01 00:00:01',
			'common.lists.mtime' => '2011-01-01 00:00:02',
			'common.lists.editor' => 'unitTestUser',
		);

		$this->assertEquals( $expected, $this->object->toArray() );
	}
}
