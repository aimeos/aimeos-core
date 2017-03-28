<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MAdmin\Log\Manager;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MAdmin\Log\Manager\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MAdmin\\Log\\Item\\Iface', $this->object->createItem() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'log', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attr ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attr );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MAdmin\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'log.id', null );
		$expr[] = $search->compare( '!=', 'log.siteid', null );
		$expr[] = $search->compare( '==', 'log.facility', 'unittest facility' );
		$expr[] = $search->compare( '>=', 'log.timestamp', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'log.priority', 1 );
		$expr[] = $search->compare( '==', 'log.message', 'unittest message' );
		$expr[] = $search->compare( '==', 'log.request', 'unittest request' );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$criteria = $this->object->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'log.priority', 1 ) );
		$result = $this->object->searchItems( $criteria );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->createItem();
		$item->setMessage( 'unit test message' );
		$item->setRequest( 'unit test rqst' );
		$this->object->saveItem( $item );

		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setRequest( 'unit test request' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $item->getId() );

		$this->object->deleteItem( $item->getId() );

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $item->getTimestamp() === null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getFacility(), $itemSaved->getFacility() );
		$this->assertEquals( $item->getMessage(), $itemSaved->getMessage() );
		$this->assertEquals( $item->getRequest(), $itemSaved->getRequest() );
		$this->assertEquals( $item->getPriority(), $itemSaved->getPriority() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getFacility(), $itemUpd->getFacility() );
		$this->assertEquals( $itemExp->getMessage(), $itemUpd->getMessage() );
		$this->assertEquals( $itemExp->getRequest(), $itemUpd->getRequest() );
		$this->assertEquals( $itemExp->getPriority(), $itemUpd->getPriority() );

		$this->setExpectedException( '\\Aimeos\\MAdmin\\Log\\Exception' );
		$this->object->getItem( $item->getId() );
	}


	public function testLog()
	{
		$mock = $this->getMockBuilder( '\Aimeos\MAdmin\Log\Manager\Standard' )
			->setConstructorArgs( array( \TestHelperMShop::getContext() ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$mock->expects( $this->once() )->method( 'saveItem' );

		$mock->log( 'test' );
	}
}
