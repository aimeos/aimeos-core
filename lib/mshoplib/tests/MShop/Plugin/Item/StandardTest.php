<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Plugin\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'plugin.id' => 123,
			'plugin.siteid' => 99,
			'plugin.label' => 'unitTestPlugin',
			'plugin.type' => 'order',
			'plugin.provider' => 'provider',
			'plugin.config' => array( 'limit' => '40' ),
			'plugin.position' => 0,
			'plugin.status' => 1,
			'plugin.mtime' => '2011-01-01 00:00:02',
			'plugin.ctime' => '2011-01-01 00:00:01',
			'plugin.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Plugin\Item\Standard( $this->values );
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

		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Item\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'order', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Item\Iface::class, $return );
		$this->assertEquals( 'test', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unitTestPlugin', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'anotherLabel' );

		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Item\Iface::class, $return );
		$this->assertEquals( 'newProvider', $this->object->getProvider() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testSetProviderInvalid()
	{
		$this->expectException( \Aimeos\MShop\Plugin\Exception::class );
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

		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Item\Iface::class, $return );
		$this->assertEquals( array( 'threshold'=>'20.00' ), $this->object->getConfig() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 0, $this->object->getPosition() );
	}


	public function testSetPosition()
	{
		$return = $this->object->setPosition( 1 );

		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Item\Iface::class, $return );
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
		$this->assertEquals( 'plugin', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Plugin\Item\Standard();

		$list = $entries = array(
			'plugin.id' => 1,
			'plugin.type' => 'test',
			'plugin.label' => 'test item',
			'plugin.provider' => 'FreeShipping',
			'plugin.config' => array( 'test' ),
			'plugin.status' => 0,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['plugin.id'], $item->getId() );
		$this->assertEquals( $list['plugin.type'], $item->getType() );
		$this->assertEquals( $list['plugin.label'], $item->getLabel() );
		$this->assertEquals( $list['plugin.provider'], $item->getProvider() );
		$this->assertEquals( $list['plugin.config'], $item->getConfig() );
		$this->assertEquals( $list['plugin.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['plugin.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['plugin.siteid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['plugin.type'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['plugin.label'] );
		$this->assertEquals( $this->object->getProvider(), $arrayObject['plugin.provider'] );
		$this->assertEquals( $this->object->getConfig(), $arrayObject['plugin.config'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['plugin.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['plugin.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['plugin.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['plugin.editor'] );
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
