<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Locale\Manager\Site;


/**
 * Test class for \Aimeos\MShop\Locale\Manager\Site\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Locale\Manager\Site\Standard( $this->context );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Locale\\Item\\Site\\Iface', $this->object->createItem() );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Locale\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveIdException()
	{
		$this->setExpectedException( '\Aimeos\MShop\Locale\Exception' );
		$this->object->saveItem( $this->object->createItem() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->createItem();
		$item->setLabel( 'new name' );
		$item->setStatus( 1 );
		$item->setCode( 'xx' );
		$this->object->insertItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'new new name' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $item->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getChildren(), $itemSaved->getChildren() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getChildren(), $itemUpd->getChildren() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $item->getId() );
	}


	public function testFindItem()
	{
		$item = $this->object->findItem( 'unittest' );

		$this->assertEquals( 'unittest', $item->getCode() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$a = $this->object->searchItems( $search );
		if( ( $expected = reset( $a ) ) === false ) {
			throw new \RuntimeException( 'Site item not found' );
		}

		$actual = $this->object->getItem( $expected->getId() );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Locale\\Item\\Site\\Iface', $actual );
		$this->assertEquals( $expected, $actual );
	}


	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'locale.site.id', null );
		$expr[] = $search->compare( '==', 'locale.site.siteid', $siteid );
		$expr[] = $search->compare( '==', 'locale.site.code', 'unittest' );
		$expr[] = $search->compare( '==', 'locale.site.label', 'Unit test site' );
		$expr[] = $search->compare( '~=', 'locale.site.config', '{' );
		$expr[] = $search->compare( '==', 'locale.site.status', 0 );
		$expr[] = $search->compare( '>=', 'locale.site.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.site.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.site.editor', '' );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		//search with base criteria and total
		$search = $this->object->createSearch( true );
		$search->setConditions( $search->compare( '==', 'locale.site.code', array( 'unittest' ) ) );
		$search->setSlice( 0, 1 );

		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertGreaterThanOrEqual( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'locale/site', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetPath()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$list = $this->object->getPath( $expected->getId() );
		$this->assertArrayHasKey( $expected->getId(), $list );
		$this->assertEquals( $expected, $list[$expected->getId()] );
	}


	public function testGetTree()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item = $this->object->getTree( $expected->getId() );
		$this->assertEquals( $expected, $item );
	}


	public function testGetTreeNoId()
	{
		$object = $this->getMockBuilder( '\Aimeos\MShop\Locale\Manager\Site\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $object->createItem() ) ) );

		$this->assertInstanceOf( '\Aimeos\MShop\Locale\Item\Site\Iface', $object->getTree() );
	}


	public function testGetTreeNoItem()
	{
		$object = $this->getMockBuilder( '\Aimeos\MShop\Locale\Manager\Site\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( [] ) );

		$this->setExpectedException( '\Aimeos\MShop\Locale\Exception' );
		$object->getTree();
	}


	public function testGetTreeCache()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item = $this->object->getTree( $expected->getId() );
		$item2 = $this->object->getTree( $expected->getId() );

		$this->assertSame( $item, $item2 );
	}


	public function testGetTreeDefault()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'default' ) );

		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) !== false )
		{
			$item = $this->object->getTree();
			$this->assertEquals( $expected, $item );
		}
	}


	public function testMoveItem()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Locale\\Exception' );
		$this->object->moveItem( null, null, null );
	}
}
