<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MShop\Order\Manager\Status;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Order\Manager\Status\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.status.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.status.value' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'waiting', $result );
		$this->assertEquals( 2, $result['waiting'] );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'order/status', $result );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Status\Iface::class, $this->object->create() );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'order.status.value', 'shipped' ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \Aimeos\MShop\Order\Exception( 'No order status item found' );
		}

		$this->assertEquals( $expected, $this->object->get( $expected->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'order.status.value', 'shipped' ),
			$search->compare( '==', 'order.status.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order base item found.' );
		}

		$item->setId( null );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setType( 'received' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}

	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter() );
	}

	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.status.id', null );
		$expr[] = $search->compare( '==', 'order.status.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.status.parentid', null );
		$expr[] = $search->compare( '>=', 'order.status.type', 'typestatus' );
		$expr[] = $search->compare( '==', 'order.status.value', 'shipped' );
		$expr[] = $search->compare( '>=', 'order.status.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.status.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.status.editor', $this->editor );



		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '>=', 'order.status.value', 'waiting' ),
			$search->compare( '==', 'order.status.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 1 );
		$total = 0;
		$items = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 2, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}



}
