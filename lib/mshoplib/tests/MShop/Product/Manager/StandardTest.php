<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Product\Manager;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();

		$this->object = new \Aimeos\MShop\Product\Manager\Standard( $this->context );
	}

	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Product\\Item\\Iface', $this->object->createItem() );
	}


	public function testCreateSearch()
	{
		$search = $this->object->createSearch();
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\SQL', $search );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'product', $result );
		$this->assertContains( 'product/type', $result );
		$this->assertContains( 'product/lists', $result );
		$this->assertContains( 'product/lists/type', $result );
		$this->assertContains( 'product/property', $result );
		$this->assertContains( 'product/property/type', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testFindItem()
	{
		$item = $this->object->findItem( 'CNC' );

		$this->assertEquals( 'CNC', $item->getCode() );
	}


	public function testGetItem()
	{
		$domains = array( 'text', 'product', 'price', 'media', 'attribute', 'product/property' );

		$search = $this->object->createSearch();
		$conditions = array(
				$search->compare( '==', 'product.code', 'CNC' ),
				$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$products = $this->object->searchItems( $search, $domains );

		if( ( $product = reset( $products ) ) === false ) {
			throw new \RuntimeException( sprintf( 'Found no Productitem with text "%1$s"', 'Cafe Noire Cappuccino' ) );
		}

		$this->assertEquals( $product, $this->object->getItem( $product->getId(), $domains ) );
		$this->assertEquals( 6, count( $product->getRefItems( 'text' ) ) );
		$this->assertEquals( 4, count( $product->getPropertyItems() ) );
		$this->assertNotEquals( '', $product->getTypeName() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
				$search->compare( '==', 'product.code', 'CNC' ),
				$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No product item found' );
		}

		$item->setId( null );
		$item->setCode( 'CNC unit test' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unit save test' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testSaveItemSitecheck()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->editor ) );
		$search->setSlice( 0, 1 );
		$products = $manager->searchItems( $search );

		if( ( $item = reset( $products ) ) === false ) {
			throw new \RuntimeException( 'No product found' );
		}

		$item->setId( null );
		$item->setCode( 'unittest' );

		$manager->saveItem( $item );
		$manager->getItem( $item->getId() );
		$manager->deleteItem( $item->getId() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$manager->getItem( $item->getId() );
	}


	public function testUpdateListItems()
	{
		$attrManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $this->context );
		$attrId = $attrManager->findItem( 's', [], 'product', 'size' )->getId();
		$item = $this->object->findItem( 'CNC', array( 'attribute' ) );

		// create new list item
		$map = array( $attrId => array( 'product.lists.datestart' => '2000-01-01 00:00:00' ) );
		$this->object->updateListItems( $item, $map, 'attribute', 'hidden' );

		$item = $this->object->findItem( 'CNC', array( 'attribute' ) );
		$listItems = $item->getListItems( 'attribute', 'hidden' );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertEquals( '2000-01-01 00:00:00', reset( $listItems )->getDateStart() );


		// update existing list item
		$map = array( $attrId => array( 'product.lists.config' => array( 'key' => 'value' ) ) );
		$this->object->updateListItems( $item, $map, 'attribute', 'hidden' );

		$item = $this->object->findItem( 'CNC', array( 'attribute' ) );
		$listItems = $item->getListItems( 'attribute', 'hidden' );

		$this->assertEquals( 1, count( $listItems ) );
		$this->assertEquals( '2000-01-01 00:00:00', reset( $listItems )->getDateStart() );
		$this->assertEquals( array( 'key' => 'value' ), reset( $listItems )->getConfig() );


		// delete existing list item
		$this->object->updateListItems( $item, [], 'attribute', 'hidden' );

		$item = $this->object->findItem( 'CNC', array( 'attribute' ) );
		$this->assertEquals( 0, count( $item->getListItems( 'attribute', 'hidden' ) ) );
	}


	public function testSearchItems()
	{
		$total = 0;
		$listManager = $this->object->getSubManager( 'lists' );

		$search = $listManager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.lists.type.domain', 'product' ),
			$search->compare( '==', 'product.lists.type.code', 'suggestion' ),
			$search->compare( '==', 'product.lists.datestart', null ),
			$search->compare( '==', 'product.lists.dateend', null ),
			$search->compare( '!=', 'product.lists.config', null ),
			$search->compare( '==', 'product.lists.position', 0 ),
			$search->compare( '==', 'product.lists.status', 1 ),
			$search->compare( '==', 'product.lists.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$results = $listManager->searchItems( $search );
		if( ( $listItem = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'product.id', null );
		$expr[] = $search->compare( '!=', 'product.siteid', null );
		$expr[] = $search->compare( '!=', 'product.typeid', null );
		$expr[] = $search->compare( '==', 'product.code', 'CNE' );
		$expr[] = $search->compare( '==', 'product.label', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '~=', 'product.config', 'css-class' );
		$expr[] = $search->compare( '==', 'product.datestart', null );
		$expr[] = $search->compare( '==', 'product.dateend', null );
		$expr[] = $search->compare( '==', 'product.status', 1 );
		$expr[] = $search->compare( '>=', 'product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.editor', $this->editor );

		$param = array( 'product', $listItem->getTypeId(), array( $listItem->getRefId() ) );
		$expr[] = $search->compare( '>', $search->createFunction( 'product.contains', $param ), 0 );

		$expr[] = $search->compare( '!=', 'product.type.id', null );
		$expr[] = $search->compare( '!=', 'product.type.siteid', null );
		$expr[] = $search->compare( '==', 'product.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'product.type.code', 'default' );
		$expr[] = $search->compare( '==', 'product.type.label', 'Article' );
		$expr[] = $search->compare( '==', 'product.type.status', 1 );
		$expr[] = $search->compare( '==', 'product.type.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'product.lists.id', null );
		$expr[] = $search->compare( '!=', 'product.lists.siteid', null );
		$expr[] = $search->compare( '!=', 'product.lists.parentid', null );
		$expr[] = $search->compare( '!=', 'product.lists.typeid', null );
		$expr[] = $search->compare( '==', 'product.lists.domain', 'product' );
		$expr[] = $search->compare( '>', 'product.lists.refid', 0 );
		$expr[] = $search->compare( '==', 'product.lists.datestart', null );
		$expr[] = $search->compare( '==', 'product.lists.dateend', null );
		$expr[] = $search->compare( '!=', 'product.lists.config', null );
		$expr[] = $search->compare( '==', 'product.lists.position', 0 );
		$expr[] = $search->compare( '==', 'product.lists.status', 1 );
		$expr[] = $search->compare( '==', 'product.lists.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'product.lists.type.id', null );
		$expr[] = $search->compare( '!=', 'product.lists.type.siteid', null );
		$expr[] = $search->compare( '==', 'product.lists.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'product.lists.type.code', 'suggestion' );
		$expr[] = $search->compare( '==', 'product.lists.type.label', 'Suggestion' );
		$expr[] = $search->compare( '==', 'product.lists.type.status', 1 );
		$expr[] = $search->compare( '==', 'product.lists.type.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'product.property.id', null );
		$expr[] = $search->compare( '!=', 'product.property.siteid', null );
		$expr[] = $search->compare( '!=', 'product.property.typeid', null );
		$expr[] = $search->compare( '==', 'product.property.languageid', null );
		$expr[] = $search->compare( '==', 'product.property.value', '1' );
		$expr[] = $search->compare( '==', 'product.property.editor', $this->editor );


		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->editor ) );
		$search->setSlice( 0, 10 );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 10, count( $results ) );
		$this->assertEquals( 28, $total );


		$search = $this->object->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ),
			$search->compare( '==', 'product.editor', $this->editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, array( 'media' ) );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchWildcards()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '=~', 'product.code', 'CN_' ) );
		$result = $this->object->searchItems( $search );

		$this->assertEquals( 0, count( $result ) );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '=~', 'product.code', 'CN%' ) );
		$result = $this->object->searchItems( $search );

		$this->assertEquals( 0, count( $result ) );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '=~', 'product.code', 'CN[C]' ) );
		$result = $this->object->searchItems( $search );

		$this->assertEquals( 0, count( $result ) );
	}


	public function testSearchItemsLimit()
	{
		$start = 0;
		$numproducts = 0;

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', 'core:unittest' ) );
		$search->setSlice( $start, 5 );

		do
		{
			$result = $this->object->searchItems( $search );

			foreach( $result as $item ) {
				$numproducts++;
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start, 5 );
		}
		while( $count > 0 );

		$this->assertEquals( 28, $numproducts );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'property' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'property', 'Standard' ) );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'lists', 'unknown' );
	}
}
