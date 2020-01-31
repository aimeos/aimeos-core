<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Product\Manager\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $editor = '';


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context, 'Standard' );
		$this->object = $productManager->getSubManager( 'lists', 'Standard' );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'product/lists', $result );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'product.lists.editor', 'core:lib/mshoplib' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->aggregate( $search, 'product.lists.domain' )->toArray();

		$this->assertEquals( 6, count( $result ) );
		$this->assertArrayHasKey( 'price', $result );
		$this->assertEquals( 22, $result['price'] );
	}


	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $item );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'product.lists.domain', 'text' ),
			$search->compare( '==', 'product.lists.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId() ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSaveUpdateDeleteItem()
	{
		$siteid = \TestHelperMShop::getContext()->getLocale()->getSiteId();

		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.lists.siteid', $siteid ),
			$search->compare( '==', 'product.lists.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item->setId( null );
		$item->setDomain( 'unittest' );
		$resultSaved = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest2' );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getRefId(), $itemSaved->getRefId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getRefId(), $itemUpd->getRefId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();
		$expr = array(
			$search->compare( '==', 'product.lists.domain', 'media' ),
			$search->compare( '==', 'product.lists.datestart', '2000-01-01 00:00:00' ),
			$search->compare( '==', 'product.lists.dateend', '2100-01-01 00:00:00' ),
			$search->compare( '==', 'product.lists.position', 1 ),
			$search->compare( '==', 'product.lists.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->searchItems( $search )->toArray();
		if( ( $listItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No list item found' );
		}


		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'product.lists.id', null );
		$expr[] = $search->compare( '!=', 'product.lists.siteid', null );
		$expr[] = $search->compare( '!=', 'product.lists.parentid', null );
		$expr[] = $search->compare( '==', 'product.lists.domain', 'media' );
		$expr[] = $search->compare( '==', 'product.lists.type', 'default' );
		$expr[] = $search->compare( '==', 'product.lists.refid', $listItem->getRefId() );
		$expr[] = $search->compare( '==', 'product.lists.datestart', '2000-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.lists.dateend', '2100-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'product.lists.config', null );
		$expr[] = $search->compare( '==', 'product.lists.position', 1 );
		$expr[] = $search->compare( '==', 'product.lists.status', 1 );
		$expr[] = $search->compare( '>=', 'product.lists.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.lists.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.lists.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchItemsBase()
	{
		$total = 0;
		//search with base criteria

		$search = $this->object->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.lists.domain', 'product' ),
			$search->compare( '==', 'product.lists.editor', $this->editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 4 );
		$results = $this->object->searchItems( $search, [], $total )->toArray();
		$this->assertEquals( 4, count( $results ) );
		$this->assertEquals( 15, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}
}
