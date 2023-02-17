<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Media\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $editor = '';


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->editor = $this->context->editor();

		$this->object = new \Aimeos\MShop\Media\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );

		$item = $this->object->create()->setUrl( 'test.jpg' )->setPreviews( [1 => 'test-1.jpg'] )->setId( -1 );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [$item] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'media', $result );
		$this->assertContains( 'media/lists', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testCreateItem()
	{
		$item = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $item );
	}


	public function testCreateItemType()
	{
		$item = $this->object->create( ['media.type' => 'default'] );
		$this->assertEquals( 'default', $item->getType() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter() );
	}


	public function testCopy()
	{
		$fsm = $this->getMockBuilder( \Aimeos\Base\Filesystem\Manager\Standard::class )
			->onlyMethods( array( 'get' ) )
			->disableOriginalConstructor()
			->getMock();

		$fs = $this->getMockBuilder( \Aimeos\Base\Filesystem\Standard::class )
			->onlyMethods( array( 'has', 'copy' ) )
			->disableOriginalConstructor()
			->getMock();

		$fsm->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $fs ) );

		$fs->expects( $this->exactly( 2 ) )->method( 'has' )
			->will( $this->returnValue( true ) );

		$fs->expects( $this->exactly( 2 ) )->method( 'copy' );

		$this->context->setFilesystemManager( $fsm );

		$item = $this->object->create()->setPreview( 'test' )->setUrl( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $this->object->copy( $item ) );
	}


	public function testSearchItem()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'media.url', 'prod_266x221/198_prod_266x221.jpg' ) );
		$item = $this->object->search( $search, ['attribute'] )->first();

		if( $item && ( $listItem = $item->getListItems( 'attribute', 'option' )->first() ) === null ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'media.id', null );
		$expr[] = $search->compare( '!=', 'media.siteid', null );
		$expr[] = $search->compare( '==', 'media.languageid', 'de' );
		$expr[] = $search->compare( '==', 'media.type', 'slideshow' );
		$expr[] = $search->compare( '==', 'media.domain', 'product' );
		$expr[] = $search->compare( '==', 'media.filesystem', 'fs-media' );
		$expr[] = $search->compare( '==', 'media.label', 'prod_266x221/198_prod_266x221.jpg' );
		$expr[] = $search->compare( '==', 'media.url', 'prod_266x221/198_prod_266x221.jpg' );
		$expr[] = $search->compare( '=~', 'media.preview', '{' );
		$expr[] = $search->compare( '==', 'media.mimetype', 'image/jpeg' );
		$expr[] = $search->compare( '==', 'media.status', 1 );
		$expr[] = $search->compare( '>=', 'media.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'media.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'media.editor', $this->editor );

		$param = ['attribute', 'option', $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->make( 'media:has', $param ), null );

		$param = ['attribute', 'option'];
		$expr[] = $search->compare( '!=', $search->make( 'media:has', $param ), null );

		$param = ['attribute'];
		$expr[] = $search->compare( '!=', $search->make( 'media:has', $param ), null );

		$param = ['copyright', 'de', 'ich, 2019'];
		$expr[] = $search->compare( '!=', $search->make( 'media:prop', $param ), null );

		$param = ['copyright', 'de'];
		$expr[] = $search->compare( '!=', $search->make( 'media:prop', $param ), null );

		$param = ['copyright'];
		$expr[] = $search->compare( '!=', $search->make( 'media:prop', $param ), null );

		$total = 0;
		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemBase()
	{
		//search with base criteria
		$search = $this->object->filter( true );
		$conditions = array(
			$search->compare( '==', 'media.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 4 );

		$total = 0;
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 4, count( $results ) );
		$this->assertEquals( 16, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}

	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'media.label', 'path/to/folder/example1.jpg' ),
			$search->compare( '==', 'media.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$item = $this->object->search( $search, ['media/property'] )->first();

		$this->assertEquals( $item, $this->object->get( $item->getId(), ['media/property'] ) );
		$this->assertEquals( 2, count( $item->getPropertyItems() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '~=', 'media.label', 'example' ),
			$search->compare( '==', 'media.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$item = $this->object->search( $search )->first();

		$item->setId( null );
		$item->setLanguageId( 'de' );
		$item->setDomain( 'test_dom' );
		$item->setLabel( 'test' );
		$item->setMimeType( 'image/jpeg' );
		$item->setUrl( 'test.jpg' );
		$item->setPreview( 'xxxtest-preview.jpg' );

		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setPreview( 'test-preview.jpg' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $item->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getFileSystem(), $itemSaved->getFileSystem() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getMimeType(), $itemSaved->getMimeType() );
		$this->assertEquals( $item->getUrl(), $itemSaved->getUrl() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( 0, strncmp( $item->getPreview(), $itemSaved->getPreview(), 19 ) );

		$this->assertEquals( $this->editor, $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getFileSystem(), $itemUpd->getFileSystem() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getMimeType(), $itemUpd->getMimeType() );
		$this->assertEquals( $itemExp->getUrl(), $itemUpd->getUrl() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( 0, strncmp( $itemExp->getPreview(), $itemUpd->getPreview(), 16 ) );

		$this->assertEquals( $this->editor, $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $item->getId() );
	}


	public function testGetSavePropertyItems()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'media.label', 'path/to/folder/example1.jpg' ) );
		$item = $this->object->search( $search, ['media/property'] )->first();

		$item->setId( null )->setLabel( 'path/to/folder/example1-1.jpg' );
		$this->object->save( $item );

		$search->setConditions( $search->compare( '==', 'media.label', 'path/to/folder/example1-1.jpg' ) );
		$item2 = $this->object->search( $search, ['media/property'] )->first();

		$this->object->delete( $item->getId() );

		$this->assertEquals( 2, count( $item->getPropertyItems() ) );
		$this->assertEquals( 2, count( $item2->getPropertyItems() ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'lists', 'unknown' );
	}


	public function testScale()
	{
		copy( __DIR__ . '/_testfiles/test.gif', 'tmp/test.gif' );

		$object = $this->getMockBuilder( \Aimeos\MShop\Media\Manager\Standard::class )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['getContent', 'store'] )
			->getMock();

		$object->expects( $this->once() )->method( 'getContent' )
			->will( $this->returnValue( file_get_contents( __DIR__ . '/_testfiles/test.gif' ) ) );

		$object->expects( $this->any() )->method( 'store' );

		$item = $this->object->create()->setUrl( 'test.gif' )
			->setMimeType( 'image/gif' )->setDomain( 'product' );

		$result = $object->scale( $item, true );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $result );
		$this->assertEquals( 'test.gif', $result->getUrl() );
		$this->assertNotEquals( '', $result->getPreview() );
	}


	public function testGetContent()
	{
		$dest = dirname( __DIR__, 3 ) . '/tmp/';
		is_dir( $dest ) ?: mkdir( $dest, 0755, true );
		copy( __DIR__ . '/_testfiles/test.gif', $dest . 'test.gif' );

		$result = $this->access( 'getContent' )->invokeArgs( $this->object, ['test.gif'] );

		$this->assertNotEquals( '', $result );
	}


	public function testGetContentHttp()
	{
		$url = 'https://aimeos.org/fileadmin/logos/favicon.png';
		$result = $this->access( 'getContent' )->invokeArgs( $this->object, [$url] );

		$this->assertNotEquals( '', $result );
	}


	public function testGetContentException()
	{
		$this->expectException( \Aimeos\MShop\Media\Exception::class );
		$this->access( 'getContent' )->invokeArgs( $this->object, [''] );
	}


	public function testGetFile()
	{
		$dest = dirname( __DIR__, 3 ) . '/tmp/';
		is_dir( $dest ) ?: mkdir( $dest, 0755, true );
		copy( __DIR__ . '/_testfiles/test.gif', $dest . 'test.gif' );

		$result = $this->access( 'getFile' )->invokeArgs( $this->object, ['test.gif'] );
		$this->assertInstanceOf( \Aimeos\MW\Media\Iface::class, $result );
	}


	public function testGetMime()
	{
		$file = \Aimeos\MW\Media\Factory::get( __DIR__ . '/_testfiles/test.png' );

		$result = $this->access( 'getMime' )->invokeArgs( $this->object, array( $file, 'files' ) );
		$this->assertEquals( true, in_array( $result, ['image/webp', 'image/png'] ) );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Media\Manager\Standard::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
