<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Supplier\Manager\Lists;


/**
 * Test class for \Aimeos\MShop\Supplier\Manager\List.
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
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::createManager( $this->context, 'Standard' );
		$this->object = $supplierManager->getSubManager( 'lists', 'Standard' );
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


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'supplier/lists', $result );
		$this->assertContains( 'supplier/lists/type', $result );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'supplier.lists.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->aggregate( $search, 'supplier.lists.domain' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 'text', $result );
		$this->assertEquals( 3, $result['text'] );
	}


	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Lists\\Iface', $item );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'supplier.lists.domain', 'text' ),
			$search->compare( '==', 'supplier.lists.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset($results) ) === false ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId() ) );
		$this->assertNotEquals( '', $item->getTypeName() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager('type') );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager('type', 'Standard') );

		$this->setExpectedException('\\Aimeos\\MShop\\Exception');
		$this->object->getSubManager('unknown');
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$siteid = \TestHelperMShop::getContext()->getLocale()->getSiteId();

		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'supplier.lists.siteid', $siteid ),
			$search->compare( '==', 'supplier.lists.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item->setId(null);
		$item->setDomain( 'unittest' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest2' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


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

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
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

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
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
			throw new \RuntimeException( 'No first supplier list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No last supplier list item' );
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
			throw new \RuntimeException( 'No first supplier list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No second supplier list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No last supplier list item' );
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
			throw new \RuntimeException( 'No first supplier list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No second supplier list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No last supplier list item' );
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
		$total = 0;
		$siteid = \TestHelperMShop::getContext()->getLocale()->getSiteId();


		$search = $this->object->createSearch();
		$expr = array(
			$search->compare( '==', 'supplier.lists.siteid', $siteid ),
			$search->compare( '==', 'supplier.lists.domain', 'text' ),
			$search->compare( '==', 'supplier.lists.datestart', '2010-01-01 00:00:00' ),
			$search->compare( '==', 'supplier.lists.dateend', '2100-01-01 00:00:00' ),
			$search->compare( '!=', 'supplier.lists.config', null ),
			$search->compare( '==', 'supplier.lists.position', 1 ),
			$search->compare( '==', 'supplier.lists.status', 1 ),
			$search->compare( '==', 'supplier.lists.editor', $this->editor ),
			$search->compare( '==', 'supplier.lists.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->searchItems( $search );
		if( ( $listItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No list item found' );
		}


		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'supplier.lists.id', null );
		$expr[] = $search->compare( '==', 'supplier.lists.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'supplier.lists.parentid', null );
		$expr[] = $search->compare( '!=', 'supplier.lists.typeid', null );
		$expr[] = $search->compare( '==', 'supplier.lists.domain', 'text' );
		$expr[] = $search->compare( '==', 'supplier.lists.refid', $listItem->getRefId() );
		$expr[] = $search->compare( '==', 'supplier.lists.datestart', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.lists.dateend', '2100-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'supplier.lists.config', null );
		$expr[] = $search->compare( '==', 'supplier.lists.position', 1 );
		$expr[] = $search->compare( '==', 'supplier.lists.status', 1 );
		$expr[] = $search->compare( '>=', 'supplier.lists.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'supplier.lists.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.lists.editor', $this->editor );

		$search->setConditions( $search->combine('&&', $expr) );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $results ) );


		//search with base criteria
		$search = $this->object->createSearch(true);
		$expr = array(
			$search->compare( '==', 'supplier.lists.domain', 'text' ),
			$search->compare( '==', 'supplier.lists.editor', $this->editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice(0, 1);
		$results = $this->object->searchItems($search, [], $total);
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 3, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchRefItems()
	{
		$total = 0;

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'supplier.lists.domain', array( 'text' ) ) );

		$result = $this->object->searchRefItems( $search, array( 'text' ), $total );

		$this->assertArrayHasKey( 'text', $result );

		$this->assertEquals( 3, count( $result['text'] ) );

		// this is the total of list items, not the total of referenced items
		// whose number might be lower due to duplicates
		$this->assertEquals( 3, $total );
	}


	protected function getListItems()
	{
		$manager = \Aimeos\MShop\Supplier\Manager\Factory::createManager( $this->context, 'Standard' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'supplier.code', 'unitCode001' ) );
		$search->setSlice( 0, 1 );

		$results = $manager->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No supplier item found' );
		}

		$search = $this->object->createSearch();
		$expr = array(
			$search->compare( '==', 'supplier.lists.parentid', $item->getId() ),
			$search->compare( '==', 'supplier.lists.domain', 'text' ),
			$search->compare( '==', 'supplier.lists.editor', $this->editor ),
			$search->compare( '==', 'supplier.lists.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'supplier.lists.position' ) ) );

		return $this->object->searchItems( $search );
	}
}
