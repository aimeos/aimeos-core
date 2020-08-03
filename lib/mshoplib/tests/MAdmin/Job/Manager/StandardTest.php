<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MAdmin\Job\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MAdmin\Job\Manager\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MAdmin\Job\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MAdmin\Job\Item\Iface::class, $this->object->createItem() );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MAdmin\Job\Manager\Iface::class, $this->object->deleteItems( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'job', $result );
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
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'job.id', null );
		$expr[] = $search->compare( '!=', 'job.siteid', null );
		$expr[] = $search->compare( '==', 'job.label', 'unittest job' );
		$expr[] = $search->compare( '==', 'job.method', 'controller.method' );
		$expr[] = $search->compare( '==', 'job.parameter', '{"items":"testfile.ext"}' );
		$expr[] = $search->compare( '==', 'job.result', '{"items":"testfile.ext"}' );
		$expr[] = $search->compare( '==', 'job.status', 0 );
		$expr[] = $search->compare( '>=', 'job.ctime', '2000-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'job.mtime', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'job.editor', '' );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$criteria = $this->object->createSearch()->setSlice( 0, 1 );
		$criteria->setConditions( $criteria->compare( '==', 'job.method', 'controller.method' ) );
		$result = $this->object->searchItems( $criteria )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->createItem();
		$item->setLabel( 'unit test' );
		$item->setMethod( 'crtl.method' );
		$resultSaved = $this->object->saveItem( $item );

		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setMethod( 'controll.method' );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $item->getId() );

		$this->object->deleteItem( $item->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getResult(), $itemSaved->getResult() );
		$this->assertEquals( $item->getMethod(), $itemSaved->getMethod() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemSaved->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getResult(), $itemUpd->getResult() );
		$this->assertEquals( $itemExp->getMethod(), $itemUpd->getMethod() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MAdmin\Job\Exception::class );
		$this->object->getItem( $item->getId() );
	}
}
