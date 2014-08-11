<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Product_Item_Tag_Default.
 */
class MShop_Product_Item_Tag_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_values = array(
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

		$this->_object = new MShop_Product_Item_Tag_Default( $this->_values );
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
		$this->assertEquals( 987, $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertTrue( $this->_object->isModified() );

		$this->assertNull( $this->_object->getId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}

	public function testGetLanguageId()
	{
		$this->assertEquals( 'en', $this->_object->getLanguageId() );
	}

	public function testSetLanguageId()
	{
		$this->_object->setLanguageId('fr');
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 'fr', $this->_object->getLanguageId() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 44, $this->_object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$this->_object->setTypeId(33);
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 33, $this->_object->getTypeId() );
	}

	public function testGetType()
	{
		$this->assertEquals( 'taste', $this->_object->getType() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'salty', $this->_object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->_object->setLabel('bitter');
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 'bitter', $this->_object->getLabel() );
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

	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['product.tag.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['product.tag.siteid'] );
		$this->assertEquals( $this->_object->getTypeId(), $arrayObject['product.tag.typeid'] );
		$this->assertEquals( $this->_object->getLanguageId(), $arrayObject['product.tag.languageid'] );
		$this->assertEquals( $this->_object->getType(), $arrayObject['product.tag.type'] );
		$this->assertEquals( $this->_object->getLabel(), $arrayObject['product.tag.label'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['product.tag.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['product.tag.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['product.tag.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}
}
