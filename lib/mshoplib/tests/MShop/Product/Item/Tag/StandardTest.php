<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Product_Item_Tag_Standard.
 */
class MShop_Product_Item_Tag_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->values = array(
			'id' => 987,
			'siteid'=>99,
			'typeid' => 44,
			'langid' => 'en',
			'type' => 'taste',
			'label' => 'salty',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new MShop_Product_Item_Tag_Standard( $this->values );
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
		$this->assertEquals( 987, $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertTrue( $this->object->isModified() );

		$this->assertNull( $this->object->getId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetLanguageId()
	{
		$this->assertEquals( 'en', $this->object->getLanguageId() );
	}

	public function testSetLanguageId()
	{
		$this->object->setLanguageId( 'fr' );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 'fr', $this->object->getLanguageId() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 44, $this->object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$this->object->setTypeId( 33 );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 33, $this->object->getTypeId() );
	}

	public function testGetType()
	{
		$this->assertEquals( 'taste', $this->object->getType() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'salty', $this->object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->object->setLabel( 'bitter' );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 'bitter', $this->object->getLabel() );
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
		$item = new MShop_Product_Item_Tag_Standard();

		$list = array(
			'product.tag.id' => 1,
			'product.tag.typeid' => 2,
			'product.tag.label' => 'test item',
			'product.tag.languageid' => 'de',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['product.tag.id'], $item->getId() );
		$this->assertEquals( $list['product.tag.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['product.tag.label'], $item->getLabel() );
		$this->assertEquals( $list['product.tag.languageid'], $item->getLanguageId() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['product.tag.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['product.tag.siteid'] );
		$this->assertEquals( $this->object->getTypeId(), $arrayObject['product.tag.typeid'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['product.tag.languageid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['product.tag.type'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['product.tag.label'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['product.tag.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['product.tag.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['product.tag.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
