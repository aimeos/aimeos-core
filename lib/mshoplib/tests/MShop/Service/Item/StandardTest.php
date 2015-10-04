<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Service_Item_Standard.
 */
class MShop_Service_Item_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $values = array();


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->values = array(
			'id' => 541,
			'siteid'=>99,
			'pos' => '0',
			'typeid' => 1,
			'type' => 'delivery',
			'code' => 'wa34Hg',
			'label' => 'deliveryObject',
			'provider' => 'Standard',
			'config' => array( 'url' => 'https://localhost/' ),
			'status' => 0,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new MShop_Service_Item_Standard( $this->values );
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
		$this->assertFalse( $this->object->isModified() );

		$this->object->setId( null );
		$this->assertTrue( $this->object->isModified() );
		$this->assertNull( $this->object->getId() );
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
		$this->assertFalse( $this->object->isModified() );

		$this->object->setPosition( 4 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 4, $this->object->getPosition() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'wa34Hg', $this->object->getCode() );
	}

	public function testSetCode()
	{
		$this->assertFalse( $this->object->isModified() );

		$this->object->setCode( 'newCode' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'newCode', $this->object->getCode() );
	}

	public function testGetProvider()
	{
		$this->assertEquals( 'Standard', $this->object->getProvider() );
	}

	public function testSetProvider()
	{
		$this->assertFalse( $this->object->isModified() );

		$this->object->setProvider( 'TestProvider' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'TestProvider', $this->object->getProvider() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'deliveryObject', $this->object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->assertFalse( $this->object->isModified() );

		$this->object->setLabel( 'newName' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'newName', $this->object->getLabel() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 0, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$this->object->setStatus( 10 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 10, $this->object->getStatus() );
	}

	public function testGetConfig()
	{
		$this->assertEquals( array( 'url' => 'https://localhost/' ), $this->object->getConfig() );
	}


	public function testSetConfig()
	{
		$this->assertFalse( $this->object->isModified() );

		$this->object->setConfig( array( 'account' => 'testAccount' ) );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( array( 'account' => 'testAccount' ), $this->object->getConfig() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( $this->values['typeid'], $this->object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$this->assertFalse( $this->object->isModified() );

		$this->object->setTypeId( 2 );

		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 2, $this->object->getTypeId() );
	}

	public function testGetType()
	{
		$this->assertEquals( $this->values['type'], $this->object->getType() );
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
		$item = new MShop_Service_Item_Standard();

		$list = array(
			'service.id' => 1,
			'service.typeid' => 2,
			'service.code' => 'test',
			'service.label' => 'test item',
			'service.provider' => 'PayPal',
			'service.config' => array( 'test' ),
			'service.position' => 3,
			'service.status' => 0,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['service.id'], $item->getId() );
		$this->assertEquals( $list['service.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['service.code'], $item->getCode() );
		$this->assertEquals( $list['service.label'], $item->getLabel() );
		$this->assertEquals( $list['service.provider'], $item->getProvider() );
		$this->assertEquals( $list['service.position'], $item->getPosition() );
		$this->assertEquals( $list['service.config'], $item->getConfig() );
		$this->assertEquals( $list['service.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['service.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['service.siteid'] );
		$this->assertEquals( $this->object->getTypeId(), $arrayObject['service.typeid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['service.type'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['service.code'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['service.label'] );
		$this->assertEquals( $this->object->getProvider(), $arrayObject['service.provider'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['service.position'] );
		$this->assertEquals( $this->object->getConfig(), $arrayObject['service.config'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['service.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['service.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['service.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['service.editor'] );
	}
}