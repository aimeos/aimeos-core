<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Common\Item\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$values = array(
			'common.lists.id' => 8,
			'common.lists.siteid' => 99,
			'common.lists.parentid' => 2,
			'common.lists.domain' => 'testDomain',
			'common.lists.refid' => 'unitId',
			'common.lists.datestart' => '2005-01-01 00:00:00',
			'common.lists.dateend' => '2100-01-01 00:00:00',
			'common.lists.config' => array( 'cnt' => '40' ),
			'common.lists.position' => 7,
			'common.lists.status' => 1,
			'common.lists.type' => 'test',
			'common.lists.mtime' => '2011-01-01 00:00:02',
			'common.lists.ctime' => '2011-01-01 00:00:01',
			'common.lists.editor' => 'unitTestUser',
			'.date' => date( 'Y-m-d H:i:s' ),
		);

		$this->object = new \Aimeos\MShop\Common\Item\Lists\Standard( 'common.lists.', $values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( 8, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertTrue( $this->object->isModified() );
		$this->assertNull( $this->object->getId() );
	}


	public function testGetParentId()
	{
		$this->assertEquals( 2, $this->object->getParentId() );
	}


	public function testSetParentId()
	{
		$return = $this->object->setParentId( 5 );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertEquals( 5, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetKey()
	{
		$this->assertEquals( 'testDomain|test|unitId', $this->object->getKey() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'testDomain', $this->object->getDomain() );
	}


	public function testSetDomain()
	{
		$return = $this->object->setDomain( 'newDom' );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertEquals( 'newDom', $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetRefId()
	{
		$this->assertEquals( 'unitId', $this->object->getRefId() );
	}


	public function testSetRefId()
	{
		$return = $this->object->setRefId( 'unitReference' );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertEquals( 'unitReference', $this->object->getRefId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDateStart()
	{
		$this->assertEquals( '2005-01-01 00:00:00', $this->object->getDateStart() );
	}


	public function testSetDateStart()
	{
		$return = $this->object->setDateStart( '2002-01-01 00:00:00' );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertEquals( '2002-01-01 00:00:00', $this->object->getDateStart() );
		$this->assertTrue( $this->object->isModified() );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setDateStart( '2008-34-12' );
	}


	public function testGetDateEnd()
	{
		$this->assertEquals( '2100-01-01 00:00:00', $this->object->getDateEnd() );
	}


	public function testSetDateEnd()
	{
		$return = $this->object->setDateEnd( '4400-12-31 00:00:00' );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertEquals( '4400-12-31 00:00:00', $this->object->getDateEnd() );
		$this->assertTrue( $this->object->isModified() );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setDateEnd( '2008-34-12' );
	}


	public function testSetPosition()
	{
		$return = $this->object->setPosition( 1234 );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertEquals( 1234, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 7, $this->object->getPosition() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'test', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'test2' );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertEquals( 'test2', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
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


	public function testGetConfigValue()
	{
		$this->assertEquals( '40', $this->object->getConfigValue( 'cnt' ) );
	}


	public function testSetConfig()
	{
		$return = $this->object->setConfig( array( 'new value'=>'20.00' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertEquals( array( 'new value'=>'20.00' ), $this->object->getConfig() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setAvailable( false );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsAvailableOnStatus()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setStatus( 0 );
		$this->assertFalse( $this->object->isAvailable() );
		$this->object->setStatus( -1 );
		$this->assertFalse( $this->object->isAvailable() );
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
		$obj = new \Aimeos\MShop\Common\Item\Lists\Standard( 'reftest', [] );

		$return = $this->object->setRefItem( $obj );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $return );
		$this->assertSame( $obj, $this->object->getRefItem() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'common/lists', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Common\Item\Lists\Standard( 'common.lists.' );

		$list = $entries = array(
			'common.lists.id' => 8,
			'common.lists.parentid' => 2,
			'common.lists.type' => 'default',
			'common.lists.domain' => 'testDomain',
			'common.lists.refid' => 'unitId',
			'common.lists.config' => array( 'cnt' => '40' ),
			'common.lists.position' => 7,
			'common.lists.status' => 1,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['common.lists.id'], $item->getId() );
		$this->assertEquals( $list['common.lists.parentid'], $item->getParentId() );
		$this->assertEquals( $list['common.lists.type'], $item->getType() );
		$this->assertEquals( $list['common.lists.domain'], $item->getDomain() );
		$this->assertEquals( $list['common.lists.refid'], $item->getRefId() );
		$this->assertEquals( $list['common.lists.config'], $item->getConfig() );
		$this->assertEquals( $list['common.lists.position'], $item->getPosition() );
		$this->assertEquals( $list['common.lists.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$expected = array(
			'common.lists.id' => '8',
			'common.lists.siteid' => 99,
			'common.lists.parentid' => 2,
			'common.lists.key' => 'testDomain|test|unitId',
			'common.lists.domain' => 'testDomain',
			'common.lists.refid' => 'unitId',
			'common.lists.datestart' => '2005-01-01 00:00:00',
			'common.lists.dateend' => '2100-01-01 00:00:00',
			'common.lists.config' => array( 'cnt' => '40' ),
			'common.lists.position' => 7,
			'common.lists.status' => 1,
			'common.lists.ctime' => '2011-01-01 00:00:01',
			'common.lists.mtime' => '2011-01-01 00:00:02',
			'common.lists.editor' => 'unitTestUser',
			'common.lists.type' => 'test',
		);

		$this->assertEquals( $expected, $this->object->toArray( true ) );
	}
}
