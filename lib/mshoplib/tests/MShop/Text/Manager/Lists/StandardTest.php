<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Text\Manager\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $editor = '';


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();
		$manager = \Aimeos\MShop\Text\Manager\Factory::create( $this->context, 'Standard' );
		$this->object = $manager->getSubManager( 'lists', 'Standard' );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->context );
	}


	public function testCleanup()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->cleanup( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'text/lists', $result );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'text.lists.editor', 'core:lib/mshoplib' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->aggregate( $search, 'text.lists.domain' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 'media', $result );
		$this->assertEquals( 10, $result['media'] );
	}


	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $item );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'text.lists.editor', $this->editor ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'text.lists.editor', $this->editor ) );
		$items = $this->object->searchItems( $search );

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

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'text.lists.id', null );
		$expr[] = $search->compare( '!=', 'text.lists.siteid', null );
		$expr[] = $search->compare( '!=', 'text.lists.parentid', null );
		$expr[] = $search->compare( '==', 'text.lists.domain', 'media' );
		$expr[] = $search->compare( '==', 'text.lists.type', 'align-top' );
		$expr[] = $search->compare( '>', 'text.lists.refid', 0 );
		$expr[] = $search->compare( '==', 'text.lists.datestart', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.lists.dateend', '2098-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'text.lists.config', null );
		$expr[] = $search->compare( '==', 'text.lists.position', 0 );
		$expr[] = $search->compare( '==', 'text.lists.status', 1 );
		$expr[] = $search->compare( '==', 'text.lists.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchItemsAll()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'text.lists.editor', $this->editor ) );
		$this->assertEquals( 12, count( $this->object->searchItems( $search ) ) );
	}


	public function testSearchItemsBase()
	{
		$total = 0;
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'text.lists.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 5 );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 10, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}
}
