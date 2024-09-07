<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Media\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'media.id' => 1,
			'media.siteid' => 123,
			'media.type' => 'category',
			'media.domain' => 'test_dom',
			'media.label' => 'testPicture',
			'media.mimetype' => 'image/jpeg',
			'media.filesystem' => 'fs-mimeicon',
			'media.url' => 'http://www.url.com/test.jpg',
			'media.preview' => [100 => 'directory/test.jpg', 200 => 'directory/test2.jpg'],
			'media.status' => 6,
			'media.languageid' => 'de',
			'media.mtime' => '2011-01-01 00:00:02',
			'media.ctime' => '2011-01-01 00:00:01',
			'media.editor' => 'unitTestUser',
			'.languageid' => 'de',
		);

		$this->object = new \Aimeos\MShop\Media\Item\Standard( 'media.', $this->values );
	}


	protected function tearDown() : void
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

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'test_dom', $this->object->getDomain() );
	}


	public function testSetDomain()
	{
		$return = $this->object->setDomain( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
		$this->assertEquals( 'test', $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'category', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'size' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
		$this->assertEquals( 'size', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetFileSystem()
	{
		$this->assertEquals( 'fs-mimeicon', $this->object->getFileSystem() );
	}


	public function testSetFileSystem()
	{
		$return = $this->object->setFileSystem( 'fs-test' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
		$this->assertEquals( 'fs-test', $this->object->getFileSystem() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'testPicture', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'newPicture' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
		$this->assertEquals( 'en', $this->object->getLanguageId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetLanguageIdInvalid()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setLanguageId( '00' );
	}


	public function testGetMimeType()
	{
		$this->assertEquals( 'image/jpeg', $this->object->getMimeType() );
	}


	public function testSetMimeType()
	{
		$return = $this->object->setMimeType( 'image/png' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
		$this->assertEquals( 'image/png', $this->object->getMimeType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetMimeTypeNoSlash()
	{
		$this->expectException( \Aimeos\MShop\Media\Exception::class );
		$this->object->setMimeType( 'image' );
	}


	public function testSetMimeTypeInvalidCategory()
	{
		$this->expectException( \Aimeos\MShop\Media\Exception::class );
		$this->object->setMimeType( 'image+audio/test' );
	}


	public function testGetUrl()
	{
		$this->assertEquals( 'http://www.url.com/test.jpg', $this->object->getUrl() );
	}


	public function testGetUrlVersion()
	{
		$this->object->setUrl( 'test.jpg' );
		$this->assertStringStartsWith( 'test.jpg?v=', $this->object->getUrl( true ) );
	}


	public function testSetUrl()
	{
		$return = $this->object->setUrl( null )->setUrl( '/pictures/category.jpg' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
		$this->assertEquals( '/pictures/category.jpg', $this->object->getUrl() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPreview()
	{
		$this->assertStringStartsWith( 'directory/test.jpg?v=', $this->object->getPreview() );
		$this->assertStringStartsWith( 'directory/test.jpg?v=', $this->object->getPreview( 100 ) );
		$this->assertStringStartsWith( 'directory/test2.jpg?v=', $this->object->getPreview( true ) );
		$this->assertStringStartsWith( 'directory/test2.jpg?v=', $this->object->getPreview( 150 ) );
		$this->assertStringStartsWith( 'directory/test2.jpg?v=', $this->object->getPreview( 250 ) );
	}


	public function testGetPreviews()
	{
		$this->assertEquals( [100 => 'directory/test.jpg', 200 => 'directory/test2.jpg'], $this->object->getPreviews() );
	}


	public function testGetPreviewsVersion()
	{
		$expected = [100 => 'directory/test.jpg?v=', 200 => 'directory/test2.jpg?v='];

		foreach( $this->object->getPreviews( true ) as $key => $path ) {
			$this->assertStringStartsWith( $expected[$key], $path );
		}
	}


	public function testSetPreview()
	{
		$return = $this->object->setPreview( '/pictures/category.jpg' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
		$this->assertStringStartsWith( '/pictures/category.jpg', $this->object->getPreview() );
		$this->assertEquals( [1 => '/pictures/category.jpg'], $this->object->getPreviews() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPreviews()
	{
		$return = $this->object->setPreviews( [1 => '/pictures/category.jpg'] );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
		$this->assertEquals( [1 => '/pictures/category.jpg'], $this->object->getPreviews() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 6, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $return );
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
		$this->assertEquals( 'unitTestUser', $this->object->editor() );
	}


	public function testGetName()
	{
		$this->assertEquals( 'testPicture', $this->object->getName() );
	}


	public function testGetNameProperties()
	{
		$values = ['media.property.value' => 'title', 'media.property.type' => 'name', '.languageid' => null];
		$propitems = [new \Aimeos\MShop\Common\Item\Property\Standard( 'media.property.', $values )];
		$object = new \Aimeos\MShop\Media\Item\Standard( 'media.', $this->values + ['.propitems' => $propitems] );

		$this->assertEquals( 'title', $object->getName() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'media', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Media\Item\Standard( 'media.' );

		$list = $entries = array(
			'media.id' => 1,
			'media.domain' => 'product',
			'media.label' => 'test item',
			'media.languageid' => 'de',
			'media.type' => 'test',
			'media.filesystem' => 'fs-test',
			'media.mimetype' => 'image/jpeg',
			'media.previews' => [1 => 'preview.jpg'],
			'media.preview' => 'preview.jpg',
			'media.url' => 'image.jpg',
			'media.status' => 0,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['media.id'], $item->getId() );
		$this->assertEquals( $list['media.domain'], $item->getDomain() );
		$this->assertEquals( $list['media.label'], $item->getLabel() );
		$this->assertEquals( $list['media.filesystem'], $item->getFileSystem() );
		$this->assertEquals( $list['media.languageid'], $item->getLanguageId() );
		$this->assertEquals( $list['media.type'], $item->getType() );
		$this->assertEquals( $list['media.mimetype'], $item->getMimetype() );
		$this->assertEquals( $list['media.previews'], $item->getPreviews() );
		$this->assertEquals( $list['media.preview'], $item->getPreview() );
		$this->assertEquals( $list['media.url'], $item->getUrl() );
		$this->assertEquals( $list['media.status'], $item->getStatus() );
		$this->assertEquals( '', $item->getSiteId() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['media.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['media.siteid'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['media.domain'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['media.label'] );
		$this->assertEquals( $this->object->getFileSystem(), $arrayObject['media.filesystem'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['media.languageid'] );
		$this->assertEquals( $this->object->getMimeType(), $arrayObject['media.mimetype'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['media.type'] );
		$this->assertEquals( $this->object->getUrl(), $arrayObject['media.url'] );
		$this->assertEquals( $this->object->getPreview(), $arrayObject['media.preview'] );
		$this->assertEquals( $this->object->getPreviews(), $arrayObject['media.previews'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['media.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['media.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['media.mtime'] );
		$this->assertEquals( $this->object->editor(), $arrayObject['media.editor'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setAvailable( false );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsAvailableOnStatus()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setStatus( 0 );
		$this->assertFalse( $this->object->isAvailable() );
		$this->object->setStatus( -1 );
		$this->assertFalse( $this->object->isAvailable() );
	}

}
