<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Locale_Item_Language_Default.
 */
class MShop_Locale_Item_Language_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	protected function setUp()
	{
		$this->values = array(
			'id' => 'es',
			'label' => 'spanish',
			'siteid' => 1,
			'status' => 9,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);
		$this->object = new MShop_Locale_Item_Language_Default( $this->values );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetId()
	{
		$this->assertEquals( 'es', $this->object->getId() );
	}


	public function testSetId()
	{
		$this->object->setId( 'de' );
		$this->assertEquals( 'de', $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$var = null;
		$this->object->setId( $var );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetIdLength()
	{
		$this->setExpectedException( 'MShop_Locale_Exception' );
		$this->object->setId( 'espania' );
	}


	public function testSetIdNumeric()
	{
		$this->setExpectedException( 'MShop_Locale_Exception' );
		$this->object->setId( 123 );
	}


	public function testGetCode()
	{
		$this->assertEquals( $this->object->getId(), $this->object->getCode() );
	}


	public function testSetCodeInvalid()
	{
		$this->setExpectedException( 'MShop_Locale_Exception' );
		$this->object->setCode( 'XXX' );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'spanish', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->object->setLabel( 'OtherName' );
		$this->assertEquals( 'OtherName', $this->object->getLabel() );
		// test modifier
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 1, $this->object->getSiteId() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 9, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$this->object->setStatus( 0 );
		$this->assertEquals( 0, $this->object->getStatus() );
		// test modifier
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


	public function testFromArray()
	{
		$item = new MShop_Locale_Item_Language_Default();

		$list = array(
				'locale.language.id' => 'de',
				'locale.language.code' => 'de',
				'locale.language.label' => 'test item',
				'locale.language.status' => 1,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['locale.language.id'], $item->getId() );
		$this->assertEquals( $list['locale.language.code'], $item->getCode() );
		$this->assertEquals( $list['locale.language.label'], $item->getLabel() );
		$this->assertEquals( $list['locale.language.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( ( count( $this->values ) + 1 ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['locale.language.id'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['locale.language.code'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['locale.language.label'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['locale.language.siteid'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['locale.language.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['locale.language.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['locale.language.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['locale.language.editor'] );
	}

}
