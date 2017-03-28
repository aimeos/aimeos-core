<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Coupon\Manager;


/**
 * Test class for \Aimeos\MShop\Coupon\Manager\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $item;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = \Aimeos\MShop\Coupon\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$this->item = $this->object->createItem();
		$this->item->setProvider( 'Example' );
		$this->item->setConfig( array( 'key'=>'value' ) );
		$this->item->setStatus( '1' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
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

		$this->assertContains( 'coupon', $result );
		$this->assertContains( 'coupon/code', $result );
	}


	public function testGetSearchAttributes()
	{
		$attribs = $this->object->getSearchAttributes();
		foreach( $attribs as $obj ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $obj );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Coupon\\Item\\Iface', $this->object->createItem() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'code' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'code', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( '$%^' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'Code', 'unknown' );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->object->searchItems( $search );

		if( ( $itemA = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No results available' );
		}

		$itemB = $this->object->getItem( $itemA->getId() );
		$this->assertEquals( 'Unit test example', $itemB->getLabel() );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Coupon\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$result = $this->object->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No coupon item found' );
		}

		$item->setId( null );
		$item->setProvider( 'Unit' );
		$item->setConfig( array( 'key'=>'value' ) );
		$item->setStatus( '1' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;

		$itemExp->setStatus( '0' );
		$this->object->saveItem( $itemExp );

		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $item->getId() );

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

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $item->getId() );
	}


	public function testGetProvider()
	{
		$item = $this->object->createItem();
		$item->setProvider( 'Present,Example' );
		$provider = $this->object->getProvider( $item, 'abcd' );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Coupon\\Provider\\Iface', $provider );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Coupon\\Provider\\Decorator\\Example', $provider );


		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getProvider( $this->object->createItem(), '' );
	}


	public function testCreateSearch()
	{
		$search = $this->object->createSearch();
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\SQL', $search );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();

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
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );


		//search with base criteria
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'coupon.code.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$this->assertEquals( 5, count( $this->object->searchItems( $search ) ) );
	}
}
