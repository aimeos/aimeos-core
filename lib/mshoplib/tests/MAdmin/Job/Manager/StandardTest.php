<?php

namespace Aimeos\MAdmin\Job\Manager;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new \Aimeos\MAdmin\Job\Manager\Standard( \TestHelperMShop::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
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
		$this->assertInstanceOf( '\\Aimeos\\MAdmin\\Job\\Item\\Iface', $this->object->createItem() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'job', $result );
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
		$criteria->setConditions( $criteria->compare( '==', 'job.method', 'controller.method' ) );
		$result = $this->object->searchItems( $criteria );

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
		$this->object->saveItem( $item );

		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setMethod( 'controll.method' );
		$this->object->saveItem( $itemExp );
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

		$this->setExpectedException( '\\Aimeos\\MAdmin\\Job\\Exception' );
		$this->object->getItem( $item->getId() );
	}
}
