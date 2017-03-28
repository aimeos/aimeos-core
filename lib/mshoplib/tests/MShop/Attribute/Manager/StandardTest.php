<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Attribute\Manager;


/**
 * Test class for \Aimeos\MShop\Attribute\Manager\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = \Aimeos\MShop\Attribute\Manager\Factory::createManager( \TestHelperMShop::getContext() );
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

		$this->assertContains( 'attribute', $result );
		$this->assertContains( 'attribute/type', $result );
		$this->assertContains( 'attribute/lists', $result );
		$this->assertContains( 'attribute/lists/type', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $obj ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $obj );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Attribute\\Item\\Iface', $this->object->createItem() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'lists', 'unknown' );
	}


	public function testFindItem()
	{
		$item = $this->object->findItem( 'm', array( 'text' ), 'product', 'size' );

		$this->assertEquals( 'm', $item->getCode() );
		$this->assertEquals( 'size', $item->getType() );
		$this->assertEquals( 'product', $item->getDomain() );
		$this->assertEquals( 1, count( $item->getListItems( 'text' ) ) );
		$this->assertEquals( 1, count( $item->getRefItems( 'text' ) ) );
	}


	public function testFindItemInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Exception' );
		$this->object->findItem( 'invalid' );
	}


	public function testFindItemMissing()
	{
		$this->setExpectedException( '\Aimeos\MShop\Exception' );
		$this->object->findItem( 'm', [], 'product' );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.code', 'm' ),
			$search->compare( '==', 'attribute.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$results = $this->object->searchItems( $search, array( 'text' ) );
		if( ( $itemA = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No search results available in testGetItem()' );
		}

		$itemB = $this->object->getItem( $itemA->getId(), array( 'text' ) );

		$this->assertEquals( $itemA->getId(), $itemB->getId() );
		$this->assertEquals( 1, count( $itemB->getListItems( 'text' ) ) );
		$this->assertEquals( 1, count( $itemB->getRefItems( 'text' ) ) );
		$this->assertNotEquals( '', $itemB->getTypeName() );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Attribute\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$typeManager = $this->object->getSubManager( 'type' );
		$search = $typeManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.type.code', 'size' ),
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$typeItems = $typeManager->searchItems( $search );

		if( ( $typeItem = reset( $typeItems ) ) === false ) {
			throw new \RuntimeException( 'No attribute type item available in setUp()' );
		}

		$item = $this->object->createItem();
		$item->setId( null );
		$item->setDomain( 'tmpDomainx' );
		$item->setCode( '106x' );
		$item->setLabel( '106x' );
		$item->setTypeId( $typeItem->getId() );
		$item->setPosition( 0 );
		$item->setStatus( 7 );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'tmpDomain' );
		$itemExp->setCode( '106' );
		$itemExp->setLabel( '106' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $item->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $item->getId() );
	}


	public function testCreateSearch()
	{
		$search = $this->object->createSearch();
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $search );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'attribute.id', null );
		$expr[] = $search->compare( '!=', 'attribute.siteid', null );
		$expr[] = $search->compare( '!=', 'attribute.typeid', null );
		$expr[] = $search->compare( '==', 'attribute.position', 5 );
		$expr[] = $search->compare( '==', 'attribute.code', 'black' );
		$expr[] = $search->compare( '==', 'attribute.label', 'black' );
		$expr[] = $search->compare( '==', 'attribute.domain', 'product' );
		$expr[] = $search->compare( '==', 'attribute.status', 0 );
		$expr[] = $search->compare( '>=', 'attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'attribute.type.id', null );
		$expr[] = $search->compare( '!=', 'attribute.type.siteid', null );
		$expr[] = $search->compare( '==', 'attribute.type.code', 'color' );
		$expr[] = $search->compare( '==', 'attribute.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'attribute.type.label', 'Color' );
		$expr[] = $search->compare( '==', 'attribute.type.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.type.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'attribute.lists.id', null );
		$expr[] = $search->compare( '!=', 'attribute.lists.siteid', null );
		$expr[] = $search->compare( '!=', 'attribute.lists.parentid', null );
		$expr[] = $search->compare( '==', 'attribute.lists.domain', 'text' );
		$expr[] = $search->compare( '!=', 'attribute.lists.typeid', null );
		$expr[] = $search->compare( '>', 'attribute.lists.refid', 0 );
		$expr[] = $search->compare( '==', 'attribute.lists.datestart', '2000-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.lists.dateend', '2001-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'attribute.lists.config', null );
		$expr[] = $search->compare( '==', 'attribute.lists.position', 0 );
		$expr[] = $search->compare( '==', 'attribute.lists.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.lists.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.lists.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.lists.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'attribute.lists.type.id', null );
		$expr[] = $search->compare( '!=', 'attribute.lists.type.siteid', null );
		$expr[] = $search->compare( '==', 'attribute.lists.type.code', 'default' );
		$expr[] = $search->compare( '==', 'attribute.lists.type.domain', 'text' );
		$expr[] = $search->compare( '==', 'attribute.lists.type.label', 'Standard' );
		$expr[] = $search->compare( '==', 'attribute.lists.type.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.lists.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.lists.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.lists.type.editor', $this->editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		//search with base criteria
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '~=', 'attribute.code', '3' ),
			$search->compare( '==', 'attribute.editor', $this->editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 5 );

		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 10, $total );
		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchTotal()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.type.code', 'size' ),
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );

		$total = 0;
		$items = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 6, $total );
	}
}
