<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Coupon\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $item;


	protected function setUp() : void
	{
		$this->object = \Aimeos\MShop\Coupon\Manager\Factory::create( \TestHelperMShop::getContext() );

		$this->item = $this->object->create();
		$this->item->setProvider( 'None' );
		$this->item->setConfig( array( 'key'=>'value' ) );
		$this->item->setStatus( '1' );
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
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'coupon', $result );
		$this->assertContains( 'coupon/code', $result );
	}


	public function testGetSearchAttributes()
	{
		$attribs = $this->object->getSearchAttributes();
		foreach( $attribs as $obj ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $obj );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Item\Iface::class, $this->object->create() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'code' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'code', 'Standard' ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( '$%^' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'Code', 'unknown' );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $itemA = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No results available' );
		}

		$itemB = $this->object->get( $itemA->getId() );
		$this->assertEquals( 'Unit test example', $itemB->getLabel() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$result = $this->object->search( $search )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No coupon item found' );
		}

		$item->setId( null );
		$item->setProvider( 'Unit' );
		$item->setConfig( array( 'key'=>'value' ) );
		$item->setStatus( '1' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;

		$itemExp->setStatus( '0' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $item->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getProvider(), $itemSaved->getProvider() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getProvider(), $itemUpd->getProvider() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $item->getId() );
	}


	public function testGetProvider()
	{
		$item = $this->object->create();
		$item->setProvider( 'Present,Not' );
		$provider = $this->object->getProvider( $item, 'abcd' );

		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $provider );
		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Decorator\Not::class, $provider );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getProvider( $this->object->create(), '' );
	}


	public function testCreateSearch()
	{
		$search = $this->object->filter();
		$this->assertInstanceOf( \Aimeos\MW\Criteria\SQL::class, $search );
	}


	public function testSearchItems()
	{
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'coupon.id', null );
		$expr[] = $search->compare( '!=', 'coupon.siteid', null );
		$expr[] = $search->compare( '==', 'coupon.label', 'Unit test fixed rebate' );
		$expr[] = $search->compare( '=~', 'coupon.provider', 'FixedRebate' );
		$expr[] = $search->compare( '~=', 'coupon.config', 'product' );
		$expr[] = $search->compare( '==', 'coupon.datestart', '2002-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'coupon.dateend', '2100-12-31 00:00:00' );
		$expr[] = $search->compare( '==', 'coupon.status', 1 );
		$expr[] = $search->compare( '>=', 'coupon.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.editor', '' );

		$expr[] = $search->compare( '!=', 'coupon.code.id', null );
		$expr[] = $search->compare( '!=', 'coupon.code.siteid', null );
		$expr[] = $search->compare( '!=', 'coupon.code.parentid', null );
		$expr[] = $search->compare( '==', 'coupon.code.code', '5678' );
		$expr[] = $search->compare( '>=', 'coupon.code.count', 0 );
		$expr[] = $search->compare( '==', 'coupon.code.datestart', '2000-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'coupon.code.dateend', '2004-12-21 23:59:59' );
		$expr[] = $search->compare( '>=', 'coupon.code.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.code.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.code.editor', '' );

		$total = 0;
		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemsTotal()
	{
		$total = 0;
		//search with base criteria
		$search = $this->object->filter( true );
		$search->slice( 0, 1 );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'coupon.code.editor', 'core:lib/mshoplib' ),
		);
		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 6, $total );
	}
}
