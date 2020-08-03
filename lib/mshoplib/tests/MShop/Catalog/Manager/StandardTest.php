<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Catalog\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Catalog\Manager\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testCreateItem()
	{
		$item = $this->object->createItem();

		$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $item );
		$this->assertEquals( \TestHelperMShop::getContext()->getLocale()->getSiteId(), $item->getNode()->siteid );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->createSearch() );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->createSearch( true ) );
	}


	public function testDeleteItems()
	{
		$this->expectException( 'Aimeos\MW\Tree\Exception' );
		$this->object->deleteItems( array( -1 ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'catalog', $result );
		$this->assertContains( 'catalog/lists', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testSearchItems()
	{
		$item = $this->object->findItem( 'cafe', ['product'] );

		if( ( $listItem = $item->getListItems( 'product', 'promotion' )->first() ) === null ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'catalog.id', null );
		$expr[] = $search->compare( '!=', 'catalog.siteid', null );
		$expr[] = $search->compare( '==', 'catalog.code', 'cafe' );
		$expr[] = $search->compare( '==', 'catalog.level', 2 );
		$expr[] = $search->compare( '==', 'catalog.left', 3 );
		$expr[] = $search->compare( '==', 'catalog.right', 4 );
		$expr[] = $search->compare( '==', 'catalog.status', 1 );
		$expr[] = $search->compare( '==', 'catalog.url', 'kaffee' );
		$expr[] = $search->compare( '==', 'catalog.label', 'Kaffee' );
		$expr[] = $search->compare( '~=', 'catalog.config', '{' );
		$expr[] = $search->compare( '>=', 'catalog.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.editor', $this->editor );
		$expr[] = $search->compare( '>=', 'catalog.target', '' );

		$param = ['product', 'promotion', $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->createFunction( 'catalog:has', $param ), null );

		$param = ['product', 'promotion'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'catalog:has', $param ), null );

		$param = ['product'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'catalog:has', $param ), null );


		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$items = $this->object->searchItems( $search, [], $total )->toArray();

		$this->assertEquals( 1, $total );
		$this->assertEquals( 1, count( $items ) );

		foreach( $items as $itemId => $item ) {
			$this->assertInstanceOf( \Aimeos\MShop\Catalog\Item\Iface::class, $item );
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsTranslation()
	{
		$search = $this->object->createSearch();

		$conditions = array(
			$search->compare( '==', 'catalog.label', 'Misc' ),
			$search->compare( '==', 'catalog.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$item = $this->object->searchItems( $search, array( 'text' ) )->first();

		$this->assertEquals( 'Sonstiges', $item->getName() );
	}


	public function testFindItem()
	{
		$item = $this->object->findItem( 'root' );

		$this->assertEquals( 'root', $item->getCode() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'catalog.label', 'Root' ),
			$search->compare( '==', 'catalog.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$item = $this->object->searchItems( $search, array( 'text' ) )->first();

		$testItem = $this->object->getItem( $item->getId() );

		$this->assertEquals( \TestHelperMShop::getContext()->getLocale()->getSiteId(), $testItem->getSiteId() );
		$this->assertEquals( $item->getId(), $testItem->getId() );
		$this->assertEquals( 'Root', $testItem->getLabel() );
		$this->assertEquals( 'Root', $testItem->getName() );
		$this->assertEquals( 'home', $testItem->getUrl() );
		$this->assertEquals( 1, $testItem->getStatus() );
	}


	public function testGetTree()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'catalog.code', 'root' ),
			$search->compare( '==', 'catalog.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$item = $this->object->searchItems( $search )->first();

		$rootItem = $this->object->getTree( $item->getId(), array( 'text' ), \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );
		$categoryItem = $rootItem->getChild( 0 );
		$miscItem = $categoryItem->getChild( 2 );

		$this->assertEquals( \TestHelperMShop::getContext()->getLocale()->getSiteId(), $miscItem->getSiteId() );
		$this->assertEquals( 'Misc', $miscItem->getLabel() );
		$this->assertEquals( 'Sonstiges', $miscItem->getName() );
	}


	public function testGetTreeWithConditions()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'catalog.code', array( 'root', 'categories' ) ),
			$search->compare( '==', 'catalog.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search )->toArray();
		$parentIds = [];

		foreach( $items as $item ) {
			$parentIds[] = $item->getId();
		}

		if( count( $parentIds ) != 2 ) {
			throw new \RuntimeException( 'Not all categories found!' );
		}

		$parentIds[] = 0;

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.parentid', $parentIds ) );

		$tree = $this->object->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE, $search );

		$categorycat = $tree->getChild( 0 );
		$groupcat = $tree->getChild( 1 );
		$groupcatChildren = $groupcat->getChildren();
		$categorycatChildren = $categorycat->getChildren();
		$cafecat = $categorycat->getChild( 0 );

		$caffein = $this->object->createItem();
		$caffein->setCode( 'caffein' );
		$caffein->setLabel( 'Caffein' );

		$this->object->insertItem( $caffein, $cafecat->getId() );
		$this->object->deleteItem( $caffein->getId() );

		$this->assertEquals( 0, $tree->getNode()->parentid );
		$this->assertEquals( 'categories', $categorycat->getCode() );
		$this->assertEquals( 'group', $groupcat->getCode() );
		$this->assertEquals( $tree->getId(), $categorycat->getNode()->parentid );
		$this->assertEquals( $tree->getId(), $groupcat->getNode()->parentid );
		$this->assertEquals( $categorycat->getId(), $cafecat->getNode()->parentid );
		$this->assertEquals( $cafecat->getId(), $caffein->getNode()->parentid );
		$this->assertEquals( [], $groupcatChildren->toArray() );
		$this->assertEquals( 3, count( $categorycatChildren ) );
	}


	public function testGetTreeWithFilter()
	{
		$index = 1;
		$this->object->addFilter( \Aimeos\MShop\Common\Item\ListRef\Iface::class, function( $item ) use ( &$index ) {
			return $index++ % 2 ? $item : null;
		} );

		$tree = $this->object->getTree();
		$groupItem = $tree->getChild( 0 );

		$this->assertEquals( 1, count( $tree->getChildren() ) );
		$this->assertEquals( 'Groups', $groupItem->getLabel() );
		$this->assertEquals( 1, count( $groupItem->getChildren() ) );
		$this->assertEquals( 'Internet', $groupItem->getChild( 0 )->getLabel() );
	}


	public function testGetPath()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'catalog.label', 'Kaffee' ),
			$search->compare( '==', 'catalog.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$item = $this->object->searchItems( $search, array( 'text' ) )->first();

		$items = $this->object->getPath( $item->getId() );
		$expected = array( 'Root', 'Categories', 'Kaffee' );

		foreach( $items as $item ) {
			$this->assertEquals( array_shift( $expected ), $item->getLabel() );
		}

		$this->assertEquals( 0, count( $expected ) );
		$this->assertEquals( 'home', $items->getUrl()->first() );
	}


	public function testSaveInsertMoveDeleteItem()
	{
		$item = $this->object->findItem( 'root', ['text'] );

		$parentId = $item->getId();
		$item->setId( null );
		$item->setLabel( 'Root child' );
		$item->setCode( 'new Root child' );
		$resultInsert = $this->object->insertItem( $item, $parentId );
		$this->object->moveItem( $item->getId(), $parentId, $parentId );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setStatus( true );
		$resultSaved = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getTarget(), $itemSaved->getTarget() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getTarget(), $itemUpd->getTarget() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultInsert );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getItem( $item->getId() );
	}


	public function testSaveChildren()
	{
		$item = $this->object->findItem( 'cafe', ['product'] )->setCode( 'ccafe' )->setId( null );
		$child = $this->object->findItem( 'misc', ['product'] )->setCode( 'cmisc' )->setId( null );

		$item = $this->object->insertItem( $item->addChild( $child ) );
		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 1, count( $item->getChildren() ) );
		$this->assertEquals( 3, count( $item->getListItems() ) );
		$this->assertEquals( 3, count( $item->getChild( 0 )->getListItems() ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->findItem( 'ccafe' );
	}


	public function testGetSubManager()
	{
		$target = \Aimeos\MShop\Common\Manager\Iface::class;
		$this->assertInstanceOf( $target, $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( $target, $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'lists', 'unknown' );
	}
}
