<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Attribute\Manager\Lists\Type;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::create( \TestHelperMShop::getContext() );

		$attributeListManager = $attributeManager->getSubManager( 'lists' );
		$this->object = $attributeListManager->getSubManager( 'type' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testGetResourceType()
	{
		$this->assertContains( 'attribute/lists/type', $this->object->getResourceType() );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $this->object->createItem() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$results = $this->object->searchItems( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No attribute list type item found' );
		}

		$this->assertEquals( $expected, $this->object->getItem( $expected->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$results = $this->object->searchItems( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No type item found' );
		}

		$item->setId( null );
		$item->setCode( 'unitTestSave' );
		$resultSaved = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unitTestSave2' );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
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

		$expr = [];
		$expr[] = $search->compare( '!=', 'attribute.lists.type.id', null );
		$expr[] = $search->compare( '!=', 'attribute.lists.type.siteid', null );
		$expr[] = $search->compare( '==', 'attribute.lists.type.code', 'default' );
		$expr[] = $search->compare( '==', 'attribute.lists.type.domain', 'attribute' );
		$expr[] = $search->compare( '==', 'attribute.lists.type.label', 'Standard' );
		$expr[] = $search->compare( '>=', 'attribute.lists.type.position', 0 );
		$expr[] = $search->compare( '==', 'attribute.lists.type.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.lists.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.lists.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.lists.type.editor', $this->editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		// search with base criteria
		$search = $this->object->createSearch( true );
		$search->setConditions( $search->compare( '==', 'attribute.lists.type.editor', $this->editor ) );
		$search->setSortations( [$search->sort( '-', 'attribute.lists.type.position' )] );
		$search->setSlice( 0, 5 );
		$results = $this->object->searchItems( $search, [], $total )->toArray();
		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 7, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}

}
