<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MShop\Rule\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'rule.id' => 123,
			'rule.siteid' => 99,
			'rule.label' => 'unitTestRule',
			'rule.type' => 'catalog',
			'rule.provider' => 'provider',
			'rule.config' => array( 'limit' => '40' ),
			'rule.datestart' => null,
			'rule.dateend' => null,
			'rule.position' => 0,
			'rule.status' => 1,
			'rule.mtime' => '2011-01-01 00:00:02',
			'rule.ctime' => '2011-01-01 00:00:01',
			'rule.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Rule\Item\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( 123, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Rule\Item\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'catalog', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Rule\Item\Iface::class, $return );
		$this->assertEquals( 'test', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unitTestRule', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'anotherLabel' );

		$this->assertInstanceOf( \Aimeos\MShop\Rule\Item\Iface::class, $return );
		$this->assertEquals( 'anotherLabel', $this->object->getLabel() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetProvider()
	{
		$this->assertEquals( 'provider', $this->object->getProvider() );
	}


	public function testSetProvider()
	{
		$return = $this->object->setProvider( 'newProvider' );

		$this->assertInstanceOf( \Aimeos\MShop\Rule\Item\Iface::class, $return );
		$this->assertEquals( 'newProvider', $this->object->getProvider() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testSetProviderInvalid()
	{
		$this->expectException( \Aimeos\MShop\Rule\Exception::class );
		$this->object->setProvider( ',newProvider' );
	}


	public function testGetConfig()
	{
		$this->assertEquals( array( 'limit'=>'40' ), $this->object->getConfig() );
	}


	public function testGetConfigValue()
	{
		$this->assertEquals( '40', $this->object->getConfigValue( 'limit' ) );
	}


	public function testSetConfig()
	{
		$return = $this->object->setConfig( array( 'threshold' => '20.00' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Rule\Item\Iface::class, $return );
		$this->assertEquals( array( 'threshold'=>'20.00' ), $this->object->getConfig() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetDateStart()
	{
		$this->assertEquals( null, $this->object->getDateStart() );
	}


	public function testSetDateStart()
	{
		$return = $this->object->setDateStart( '2010-04-22 06:22:22' );

		$this->assertInstanceOf( \Aimeos\MShop\Rule\Item\Iface::class, $return );
		$this->assertEquals( '2010-04-22 06:22:22', $this->object->getDateStart() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDateEnd()
	{
		$this->assertEquals( null, $this->object->getDateEnd() );
	}


	public function testSetDateEnd()
	{
		$return = $this->object->setDateEnd( '2010-05-22 06:22' );

		$this->assertInstanceOf( \Aimeos\MShop\Rule\Item\Iface::class, $return );
		$this->assertEquals( '2010-05-22 06:22:00', $this->object->getDateEnd() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 0, $this->object->getPosition() );
	}


	public function testSetPosition()
	{
		$return = $this->object->setPosition( 1 );

		$this->assertInstanceOf( \Aimeos\MShop\Rule\Item\Iface::class, $return );
		$this->assertEquals( 1, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Rule\Item\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getStatus() );
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
		$this->assertEquals( 'rule', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Rule\Item\Standard();

		$list = $entries = array(
			'rule.id' => 1,
			'rule.type' => 'test',
			'rule.label' => 'test item',
			'rule.provider' => 'FreeShipping',
			'rule.datestart' => '2000-01-01 00:00:00',
			'rule.dateend' => '2001-01-01 00:00:00',
			'rule.config' => array( 'test' ),
			'rule.status' => 0,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['rule.id'], $item->getId() );
		$this->assertEquals( $list['rule.type'], $item->getType() );
		$this->assertEquals( $list['rule.label'], $item->getLabel() );
		$this->assertEquals( $list['rule.provider'], $item->getProvider() );
		$this->assertEquals( $list['rule.datestart'], $item->getDateStart() );
		$this->assertEquals( $list['rule.dateend'], $item->getDateEnd() );
		$this->assertEquals( $list['rule.config'], $item->getConfig() );
		$this->assertEquals( $list['rule.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['rule.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['rule.siteid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['rule.type'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['rule.label'] );
		$this->assertEquals( $this->object->getProvider(), $arrayObject['rule.provider'] );
		$this->assertEquals( $this->object->getDateStart(), $arrayObject['rule.datestart'] );
		$this->assertEquals( $this->object->getDateEnd(), $arrayObject['rule.dateend'] );
		$this->assertEquals( $this->object->getConfig(), $arrayObject['rule.config'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['rule.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['rule.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['rule.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['rule.editor'] );
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
}
