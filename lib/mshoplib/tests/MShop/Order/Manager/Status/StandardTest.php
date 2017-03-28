<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

namespace Aimeos\MShop\Order\Manager\Status;


/**
 * Test class for \Aimeos\MShop\Order\Manager\Status\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
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
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Order\Manager\Status\Standard( $this->context );
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


	public function testAggregate()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.status.editor', 'core:unittest' ) );
		$result = $this->object->aggregate( $search, 'order.status.value' );

		$this->assertEquals( 6, count( $result ) );
		$this->assertArrayHasKey( 'waiting', $result );
		$this->assertEquals( 2, $result['waiting'] );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'order/status', $result );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Status\\Iface', $this->object->createItem() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.status.value', 'shipped' ) );
		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \Aimeos\MShop\Order\Exception( 'No order status item found' );
		}

		$this->assertEquals( $expected, $this->object->getItem( $expected->getId() ) );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Order\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.status.value', 'shipped' ),
			$search->compare( '==', 'order.status.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order base item found.' );
		}

		$item->setId( null );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setType( 'received' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}

	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $this->object->createSearch() );
	}

	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.status.id', null );
		$expr[] = $search->compare( '==', 'order.status.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.status.parentid', null );
		$expr[] = $search->compare( '>=', 'order.status.type', 'typestatus' );
		$expr[] = $search->compare( '==', 'order.status.value', 'shipped' );
		$expr[] = $search->compare( '>=', 'order.status.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.status.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.status.editor', $this->editor );



		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '>=', 'order.status.value', 'waiting' ),
			$search->compare( '==', 'order.status.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );
		$total = 0;
		$items = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 2, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}



}