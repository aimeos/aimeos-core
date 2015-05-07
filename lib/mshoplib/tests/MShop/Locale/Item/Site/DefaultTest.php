<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Test class for MShop_Locale_Item_Site_Default.
 */
class MShop_Locale_Item_Site_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_values;


	protected function setUp()
	{
		$this->_values = array(
			'id' => 12,
			'siteid' => 12,
			'code' => 'ExtID',
			'label' => 'My Site',
			'config' => array( 'timezone' => 'Europe/Berlin' ),
			'status' => 1,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$children = array( new MShop_Locale_Item_Site_Default() );
		$this->_object = new MShop_Locale_Item_Site_Default($this->_values, $children);
	}


	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}


	public function testGetId()
	{
		$this->assertEquals(12, $this->_object->getId());
	}


	public function testGetSiteId()
	{
		$this->assertEquals(12, $this->_object->getSiteId());
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertEquals(null, $this->_object->getId());
		$this->assertTrue($this->_object->isModified());

		$this->_object->setId(12);
		$this->assertFalse($this->_object->isModified());

		$this->setExpectedException('MShop_Exception');
		$this->_object->setId(99);
	}


	public function testGetCode()
	{
		$this->assertEquals('ExtID', $this->_object->getCode());
	}


	public function testSetCode()
	{
		$this->_object->setCode('OtherExtID');
		$this->assertEquals('OtherExtID', $this->_object->getCode());
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetLabel()
	{
		$this->assertEquals('My Site', $this->_object->getLabel());
	}


	public function testSetLabel()
	{
		$this->_object->setLabel('Other Name');
		$this->assertEquals('Other Name', $this->_object->getLabel());
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetConfig()
	{
		$this->assertEquals(array( 'timezone' => 'Europe/Berlin' ), $this->_object->getConfig());
	}


	public function testSetConfig()
	{
		$this->_object->setConfig(array( 'timezone' => 'Europe/Paris' ));
		$this->assertEquals(array( 'timezone' => 'Europe/Paris' ), $this->_object->getConfig());
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetStatus()
	{
		$this->assertEquals(1, $this->_object->getStatus());
	}


	public function testSetStatus()
	{
		$this->_object->setStatus(0);
		$this->assertEquals(0, $this->_object->getStatus());
		$this->assertTrue($this->_object->isModified());
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
		$item = new MShop_Locale_Item_Site_Default();

		$list = array(
			'locale.site.id' => 2,
			'locale.site.code' => 'test',
			'locale.site.label' => 'test item',
			'locale.site.status' => 1,
			'locale.site.config' => array('test'),
		);

		$unknown = $item->fromArray($list);

		$this->assertEquals(array(), $unknown);

		$this->assertEquals($list['locale.site.id'], $item->getId());
		$this->assertEquals($list['locale.site.code'], $item->getCode());
		$this->assertEquals($list['locale.site.label'], $item->getLabel());
		$this->assertEquals($list['locale.site.status'], $item->getStatus());
		$this->assertEquals($list['locale.site.config'], $item->getConfig());
	}


	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals(count($this->_values), count($arrayObject));

		$this->assertEquals($this->_object->getId(), $arrayObject['locale.site.id']);
		$this->assertEquals($this->_object->getSiteId(), $arrayObject['locale.site.siteid']);
		$this->assertEquals($this->_object->getCode(), $arrayObject['locale.site.code']);
		$this->assertEquals($this->_object->getLabel(), $arrayObject['locale.site.label']);
		$this->assertEquals($this->_object->getStatus(), $arrayObject['locale.site.status']);
		$this->assertEquals($this->_object->getConfig(), $arrayObject['locale.site.config']);
		$this->assertEquals($this->_object->getTimeCreated(), $arrayObject['locale.site.ctime'] );
		$this->assertEquals($this->_object->getTimeModified(), $arrayObject['locale.site.mtime'] );
		$this->assertEquals($this->_object->getEditor(), $arrayObject['locale.site.editor'] );
	}


	public function testAddChild()
	{
		$this->_object->addChild( $this->_object );
		$this->assertEquals( 2, count( $this->_object->getChildren() ) );
	}


	public function testGetChild()
	{
		$this->assertInstanceOf( 'MShop_Locale_Item_Site_Interface', $this->_object->getChild( 0 ) );

		$this->setExpectedException( 'MShop_Locale_Exception' );
		$this->_object->getChild( 1 );
	}


	public function testGetChildren()
	{
		$children = $this->_object->getChildren();
		$this->assertEquals( 1, count( $children ) );

		foreach( $children as $child ) {
			$this->assertInstanceOf( 'MShop_Locale_Item_Site_Interface', $child );
		}
	}


	public function testHasChildren()
	{
		$this->assertTrue( $this->_object->hasChildren() );
	}
}
