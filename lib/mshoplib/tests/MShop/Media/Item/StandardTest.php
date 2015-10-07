<?php

namespace Aimeos\MShop\Media\Item;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->values = array(
			'id' => 1,
			'siteid' => 123,
			'typeid' => 2,
			'type' => 'category',
			'domain' => 'test_dom',
			'label' => 'testPicture',
			'mimetype' => 'image/jpeg',
			'url' => 'http://www.url.com/test.jpg',
			'preview' => '/directory/test.jpg',
			'status' => 6,
			'langid' => 'de',
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Media\Item\Standard( $this->values );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
	}


	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'test_dom', $this->object->getDomain() );
	}


	public function testSetDomain()
	{
		$this->object->setDomain( null );
		$this->assertEquals( null, $this->object->getDomain() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'category', $this->object->getType() );
	}


	public function testGetTypeId()
	{
		$this->assertEquals( 2, $this->object->getTypeId() );
	}


	public function testSetTypeId()
	{
		$this->object->setTypeId( 3 );
		$this->assertEquals( 3, $this->object->getTypeId() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'testPicture', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->object->setLabel( 'newPicture' );
		$this->assertEquals( 'newPicture', $this->object->getLabel() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetLanguageId()
	{
		$this->assertEquals( 'de', $this->object->getLanguageId() );
	}


	public function testSetLanguageId()
	{
		$this->object->setLanguageId( 'en' );
		$this->assertEquals( 'en', $this->object->getLanguageId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetLanguageIdInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setLanguageId( 'EN' );
	}


	public function testGetMimeType()
	{
		$this->assertEquals( 'image/jpeg', $this->object->getMimeType() );
	}


	public function testSetMimeType()
	{
		$this->object->setMimeType( 'image/png' );
		$this->assertEquals( 'image/png', $this->object->getMimeType() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testSetMimeTypeNoSlash()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Media\\Exception' );
		$this->object->setMimeType( 'image' );
	}


	public function testSetMimeTypeInvalidCategory()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Media\\Exception' );
		$this->object->setMimeType( 'image+audio/test' );
	}


	public function testGetUrl()
	{
		$this->assertEquals( 'http://www.url.com/test.jpg', $this->object->getUrl() );
	}


	public function testSetUrl()
	{
		$this->object->setUrl( '/pictures/category.jpg' );
		$this->assertEquals( '/pictures/category.jpg', $this->object->getUrl() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetPreview()
	{
		$this->assertEquals( '/directory/test.jpg', $this->object->getPreview() );
	}


	public function testSetPreview()
	{
		$this->object->setPreview( '/pictures/category.jpg' );
		$this->assertEquals( '/pictures/category.jpg', $this->object->getPreview() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 6, $this->object->getStatus() );
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


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Media\Item\Standard();

		$list = array(
			'media.id' => 1,
			'media.domain' => 'product',
			'media.label' => 'test item',
			'media.languageid' => 'de',
			'media.typeid' => 2,
			'media.mimetype' => 'image/jpeg',
			'media.preview' => 'preview.jpg',
			'media.url' => 'image.jpg',
			'media.status' => 0,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['media.id'], $item->getId() );
		$this->assertEquals( $list['media.domain'], $item->getDomain() );
		$this->assertEquals( $list['media.label'], $item->getLabel() );
		$this->assertEquals( $list['media.languageid'], $item->getLanguageId() );
		$this->assertEquals( $list['media.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['media.mimetype'], $item->getMimetype() );
		$this->assertEquals( $list['media.preview'], $item->getPreview() );
		$this->assertEquals( $list['media.url'], $item->getUrl() );
		$this->assertEquals( $list['media.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['media.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['media.siteid'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['media.domain'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['media.label'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['media.languageid'] );
		$this->assertEquals( $this->object->getMimeType(), $arrayObject['media.mimetype'] );
		$this->assertEquals( $this->object->getTypeId(), $arrayObject['media.typeid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['media.type'] );
		$this->assertEquals( $this->object->getUrl(), $arrayObject['media.url'] );
		$this->assertEquals( $this->object->getPreview(), $arrayObject['media.preview'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['media.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['media.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['media.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['media.editor'] );
	}

}