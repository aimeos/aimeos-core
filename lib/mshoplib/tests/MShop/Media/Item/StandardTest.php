<?php

namespace Aimeos\MShop\Media\Item;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
			'media.id' => 1,
			'media.siteid' => 123,
			'media.typeid' => 2,
			'media.type' => 'category',
			'media.typename' => 'Category',
			'media.domain' => 'test_dom',
			'media.label' => 'testPicture',
			'media.mimetype' => 'image/jpeg',
			'media.url' => 'http://www.url.com/test.jpg',
			'media.preview' => '/directory/test.jpg',
			'media.status' => 6,
			'media.languageid' => 'de',
			'media.mtime' => '2011-01-01 00:00:02',
			'media.ctime' => '2011-01-01 00:00:01',
			'media.editor' => 'unitTestUser'
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
		$return = $this->object->setId( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Media\Item\Iface', $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'test_dom', $this->object->getDomain() );
	}


	public function testSetDomain()
	{
		$return = $this->object->setDomain( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Media\Item\Iface', $return );
		$this->assertEquals( null, $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'category', $this->object->getType() );
	}


	public function testGetTypeName()
	{
		$this->assertEquals( 'Category', $this->object->getTypeName() );
	}


	public function testGetTypeId()
	{
		$this->assertEquals( 2, $this->object->getTypeId() );
	}


	public function testSetTypeId()
	{
		$return = $this->object->setTypeId( 3 );

		$this->assertInstanceOf( '\Aimeos\MShop\Media\Item\Iface', $return );
		$this->assertEquals( 3, $this->object->getTypeId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'testPicture', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'newPicture' );

		$this->assertInstanceOf( '\Aimeos\MShop\Media\Item\Iface', $return );
		$this->assertEquals( 'newPicture', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLanguageId()
	{
		$this->assertEquals( 'de', $this->object->getLanguageId() );
	}


	public function testSetLanguageId()
	{
		$return = $this->object->setLanguageId( 'en' );

		$this->assertInstanceOf( '\Aimeos\MShop\Media\Item\Iface', $return );
		$this->assertEquals( 'en', $this->object->getLanguageId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetLanguageIdInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setLanguageId( '00' );
	}


	public function testGetMimeType()
	{
		$this->assertEquals( 'image/jpeg', $this->object->getMimeType() );
	}


	public function testSetMimeType()
	{
		$return = $this->object->setMimeType( 'image/png' );

		$this->assertInstanceOf( '\Aimeos\MShop\Media\Item\Iface', $return );
		$this->assertEquals( 'image/png', $this->object->getMimeType() );
		$this->assertTrue( $this->object->isModified() );
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
		$return = $this->object->setUrl( '/pictures/category.jpg' );

		$this->assertInstanceOf( '\Aimeos\MShop\Media\Item\Iface', $return );
		$this->assertEquals( '/pictures/category.jpg', $this->object->getUrl() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPreview()
	{
		$this->assertEquals( '/directory/test.jpg', $this->object->getPreview() );
	}


	public function testSetPreview()
	{
		$return = $this->object->setPreview( '/pictures/category.jpg' );

		$this->assertInstanceOf( '\Aimeos\MShop\Media\Item\Iface', $return );
		$this->assertEquals( '/pictures/category.jpg', $this->object->getPreview() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 6, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( '\Aimeos\MShop\Media\Item\Iface', $return );
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
		$this->assertEquals( 'media', $this->object->getResourceType() );
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
			'media.type' => 'test',
			'media.typename' => 'Test',
			'media.mimetype' => 'image/jpeg',
			'media.preview' => 'preview.jpg',
			'media.url' => 'image.jpg',
			'media.status' => 0,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['media.id'], $item->getId() );
		$this->assertEquals( $list['media.domain'], $item->getDomain() );
		$this->assertEquals( $list['media.label'], $item->getLabel() );
		$this->assertEquals( $list['media.languageid'], $item->getLanguageId() );
		$this->assertEquals( $list['media.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['media.mimetype'], $item->getMimetype() );
		$this->assertEquals( $list['media.preview'], $item->getPreview() );
		$this->assertEquals( $list['media.url'], $item->getUrl() );
		$this->assertEquals( $list['media.status'], $item->getStatus() );
		$this->assertNull( $item->getSiteId() );
		$this->assertNull( $item->getTypeName() );
		$this->assertNull( $item->getType() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['media.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['media.siteid'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['media.domain'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['media.label'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['media.languageid'] );
		$this->assertEquals( $this->object->getMimeType(), $arrayObject['media.mimetype'] );
		$this->assertEquals( $this->object->getTypeName(), $arrayObject['media.typename'] );
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
