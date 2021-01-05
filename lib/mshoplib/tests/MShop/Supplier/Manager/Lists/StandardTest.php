<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Supplier\Manager\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $editor = '';


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context, 'Standard' );
		$this->object = $supplierManager->getSubManager( 'lists', 'Standard' );
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

		$this->assertContains( 'supplier/lists', $result );
	}


	public function testAggregate()
	{
		$search = $this->object->filter( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'supplier.lists.editor', 'core:lib/mshoplib' ),
		);
		$search->setConditions( $search->and( $expr ) );

		$result = $this->object->aggregate( $search, 'supplier.lists.domain' )->toArray();

		$this->assertEquals( 3, count( $result ) );
		$this->assertArrayHasKey( 'text', $result );
		$this->assertEquals( 2, $result['product'] );
		$this->assertEquals( 1, $result['media'] );
		$this->assertEquals( 3, $result['text'] );
	}


	public function testCreateItem()
	{
		$item = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $item );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'supplier.lists.domain', 'text' ),
			$search->compare( '==', 'supplier.lists.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$this->assertEquals( $item, $this->object->get( $item->getId() ) );
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

		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'supplier.lists.siteid', $siteid ),
			$search->compare( '==', 'supplier.lists.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$items = $this->object->search( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item->setId( null );
		$item->setDomain( 'unittest' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest1' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


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
		$this->object->get( $itemSaved->getId() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'supplier.lists.id', null );
		$expr[] = $search->compare( '!=', 'supplier.lists.siteid', null );
		$expr[] = $search->compare( '!=', 'supplier.lists.parentid', null );
		$expr[] = $search->compare( '==', 'supplier.lists.domain', 'text' );
		$expr[] = $search->compare( '==', 'supplier.lists.type', 'default' );
		$expr[] = $search->compare( '>', 'supplier.lists.refid', '' );
		$expr[] = $search->compare( '==', 'supplier.lists.datestart', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.lists.dateend', '2100-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'supplier.lists.config', null );
		$expr[] = $search->compare( '==', 'supplier.lists.position', 1 );
		$expr[] = $search->compare( '==', 'supplier.lists.status', 1 );
		$expr[] = $search->compare( '>=', 'supplier.lists.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'supplier.lists.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.lists.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchItemsBase()
	{
		$total = 0;
		$search = $this->object->filter( true );
		$expr = array(
			$search->compare( '==', 'supplier.lists.domain', 'text' ),
			$search->compare( '==', 'supplier.lists.editor', $this->editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->and( $expr ) );
		$search->slice( 0, 1 );
		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 3, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}
}
