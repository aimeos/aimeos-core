<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MShop\Review\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();

		$this->object = new \Aimeos\MShop\Review\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->and( [
			$search->compare( '==', 'review.domain', 'product' ),
			$search->compare( '==', 'review.editor', 'core:lib/mshoplib' )
		] ) );
		$result = $this->object->aggregate( $search, 'review.rating' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 0, $result );
		$this->assertArrayHasKey( 4, $result );
		$this->assertEquals( 1, $result[0] );
		$this->assertEquals( 1, $result[4] );
	}


	public function testAggregateRating()
	{
		$search = $this->object->filter()->add( ['review.domain' => 'product', 'review.editor' => 'core:lib/mshoplib'] );
		$result = $this->object->aggregate( $search, 'review.rating', null, 'rate' )->toArray();

		$this->assertEquals( [0 => [0 => 1], 4 => [4 => 1]], $result );
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

		$this->assertContains( 'review', $result );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Review\Item\Iface::class, $this->object->create() );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'review.status', 1 ),
			$search->compare( '==', 'review.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No review found in mshop_review with status "%1$s"', 1 ) );
		}

		$actual = $this->object->get( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'review.status', 1 ),
			$search->compare( '==', 'review.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No review item found.' );
		}

		$item->setId( null );
		$item->setRefId( 'xyz' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setRating( 5 );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getRefId(), $itemSaved->getRefId() );
		$this->assertEquals( $item->getName(), $itemSaved->getName() );
		$this->assertEquals( $item->getRating(), $itemSaved->getRating() );
		$this->assertEquals( $item->getComment(), $itemSaved->getComment() );
		$this->assertEquals( $item->getResponse(), $itemSaved->getResponse() );
		$this->assertEquals( $item->getCustomerId(), $itemSaved->getCustomerId() );
		$this->assertEquals( $item->getOrderProductId(), $itemSaved->getOrderProductId() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getRefId(), $itemUpd->getRefId() );
		$this->assertEquals( $itemExp->getName(), $itemUpd->getName() );
		$this->assertEquals( $itemExp->getRating(), $itemUpd->getRating() );
		$this->assertEquals( $itemExp->getComment(), $itemUpd->getComment() );
		$this->assertEquals( $itemExp->getResponse(), $itemUpd->getResponse() );
		$this->assertEquals( $itemExp->getCustomerId(), $itemUpd->getCustomerId() );
		$this->assertEquals( $itemExp->getOrderProductId(), $itemUpd->getOrderProductId() );

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


	public function testCreateSearchDefault()
	{
		$result = $this->object->filter( true );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\Iface::class, $result->getConditions() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->filter();
		$siteid = $this->context->getLocale()->getSiteId();

		$expr = [];
		$expr[] = $search->compare( '!=', 'review.id', null );
		$expr[] = $search->compare( '==', 'review.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'review.customerid', null );
		$expr[] = $search->compare( '!=', 'review.orderproductid', null );
		$expr[] = $search->compare( '!=', 'review.refid', null );
		$expr[] = $search->compare( '==', 'review.domain', 'customer' );
		$expr[] = $search->compare( '==', 'review.name', 'test user' );
		$expr[] = $search->compare( '>=', 'review.comment', '' );
		$expr[] = $search->compare( '>=', 'review.response', '' );
		$expr[] = $search->compare( '==', 'review.rating', 5 );
		$expr[] = $search->compare( '==', 'review.status', 1 );
		$expr[] = $search->compare( '>=', 'review.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'review.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'review.editor', $this->editor );


		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemsTotal()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'review.editor', $this->editor ) );
		$items = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 4, $total );

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
