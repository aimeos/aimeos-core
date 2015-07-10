<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Service_Item_Default.
 */
class MShop_Service_Item_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_values = array();


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_values = array(
			'id' => 541,
			'siteid'=>99,
			'pos' => '0',
			'typeid' => 1,
			'type' => 'delivery',
			'code' => 'wa34Hg',
			'label' => 'deliveryObject',
			'provider' => 'Default',
			'config' => array('url' => 'https://localhost/'),
			'status' => 0,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Service_Item_Default( $this->_values );
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
	}

	public function testGetId()
	{
		$this->assertEquals( 541, $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->assertFalse($this->_object->isModified());

		$this->_object->setId(null);
		$this->assertTrue($this->_object->isModified());
		$this->assertNull( $this->_object->getId());
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}

	public function testGetPosition()
	{
		$this->assertEquals( 0, $this->_object->getPosition() );
	}

	public function testSetPosition()
	{
		$this->assertFalse($this->_object->isModified());

		$this->_object->setPosition(4);
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 4, $this->_object->getPosition());
	}

	public function testGetCode()
	{
		$this->assertEquals( 'wa34Hg', $this->_object->getCode() );
	}

	public function testSetCode()
	{
		$this->assertFalse($this->_object->isModified());

		$this->_object->setCode( 'newCode' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'newCode', $this->_object->getCode());
	}

	public function testGetProvider()
	{
		$this->assertEquals( 'Default', $this->_object->getProvider() );
	}

	public function testSetProvider()
	{
		$this->assertFalse($this->_object->isModified());

		$this->_object->setProvider( 'TestProvider' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'TestProvider', $this->_object->getProvider() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'deliveryObject', $this->_object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->assertFalse($this->_object->isModified());

		$this->_object->setLabel( 'newName' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'newName', $this->_object->getLabel() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 0, $this->_object->getStatus() );
	}


	public function testSetStatus()
	{
		$this->_object->setStatus(10);
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 10, $this->_object->getStatus() );
	}

	public function testGetConfig()
	{
		$this->assertEquals( array('url' => 'https://localhost/'), $this->_object->getConfig());
	}


	public function testSetConfig()
	{
		$this->assertFalse($this->_object->isModified());

		$this->_object->setConfig( array('account' => 'testAccount') );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( array('account' => 'testAccount'), $this->_object->getConfig() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( $this->_values['typeid'], $this->_object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$this->assertFalse($this->_object->isModified());

		$this->_object->setTypeId( 2 );

		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 2, $this->_object->getTypeId() );
	}

	public function testGetType()
	{
		$this->assertEquals( $this->_values['type'], $this->_object->getType());
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


	public function testFromArray()
	{
		$item = new MShop_Service_Item_Default();

		$list = array(
			'service.id' => 1,
			'service.typeid' => 2,
			'service.code' => 'test',
			'service.label' => 'test item',
			'service.provider' => 'PayPal',
			'service.config' => array('test'),
			'service.position' => 3,
			'service.status' => 0,
		);

		$unknown = $item->fromArray($list);

		$this->assertEquals(array(), $unknown);

		$this->assertEquals($list['service.id'], $item->getId());
		$this->assertEquals($list['service.typeid'], $item->getTypeId());
		$this->assertEquals($list['service.code'], $item->getCode());
		$this->assertEquals($list['service.label'], $item->getLabel());
		$this->assertEquals($list['service.provider'], $item->getProvider());
		$this->assertEquals($list['service.position'], $item->getPosition());
		$this->assertEquals($list['service.config'], $item->getConfig());
		$this->assertEquals($list['service.status'], $item->getStatus());
	}


	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['service.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['service.siteid'] );
		$this->assertEquals( $this->_object->getTypeId(), $arrayObject['service.typeid'] );
		$this->assertEquals( $this->_object->getType(), $arrayObject['service.type'] );
		$this->assertEquals( $this->_object->getCode(), $arrayObject['service.code'] );
		$this->assertEquals( $this->_object->getLabel(), $arrayObject['service.label'] );
		$this->assertEquals( $this->_object->getProvider(), $arrayObject['service.provider'] );
		$this->assertEquals( $this->_object->getPosition(), $arrayObject['service.position'] );
		$this->assertEquals( $this->_object->getConfig(), $arrayObject['service.config'] );
		$this->assertEquals( $this->_object->getStatus(), $arrayObject['service.status'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['service.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['service.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['service.editor'] );
	}
}