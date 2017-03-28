<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Media\Manager\Lists;


/**
 * Test class for \Aimeos\MShop\Media\Manager\Lists\Standard.
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
		$manager = \Aimeos\MShop\Media\Manager\Factory::createManager( $this->context, 'Standard' );
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


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'media/lists', $result );
		$this->assertContains( 'media/lists/type', $result );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'media.lists.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->aggregate( $search, 'media.lists.domain' );

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'attribute', $result );
		$this->assertEquals( 6, $result['attribute'] );
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
			throw new \RuntimeException( 'No first media list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No last media list item' );
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
			throw new \RuntimeException( 'No first media list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No second media list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No last media list item' );
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
			throw new \RuntimeException( 'No first media list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No second media list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new \RuntimeException( 'No last media list item' );
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
		$expr[] = $search->compare( '!=', 'media.lists.id', null );
		$expr[] = $search->compare( '!=', 'media.lists.siteid', null );
		$expr[] = $search->compare( '>', 'media.lists.parentid', 0 );
		$expr[] = $search->compare( '==', 'media.lists.domain', 'text' );
		$expr[] = $search->compare( '>', 'media.lists.typeid', 0 );
		$expr[] = $search->compare( '>', 'media.lists.refid', 0 );
		$expr[] = $search->compare( '==', 'media.lists.datestart', null );
		$expr[] = $search->compare( '==', 'media.lists.dateend', null );
		$expr[] = $search->compare( '!=', 'media.lists.config', null );
		$expr[] = $search->compare( '==', 'media.lists.position', 0 );
		$expr[] = $search->compare( '==', 'media.lists.status', 1 );
		$expr[] = $search->compare( '>=', 'media.lists.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'media.lists.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'media.lists.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'media.lists.type.id', null );
		$expr[] = $search->compare( '!=', 'media.lists.type.siteid', null );
		$expr[] = $search->compare( '==', 'media.lists.type.code', 'option' );
		$expr[] = $search->compare( '==', 'media.lists.type.domain', 'attribute' );
		$expr[] = $search->compare( '>', 'media.lists.type.label', '' );
		$expr[] = $search->compare( '==', 'media.lists.type.status', 1 );
		$expr[] = $search->compare( '>=', 'media.lists.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'media.lists.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'media.lists.type.editor', $this->editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		//search with base criteria
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'media.lists.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 5 );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 7, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	protected function getListItems()
	{
		$manager = \Aimeos\MShop\Media\Manager\Factory::createManager( $this->context, 'Standard' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', 'prod_266x221/198_prod_266x221.jpg' ) );
		$search->setSlice( 0, 1 );

		$results = $manager->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No media item found' );
		}

		$search = $this->object->createSearch();
		$expr = array(
			$search->compare( '==', 'media.lists.parentid', $item->getId() ),
			$search->compare( '==', 'media.lists.domain', 'attribute' ),
			$search->compare( '==', 'media.lists.editor', $this->editor ),
			$search->compare( '==', 'media.lists.type.code', 'option' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'media.lists.position' ) ) );

		return $this->object->searchItems( $search );
	}
}
