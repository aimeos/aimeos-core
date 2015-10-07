<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MShop\Coupon\Manager\Code;


/**
 * Test class for \Aimeos\MShop\Coupon\Manager\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $code;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$couponManager = \Aimeos\MShop\Coupon\Manager\Factory::createManager( \TestHelper::getContext() );

		$search = $couponManager->createSearch();
		$search->setConditions( $search->compare( '~=', 'coupon.code.code', 'OPQR' ) );
		$results = $couponManager->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \Exception( 'Code item not found' );
		};

		$this->object = $couponManager->getSubManager( 'code' );
		$this->code = $this->object->createItem();
		$this->code->setCode( 'abcd' );
		$this->code->setCount( '1' );
		$this->code->setCouponId( $item->getId() );

	}


	protected function tearDown()
	{
		unset( $this->object, $this->code );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $obj ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Common\\Criteria\\Attribute\\Iface', $obj );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Coupon\\Item\\Code\\Iface', $this->object->createItem() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->object->searchItems( $search );

		if( ( $codeItem = reset( $results ) ) === false ) {
			throw new \Exception( 'no item found exception' );
		}

		$item = $this->object->getItem( $codeItem->getId() );
		$this->assertEquals( $codeItem, $item );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$result = $this->object->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Exception( 'No coupon code item found' );
		}

		$item->setId( null );
		$item->setCode( 'unittest' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;

		$itemExp->setCount( '231199' );
		$this->object->saveItem( $itemExp );

		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $item->getId() );

		$context = \TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCouponId(), $itemSaved->getCouponId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getCount(), $itemSaved->getCount() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCouponId(), $itemUpd->getCouponId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getCount(), $itemUpd->getCount() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $item->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Common\\Criteria\\SQL', $this->object->createSearch() );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'coupon.code.id', null );
		$expr[] = $search->compare( '!=', 'coupon.code.siteid', null );
		$expr[] = $search->compare( '!=', 'coupon.code.couponid', null );
		$expr[] = $search->compare( '==', 'coupon.code.code', 'OPQR' );
		$expr[] = $search->compare( '==', 'coupon.code.count', 2000000 );
		$expr[] = $search->compare( '==', 'coupon.code.datestart', null );
		$expr[] = $search->compare( '==', 'coupon.code.dateend', null );
		$expr[] = $search->compare( '>=', 'coupon.code.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.code.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.code.editor', '' );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		//search with base criteria
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'coupon.code.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$this->assertEquals( 4, count( $this->object->searchItems( $search ) ) );
	}


	public function testDecrease()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->object->searchItems( $search );

		if( ( $codeItem = reset( $results ) ) === false ) {
			throw new \Exception( 'No coupon code item found.' );
		}

		$this->object->decrease( $codeItem->getCode(), 1 );
		$actual = $this->object->getItem( $codeItem->getId() );
		$this->object->increase( $codeItem->getCode(), 1 );

		$this->assertEquals( $codeItem->getCount() - 1, $actual->getCount() );
	}


	public function testIncrease()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->object->searchItems( $search );

		if( ( $codeItem = reset( $results ) ) === false ) {
			throw new \Exception( 'No coupon code item found.' );
		}

		$this->object->increase( $codeItem->getCode(), 1 );
		$actual = $this->object->getItem( $codeItem->getId() );
		$this->object->decrease( $codeItem->getCode(), 1 );

		$this->assertEquals( $codeItem->getCount() + 1, $actual->getCount() );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}
}
