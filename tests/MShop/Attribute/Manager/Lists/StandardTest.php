<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Attribute\Manager\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $editor = '';


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->editor = $this->context->editor();
		$manager = \Aimeos\MShop::create( $this->context, 'attribute', 'Standard' );
		$this->object = $manager->getSubManager( 'lists', 'Standard' );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testGetResourceType()
	{
		$this->assertContains( 'attribute/lists', $this->object->getResourceType() );
	}


	public function testCreate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $this->object->create() );
	}


	public function testCreateType()
	{
		$item = $this->object->create( ['attribute.lists.type' => 'default'] );
		$this->assertEquals( 'default', $item->getType() );
	}


	public function testGet()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$this->assertEquals( $item, $this->object->get( $item->getId() ) );
	}


	public function testSaveUpdateDelete()
	{
		$search = $this->object->filter();
		$items = $this->object->search( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item->setId( null );
		$item->setDomain( 'unittest' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest1' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );

		$context = \TestHelper::context();

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getRefId(), $itemSaved->getRefId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );

		$this->assertEquals( $context->editor(), $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getRefId(), $itemUpd->getRefId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );

		$this->assertEquals( $context->editor(), $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testSearch()
	{
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'attribute.lists.id', null );
		$expr[] = $search->compare( '!=', 'attribute.lists.siteid', null );
		$expr[] = $search->compare( '!=', 'attribute.lists.parentid', null );
		$expr[] = $search->compare( '==', 'attribute.lists.domain', 'text' );
		$expr[] = $search->compare( '==', 'attribute.lists.type', 'default' );
		$expr[] = $search->compare( '>', 'attribute.lists.refid', 0 );
		$expr[] = $search->compare( '==', 'attribute.lists.datestart', '2000-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.lists.dateend', '2001-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'attribute.lists.config', null );
		$expr[] = $search->compare( '==', 'attribute.lists.position', 0 );
		$expr[] = $search->compare( '==', 'attribute.lists.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.lists.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.lists.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.lists.editor', $this->editor );

		$total = 0;
		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchBase()
	{
		//search with base criteria
		$search = $this->object->filter( true );
		$conditions = array(
			$search->compare( '==', 'attribute.lists.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		$this->assertEquals( 28, count( $results ) );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchTotal()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'attribute.lists.domain', 'text' ),
			$search->compare( '==', 'attribute.lists.editor', $this->editor ),
		);
		$search->setConditions( $search->and( $conditions ) );
		$items = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 25, $total );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}
}
