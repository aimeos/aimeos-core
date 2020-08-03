<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Media\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object = null;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = \Aimeos\MShop\Media\Manager\Factory::create( \TestHelperMShop::getContext() );
	}

	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->deleteItems( [-1] ) );
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
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $item );
	}


	public function testCreateItemType()
	{
		$item = $this->object->createItem( ['media.type' => 'default'] );
		$this->assertEquals( 'default', $item->getType() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->createSearch() );
	}


	public function testSearchItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', 'prod_266x221/198_prod_266x221.jpg' ) );
		$item = $this->object->searchItems( $search, ['attribute'] )->first();

		if( $item && ( $listItem = $item->getListItems( 'attribute', 'option' )->first() ) === null ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'media.id', null );
		$expr[] = $search->compare( '!=', 'media.siteid', null );
		$expr[] = $search->compare( '==', 'media.languageid', 'de' );
		$expr[] = $search->compare( '==', 'media.type', 'prod_266x221' );
		$expr[] = $search->compare( '==', 'media.domain', 'product' );
		$expr[] = $search->compare( '==', 'media.label', 'prod_266x221/198_prod_266x221.jpg' );
		$expr[] = $search->compare( '==', 'media.url', 'prod_266x221/198_prod_266x221.jpg' );
		$expr[] = $search->compare( '=~', 'media.preview', '{' );
		$expr[] = $search->compare( '==', 'media.mimetype', 'image/jpeg' );
		$expr[] = $search->compare( '==', 'media.status', 1 );
		$expr[] = $search->compare( '>=', 'media.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'media.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'media.editor', $this->editor );

		$param = ['attribute', 'option', $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->createFunction( 'media:has', $param ), null );

		$param = ['attribute', 'option'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'media:has', $param ), null );

		$param = ['attribute'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'media:has', $param ), null );

		$param = ['copyright', 'de', 'ich, 2019'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'media:prop', $param ), null );

		$param = ['copyright', 'de'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'media:prop', $param ), null );

		$param = ['copyright'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'media:prop', $param ), null );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemBase()
	{
		//search with base criteria
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'media.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 4 );

		$total = 0;
		$results = $this->object->searchItems( $search, [], $total )->toArray();

		$this->assertEquals( 4, count( $results ) );
		$this->assertEquals( 17, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}

	public function testGetItem()
	{
		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'media.label', 'path/to/folder/example1.jpg' ),
			$search->compare( '==', 'media.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$item = $this->object->searchItems( $search, ['media/property'] )->first();

		$this->assertEquals( $item, $this->object->getItem( $item->getId(), ['media/property'] ) );
		$this->assertEquals( 2, count( $item->getPropertyItems() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'media.label', 'example' ),
			$search->compare( '==', 'media.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$item = $this->object->searchItems( $search )->first();

		$item->setId( null );
		$item->setLanguageId( 'de' );
		$item->setDomain( 'test_dom' );
		$item->setLabel( 'test' );
		$item->setMimeType( 'image/jpeg' );
		$item->setUrl( 'test.jpg' );
		$item->setPreview( 'xxxtest-preview.jpg' );

		$resultSaved = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setPreview( 'test-preview.jpg' );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $item->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getMimeType(), $itemSaved->getMimeType() );
		$this->assertEquals( $item->getUrl(), $itemSaved->getUrl() );
		$this->assertEquals( $item->getPreview(), $itemSaved->getPreview() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getMimeType(), $itemUpd->getMimeType() );
		$this->assertEquals( $itemExp->getUrl(), $itemUpd->getUrl() );
		$this->assertEquals( $itemExp->getPreview(), $itemUpd->getPreview() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getItem( $item->getId() );
	}


	public function testGetSavePropertyItems()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'media.label', 'path/to/folder/example1.jpg' ) );
		$item = $this->object->searchItems( $search, ['media/property'] )->first();

		$item->setId( null )->setLabel( 'path/to/folder/example1-1.jpg' );
		$this->object->saveItem( $item );

		$search->setConditions( $search->compare( '==', 'media.label', 'path/to/folder/example1-1.jpg' ) );
		$item2 = $this->object->searchItems( $search, ['media/property'] )->first();

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 2, count( $item->getPropertyItems() ) );
		$this->assertEquals( 2, count( $item2->getPropertyItems() ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'lists', 'unknown' );
	}
}
