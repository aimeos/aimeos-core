<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Attribute\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = \Aimeos\MShop\Attribute\Manager\Factory::createManager( \TestHelperMShop::getContext() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'attribute', $result );
		$this->assertContains( 'attribute/lists', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $obj ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $obj );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Attribute\Item\Iface::class, $this->object->createItem() );
	}


	public function testCreateItemType()
	{
		$item = $this->object->createItem( 'color', 'product' );
		$this->assertEquals( 'color', $item->getType() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'lists', 'unknown' );
	}


	public function testFindItem()
	{
		$item = $this->object->findItem( 'm', array( 'text' ), 'product', 'size' );

		$this->assertEquals( 'm', $item->getCode() );
		$this->assertEquals( 'size', $item->getType() );
		$this->assertEquals( 'product', $item->getDomain() );
		$this->assertEquals( 1, count( $item->getListItems( 'text', null, null, false ) ) );
		$this->assertEquals( 1, count( $item->getRefItems( 'text', null, null, false ) ) );
	}


	public function testFindItemInvalid()
	{
		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->findItem( 'invalid' );
	}


	public function testFindItemMissing()
	{
		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->findItem( 'm', [], 'product' );
	}


	public function testGetItem()
	{
		$itemA = $this->object->findItem( 'black', [], 'product', 'color' );
		$itemB = $this->object->getItem( $itemA->getId(), ['attribute/property', 'text'] );

		$this->assertEquals( $itemA->getId(), $itemB->getId() );
		$this->assertEquals( 1, count( $itemB->getPropertyItems() ) );
		$this->assertEquals( 1, count( $itemB->getListItems( null, null, null, false ) ) );
	}


	public function testGetItemLists()
	{
		$itemA = $this->object->findItem( 'xxl', [], 'product', 'size' );
		$itemB = $this->object->getItem( $itemA->getId(), ['text'] );

		$this->assertEquals( $itemA->getId(), $itemB->getId() );
		$this->assertEquals( 1, count( $itemB->getListItems( 'text', null, null, false ) ) );
		$this->assertEquals( 1, count( $itemB->getRefItems( 'text', null, null, false ) ) );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( \Aimeos\MW\Common\Exception::class );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->createItem();
		$item->setId( null );
		$item->setDomain( 'tmpDomainx' );
		$item->setCode( '106x' );
		$item->setLabel( '106x' );
		$item->setType( 'size' );
		$item->setPosition( 0 );
		$item->setStatus( 7 );
		$resultSaved = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'tmpDomain' );
		$itemExp->setCode( '106' );
		$itemExp->setLabel( '106' );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $item->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getItem( $item->getId() );
	}


	public function testGetSavePropertyItems()
	{
		$item = $this->object->findItem( 'black', [], 'product', 'color' );

		$item->setId( null )->setCode( 'xyz' );
		$this->object->saveItem( $item );

		$item2 = $this->object->findItem( 'xyz', [], 'product', 'color' );

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 1, count( $item->getPropertyItems() ) );
		$this->assertEquals( 1, count( $item2->getPropertyItems() ) );
	}


	public function testCreateSearch()
	{
		$search = $this->object->createSearch();
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $search );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'attribute.id', null );
		$expr[] = $search->compare( '!=', 'attribute.siteid', null );
		$expr[] = $search->compare( '==', 'attribute.type', 'color' );
		$expr[] = $search->compare( '==', 'attribute.position', 5 );
		$expr[] = $search->compare( '==', 'attribute.code', 'black' );
		$expr[] = $search->compare( '==', 'attribute.label', 'product/color/black' );
		$expr[] = $search->compare( '==', 'attribute.domain', 'product' );
		$expr[] = $search->compare( '==', 'attribute.status', 0 );
		$expr[] = $search->compare( '>=', 'attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'attribute.lists.id', null );
		$expr[] = $search->compare( '!=', 'attribute.lists.siteid', null );
		$expr[] = $search->compare( '!=', 'attribute.lists.parentid', null );
		$expr[] = $search->compare( '==', 'attribute.lists.domain', 'text' );
		$expr[] = $search->compare( '==', 'attribute.lists.type', 'default' );
		$expr[] = $search->compare( '>', 'attribute.lists.refid', '' );
		$expr[] = $search->compare( '==', 'attribute.lists.datestart', '2000-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.lists.dateend', '2001-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'attribute.lists.config', null );
		$expr[] = $search->compare( '==', 'attribute.lists.position', 0 );
		$expr[] = $search->compare( '==', 'attribute.lists.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.lists.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.lists.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.lists.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'attribute.property.id', null );
		$expr[] = $search->compare( '!=', 'attribute.property.siteid', null );
		$expr[] = $search->compare( '==', 'attribute.property.type', 'htmlcolor' );
		$expr[] = $search->compare( '==', 'attribute.property.languageid', 'de' );
		$expr[] = $search->compare( '==', 'attribute.property.value', '#000000' );
		$expr[] = $search->compare( '==', 'attribute.property.editor', $this->editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, count( reset( $results )->getPropertyItems() ) );
	}


	public function testSearchBase()
	{
		//search with base criteria
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '~=', 'attribute.code', '3' ),
			$search->compare( '==', 'attribute.editor', $this->editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 5 );

		$total = 0;
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 10, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchTotal()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.type', 'size' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );

		$total = 0;
		$items = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 6, $total );
	}
}
