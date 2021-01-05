<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MShop\Service\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values = [];


	protected function setUp() : void
	{
		$this->values = array(
			'service.id' => 541,
			'service.siteid' => 99,
			'service.position' => '0',
			'service.type' => 'delivery',
			'service.code' => 'wa34Hg',
			'service.label' => 'deliveryObject',
			'service.provider' => 'Standard',
			'service.datestart' => '2000-01-01 00:00:00',
			'service.dateend' => '2100-01-01 00:00:00',
			'service.config' => array( 'url' => 'https://localhost/' ),
			'service.status' => 1,
			'service.mtime' => '2011-01-01 00:00:02',
			'service.ctime' => '2011-01-01 00:00:01',
			'service.editor' => 'unitTestUser',
			'.date' => date( 'Y-m-d H:i:s' ),
		);

		$this->object = new \Aimeos\MShop\Service\Item\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testGetId()
	{
		$this->assertEquals( 541, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 0, $this->object->getPosition() );
	}


	public function testSetPosition()
	{
		$return = $this->object->setPosition( 4 );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $return );
		$this->assertEquals( 4, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'wa34Hg', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'newCode' );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $return );
		$this->assertEquals( 'newCode', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetProvider()
	{
		$this->assertEquals( 'Standard', $this->object->getProvider() );
	}


	public function testSetProvider()
	{
		$return = $this->object->setProvider( 'TestProvider' );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $return );
		$this->assertEquals( 'TestProvider', $this->object->getProvider() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetProviderInvalid()
	{
		$this->expectException( \Aimeos\MShop\Service\Exception::class );
		$this->object->setProvider( ',newProvider' );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'deliveryObject', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'newName' );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $return );
		$this->assertEquals( 'newName', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDateStart()
	{
		$this->assertEquals( '2000-01-01 00:00:00', $this->object->getDateStart() );
	}


	public function testSetDateStart()
	{
		$return = $this->object->setDateStart( '2010-04-22 06:22:22' );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $return );
		$this->assertEquals( '2010-04-22 06:22:22', $this->object->getDateStart() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDateEnd()
	{
		$this->assertEquals( '2100-01-01 00:00:00', $this->object->getDateEnd() );
	}


	public function testSetDateEnd()
	{
		$return = $this->object->setDateEnd( '2010-05-22 06:22:22' );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $return );
		$this->assertEquals( '2010-05-22 06:22:22', $this->object->getDateEnd() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 10 );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $return );
		$this->assertEquals( 10, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetConfig()
	{
		$this->assertEquals( array( 'url' => 'https://localhost/' ), $this->object->getConfig() );
	}


	public function testGetConfigValue()
	{
		$this->assertEquals( 'https://localhost/', $this->object->getConfigValue( 'url' ) );
	}


	public function testSetConfig()
	{
		$return = $this->object->setConfig( array( 'account' => 'testAccount' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $return );
		$this->assertEquals( array( 'account' => 'testAccount' ), $this->object->getConfig() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetType()
	{
		$this->assertEquals( 'delivery', $this->object->getType() );
	}

	public function testSetType()
	{
		$return = $this->object->setType( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $return );
		$this->assertEquals( 'test', $this->object->getType() );
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
		$this->assertEquals( 'service', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Service\Item\Standard();

		$list = $entries = array(
			'service.id' => 1,
			'service.type' => 'test',
			'service.code' => 'test',
			'service.label' => 'test item',
			'service.provider' => 'PayPal',
			'service.datestart' => '2000-01-01 00:00:02',
			'service.dateend' => '2100-01-01 00:00:01',
			'service.config' => array( 'test' ),
			'service.position' => 3,
			'service.status' => 0,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['service.id'], $item->getId() );
		$this->assertEquals( $list['service.code'], $item->getCode() );
		$this->assertEquals( $list['service.label'], $item->getLabel() );
		$this->assertEquals( $list['service.type'], $item->getType() );
		$this->assertEquals( $list['service.provider'], $item->getProvider() );
		$this->assertEquals( $list['service.position'], $item->getPosition() );
		$this->assertEquals( $list['service.datestart'], $item->getDateStart() );
		$this->assertEquals( $list['service.dateend'], $item->getDateEnd() );
		$this->assertEquals( $list['service.config'], $item->getConfig() );
		$this->assertEquals( $list['service.status'], $item->getStatus() );
		$this->assertEquals( '', $item->getSiteId() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ) - 1, count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['service.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['service.siteid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['service.type'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['service.code'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['service.label'] );
		$this->assertEquals( $this->object->getProvider(), $arrayObject['service.provider'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['service.position'] );
		$this->assertEquals( $this->object->getDateStart(), $arrayObject['service.datestart'] );
		$this->assertEquals( $this->object->getDateEnd(), $arrayObject['service.dateend'] );
		$this->assertEquals( $this->object->getConfig(), $arrayObject['service.config'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['service.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['service.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['service.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['service.editor'] );
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
