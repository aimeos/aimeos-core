<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Test_Item_Default.
 */
class MShop_Text_Item_DefaultTest extends MW_Unittest_Testcase
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
			'id' => 10,
			'siteid' => 99,
			'langid' => 'de',
			'typeid' => 1,
			'label' => 'unittest label',
			'domain' => 'product',
			'content' => 'unittest text',
			'status' => 2,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Text_Item_Default( $this->_values );
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


	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}


	public function testGetId()
	{
		$this->assertEquals( '10', $this->_object->getId() );
	}


	public function testSetId()
	{
		$this->_object->setId( null );
		$this->assertNull( $this->_object->getId() );

		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}


	public function testGetTypeId()
	{
		$this->assertEquals( 1, $this->_object->getTypeId() );
	}


	public function testSetTypeId()
	{
		$this->_object->setTypeId( 2 );
		$this->assertEquals( 2, $this->_object->getTypeId() );

		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest label', $this->_object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->_object->setLabel( 'unittest set label' );
		$this->assertEquals( 'unittest set label', $this->_object->getLabel() );

		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'product', $this->_object->getDomain() );
	}


	public function testSetDomain()
	{
		$this->_object->setDomain( 'catalog' );
		$this->assertEquals( 'catalog', $this->_object->getDomain() );

		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetContent()
	{
		$this->assertEquals( 'unittest text', $this->_object->getContent() );
	}


	public function testSetContent()
	{
		$this->_object->setContent( 'unit test text' );
		$this->assertEquals( 'unit test text', $this->_object->getContent() );

		$this->_object->setContent( mb_convert_encoding( '&#x0630;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ) );
		$this->assertEquals( 'ذرز', $this->_object->getContent() );

		$this->_object->setContent( mb_convert_encoding( '&#x27144;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ) );
		$this->assertEquals( mb_convert_encoding( '&#x27144;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ), $this->_object->getContent() );

		$this->assertTrue( $this->_object->isModified() );
	}


	public function testSetContentUtf8Invalid()
	{
		$this->_object->setContent( chr( 0x96 ) . 'укгезәөшөхзәхөшк2049һһлдябчсячмииюсит.июбҗрарэ' );
		$this->assertEquals( 'укгезәөшөхзәхөшк2049һһлдябчсячмииюсит.июбҗрарэ', $this->_object->getContent() );

		$this->_object->setContent( mb_convert_encoding( '&#xD800;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ) );
		$this->assertEquals( mb_convert_encoding( '&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ), $this->_object->getContent() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 2, $this->_object->getStatus() );
	}


	public function testSetStatus()
	{
		$this->_object->setStatus( 0 );
		$this->assertEquals( 0, $this->_object->getStatus() );

		$this->assertTrue( $this->_object->isModified() );
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
		$item = new MShop_Text_Item_Default();

		$list = array(
			'text.id' => 1,
			'text.typeid' => 2,
			'text.languageid' => 'de',
			'text.label' => 'test item',
			'text.domain' => 'product',
			'text.content' => 'test content',
			'text.status' => 0,
		);

		$unknown = $item->fromArray($list);

		$this->assertEquals(array(), $unknown);

		$this->assertEquals($list['text.id'], $item->getId());
		$this->assertEquals($list['text.typeid'], $item->getTypeId());
		$this->assertEquals($list['text.languageid'], $item->getLanguageId());
		$this->assertEquals($list['text.label'], $item->getLabel());
		$this->assertEquals($list['text.domain'], $item->getDomain());
		$this->assertEquals($list['text.content'], $item->getContent());
		$this->assertEquals($list['text.status'], $item->getStatus());
	}


	public function testToArray()
	{
		$data = $this->_object->toArray();

		$this->assertEquals( $this->_object->getId(), $data['text.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $data['text.siteid'] );
		$this->assertEquals( $this->_object->getLanguageId(), $data['text.languageid'] );
		$this->assertEquals( $this->_object->getTypeId(), $data['text.typeid'] );
		$this->assertEquals( $this->_object->getLabel(), $data['text.label'] );
		$this->assertEquals( $this->_object->getDomain(), $data['text.domain'] );
		$this->assertEquals( $this->_object->getContent(), $data['text.content'] );
		$this->assertEquals( $this->_object->getStatus(), $data['text.status'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $data['text.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $data['text.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $data['text.editor'] );
	}


	public function testGetLanguageId()
	{
		$this->assertEquals( 'de', $this->_object->getLanguageId() );
	}


	public function testSetLanguageId()
	{
		$this->_object->setLanguageId( 'en' );
		$this->assertEquals( 'en', $this->_object->getLanguageId() );

		$this->setExpectedException('MShop_Exception');
		$this->_object->setLanguageId(0);
	}


	public function testSetLanguageIdInvalid()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->setLanguageId(0);
	}
}
