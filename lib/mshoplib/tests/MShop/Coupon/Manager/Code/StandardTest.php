<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Coupon\Manager\Code;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $code;


	protected function setUp() : void
	{
		$couponManager = \Aimeos\MShop\Coupon\Manager\Factory::create( \TestHelperMShop::getContext() );

		$search = $couponManager->filter();
		$search->setConditions( $search->compare( '~=', 'coupon.code.code', 'OPQR' ) );
		$results = $couponManager->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'Code item not found' );
		};

		$this->object = $couponManager->getSubManager( 'code' );
		$this->code = $this->object->create();
		$this->code->setCode( 'abcd' );
		$this->code->setCount( '1' );
		$this->code->setParentId( $item->getId() );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->code );
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
		$this->assertContains( 'coupon/code', $this->object->getResourceType() );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $obj ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $obj );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Item\Code\Iface::class, $this->object->create() );
	}


	public function testFindItem()
	{
		$item = $this->object->find( 'OPQR' );

		$this->assertEquals( 'OPQR', $item->getCode() );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $codeItem = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'no item found exception' );
		}

		$item = $this->object->get( $codeItem->getId() );
		$this->assertEquals( $codeItem, $item );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$result = $this->object->search( $search )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No coupon code item found' );
		}

		$item->setId( null );
		$item->setCode( 'unittest' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;

		$itemExp->setCount( '231199' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $item->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getCount(), $itemSaved->getCount() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getRef(), $itemSaved->getRef() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getCount(), $itemUpd->getCount() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getRef(), $itemUpd->getRef() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $item->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\SQL::class, $this->object->filter() );
	}


	public function testSearchItems()
	{
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'coupon.code.id', null );
		$expr[] = $search->compare( '!=', 'coupon.code.siteid', null );
		$expr[] = $search->compare( '!=', 'coupon.code.parentid', null );
		$expr[] = $search->compare( '==', 'coupon.code.code', 'OPQR' );
		$expr[] = $search->compare( '==', 'coupon.code.count', 2000000 );
		$expr[] = $search->compare( '==', 'coupon.code.datestart', null );
		$expr[] = $search->compare( '==', 'coupon.code.dateend', null );
		$expr[] = $search->compare( '==', 'coupon.code.ref', '' );
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
		$this->assertEquals( 5, $total );
	}


	public function testDecrease()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $codeItem = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No coupon code item found.' );
		}

		$this->object->decrease( $codeItem->getCode(), 1 );
		$actual = $this->object->get( $codeItem->getId() );
		$this->object->increase( $codeItem->getCode(), 1 );

		$this->assertEquals( $codeItem->getCount() - 1, $actual->getCount() );
	}


	public function testIncrease()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $codeItem = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No coupon code item found.' );
		}

		$this->object->increase( $codeItem->getCode(), 1 );
		$actual = $this->object->get( $codeItem->getId() );
		$this->object->decrease( $codeItem->getCode(), 1 );

		$this->assertEquals( $codeItem->getCount() + 1, $actual->getCount() );
	}


	public function testGetSubManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}
}
