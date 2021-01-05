<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MAdmin\Log\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MAdmin\Log\Manager\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MAdmin\Log\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MAdmin\Log\Item\Iface::class, $this->object->create() );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MAdmin\Log\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'log', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attr ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attr );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \Aimeos\MAdmin\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'log.id', null );
		$expr[] = $search->compare( '!=', 'log.siteid', null );
		$expr[] = $search->compare( '==', 'log.facility', 'unittest facility' );
		$expr[] = $search->compare( '>=', 'log.timestamp', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'log.priority', 1 );
		$expr[] = $search->compare( '==', 'log.message', 'unittest message' );
		$expr[] = $search->compare( '==', 'log.request', 'unittest request' );

		$total = 0;
		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$criteria = $this->object->filter()->slice( 0, 1 );
		$criteria->setConditions( $criteria->compare( '==', 'log.priority', 1 ) );
		$result = $this->object->search( $criteria )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$this->assertEquals( $item, $this->object->get( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->create();
		$item->setMessage( 'unit test message' );
		$item->setRequest( 'unit test rqst' );
		$resultSaved = $this->object->save( $item );

		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setRequest( 'unit test request' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $item->getId() );

		$this->object->delete( $item->getId() );

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

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MAdmin\Log\Exception::class );
		$this->object->get( $item->getId() );
	}


	public function testLog()
	{
		$mock = $this->getMockBuilder( \Aimeos\MAdmin\Log\Manager\Standard::class )
			->setConstructorArgs( array( \TestHelperMShop::getContext() ) )
			->setMethods( array( 'save' ) )
			->getMock();

		$mock->expects( $this->once() )->method( 'save' );

		$mock->log( 'test' );
	}
}
