<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Text\Item;


/**
 * Test class for \Aimeos\MShop\Test\Item\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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
			'text.id' => 10,
			'text.siteid' => 99,
			'text.languageid' => 'de',
			'text.typeid' => 1,
			'text.label' => 'unittest label',
			'text.domain' => 'product',
			'text.content' => 'unittest text',
			'text.status' => 2,
			'text.mtime' => '2011-01-01 00:00:02',
			'text.ctime' => '2011-01-01 00:00:01',
			'text.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Text\Item\Standard( $this->values );
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


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetId()
	{
		$this->assertEquals( '10', $this->object->getId() );
	}


	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertNull( $this->object->getId() );

		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetTypeId()
	{
		$this->assertEquals( 1, $this->object->getTypeId() );
	}


	public function testSetTypeId()
	{
		$this->object->setTypeId( 2 );
		$this->assertEquals( 2, $this->object->getTypeId() );

		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest label', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->object->setLabel( 'unittest set label' );
		$this->assertEquals( 'unittest set label', $this->object->getLabel() );

		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'product', $this->object->getDomain() );
	}


	public function testSetDomain()
	{
		$this->object->setDomain( 'catalog' );
		$this->assertEquals( 'catalog', $this->object->getDomain() );

		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetContent()
	{
		$this->assertEquals( 'unittest text', $this->object->getContent() );
	}


	public function testSetContent()
	{
		$this->object->setContent( 'unit test text' );
		$this->assertEquals( 'unit test text', $this->object->getContent() );

		$this->object->setContent( mb_convert_encoding( '&#x0630;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ) );
		$this->assertEquals( 'ذرز', $this->object->getContent() );

		$this->object->setContent( mb_convert_encoding( '&#x27144;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ) );
		$this->assertEquals( mb_convert_encoding( '&#x27144;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ), $this->object->getContent() );

		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetContentUtf8Invalid()
	{
		$this->object->setContent( chr( 0x96 ) . 'укгезәөшөхзәхөшк2049һһлдябчсячмииюсит.июбҗрарэ' );
		$this->assertEquals( 'укгезәөшөхзәхөшк2049һһлдябчсячмииюсит.июбҗрарэ', $this->object->getContent() );

		$this->object->setContent( mb_convert_encoding( '&#xD800;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ) );
		$this->assertEquals( mb_convert_encoding( '&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ), $this->object->getContent() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 2, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$this->object->setStatus( 0 );
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
		$this->assertEquals( 'text', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Text\Item\Standard();

		$list = array(
			'text.id' => 1,
			'text.typeid' => 2,
			'text.languageid' => 'de',
			'text.label' => 'test item',
			'text.domain' => 'product',
			'text.content' => 'test content',
			'text.status' => 0,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['text.id'], $item->getId() );
		$this->assertEquals( $list['text.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['text.languageid'], $item->getLanguageId() );
		$this->assertEquals( $list['text.label'], $item->getLabel() );
		$this->assertEquals( $list['text.domain'], $item->getDomain() );
		$this->assertEquals( $list['text.content'], $item->getContent() );
		$this->assertEquals( $list['text.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$data = $this->object->toArray();

		$this->assertEquals( $this->object->getId(), $data['text.id'] );
		$this->assertEquals( $this->object->getSiteId(), $data['text.siteid'] );
		$this->assertEquals( $this->object->getLanguageId(), $data['text.languageid'] );
		$this->assertEquals( $this->object->getTypeId(), $data['text.typeid'] );
		$this->assertEquals( $this->object->getLabel(), $data['text.label'] );
		$this->assertEquals( $this->object->getDomain(), $data['text.domain'] );
		$this->assertEquals( $this->object->getContent(), $data['text.content'] );
		$this->assertEquals( $this->object->getStatus(), $data['text.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $data['text.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $data['text.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $data['text.editor'] );
	}


	public function testGetLanguageId()
	{
		$this->assertEquals( 'de', $this->object->getLanguageId() );
	}


	public function testSetLanguageId()
	{
		$this->object->setLanguageId( 'en' );
		$this->assertEquals( 'en', $this->object->getLanguageId() );
	}


	public function testSetLanguageIdInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setLanguageId( '10' );
	}
}
