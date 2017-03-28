<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Catalog\Manager\Lists;


/**
 * Test class for \Aimeos\MShop\Catalog\Manager\List.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;
	private $editor = '';


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();
		$manager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context, 'Standard' );
		$this->object = $manager->getSubManager( 'lists', 'Standard' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object, $this->context );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'catalog.lists.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->aggregate( $search, 'catalog.lists.domain' );

		$this->assertEquals( 3, count( $result ) );
		$this->assertArrayHasKey( 'media', $result );
		$this->assertEquals( 7, $result['media'] );
	}


	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $item );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId() ) );
		$this->assertNotEquals( '', $item->getTypeName() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'catalog/lists', $result );
		$this->assertContains( 'catalog/lists/type', $result );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item->setId( null );
		$item->setDomain( 'unittest' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest2' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getRefId(), $itemSaved->getRefId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getRefId(), $itemUpd->getRefId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testMoveItemLastToFront()
	{
		$listItems = $this->getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No first catalog list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No last catalog list item' );
		}

		$this->object->moveItem( $last->getId(), $first->getId() );

		$newFirst = $this->object->getItem( $last->getId() );
		$newSecond = $this->object->getItem( $first->getId() );

		$this->object->moveItem( $last->getId() );

		$this->assertEquals( 1, $newFirst->getPosition() );
		$this->assertEquals( 2, $newSecond->getPosition() );
	}


	public function testMoveItemFirstToLast()
	{
		$listItems = $this->getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No first catalog list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No second catalog list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No last catalog list item' );
		}

		$this->object->moveItem( $first->getId() );

		$newBefore = $this->object->getItem( $last->getId() );
		$newLast = $this->object->getItem( $first->getId() );

		$this->object->moveItem( $first->getId(), $second->getId() );

		$this->assertEquals( $last->getPosition() - 1, $newBefore->getPosition() );
		$this->assertEquals( $last->getPosition(), $newLast->getPosition() );
	}


	public function testMoveItemFirstUp()
	{
		$listItems = $this->getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No first catalog list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No second catalog list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No last catalog list item' );
		}

		$this->object->moveItem( $first->getId(), $last->getId() );

		$newLast = $this->object->getItem( $last->getId() );
		$newUp = $this->object->getItem( $first->getId() );

		$this->object->moveItem( $first->getId(), $second->getId() );

		$this->assertEquals( $last->getPosition() - 1, $newUp->getPosition() );
		$this->assertEquals( $last->getPosition(), $newLast->getPosition() );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'catalog.lists.id', null );
		$expr[] = $search->compare( '!=', 'catalog.lists.siteid', null );
		$expr[] = $search->compare( '!=', 'catalog.lists.parentid', null );
		$expr[] = $search->compare( '==', 'catalog.lists.domain', 'product' );
		$expr[] = $search->compare( '!=', 'catalog.lists.typeid', null );
		$expr[] = $search->compare( '>', 'catalog.lists.refid', 0 );
		$expr[] = $search->compare( '==', 'catalog.lists.datestart', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.lists.dateend', '2099-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'catalog.lists.config', null );
		$expr[] = $search->compare( '==', 'catalog.lists.position', 0 );
		$expr[] = $search->compare( '==', 'catalog.lists.status', 1 );
		$expr[] = $search->compare( '>=', 'catalog.lists.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.lists.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.lists.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'catalog.lists.type.id', null );
		$expr[] = $search->compare( '!=', 'catalog.lists.type.siteid', null );
		$expr[] = $search->compare( '==', 'catalog.lists.type.code', 'new' );
		$expr[] = $search->compare( '==', 'catalog.lists.type.domain', 'product' );
		$expr[] = $search->compare( '>', 'catalog.lists.type.label', '' );
		$expr[] = $search->compare( '==', 'catalog.lists.type.status', 1 );
		$expr[] = $search->compare( '>=', 'catalog.lists.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.lists.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.lists.type.editor', $this->editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 2, $total );

		//search with base criteria
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'catalog.lists.type.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );
		$this->assertEquals( 45, count( $results ) );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	protected function getListItems()
	{
		$manager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context, 'Standard' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$search->setSlice( 0, 1 );

		$results = $manager->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No catalog item found' );
		}

		$search = $this->object->createSearch();
		$expr = array(
			$search->compare( '==', 'catalog.lists.parentid', $item->getId() ),
			$search->compare( '==', 'catalog.lists.domain', 'text' ),
			$search->compare( '==', 'catalog.lists.editor', $this->editor ),
			$search->compare( '==', 'catalog.lists.type.code', 'unittype1' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'catalog.lists.position' ) ) );

		return $this->object->searchItems( $search );
	}
}
