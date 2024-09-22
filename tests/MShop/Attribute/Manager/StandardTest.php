<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Attribute\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\MShop\Attribute\Manager\Standard( $this->context );
		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Lists( $this->object, $this->context );
		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Property( $this->object, $this->context );
		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Type( $this->object, $this->context );
		$this->object->setObject( $this->object );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDelete()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'attribute', $result );
		$this->assertContains( 'attribute/lists', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $obj ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $obj );
		}
	}


	public function testCreate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Attribute\Item\Iface::class, $this->object->create() );
	}


	public function testCreateType()
	{
		$item = $this->object->create( ['attribute.type' => 'color'] );
		$this->assertEquals( 'color', $item->getType() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'lists', 'unknown' );
	}


	public function testFind()
	{
		$item = $this->object->find( 'm', ['attribute/type', 'text'], 'product', 'size' );

		$this->assertEquals( 'm', $item->getCode() );
		$this->assertEquals( 'size', $item->getType() );
		$this->assertEquals( 'product', $item->getDomain() );
		$this->assertEquals( 1, count( $item->getListItems( 'text', null, null, false ) ) );
		$this->assertEquals( 1, count( $item->getRefItems( 'text', null, null, false ) ) );
		$this->assertInstanceof( \Aimeos\MShop\Common\Item\Type\Iface::class, $item->get( '.type' ) );
	}


	public function testFindInvalid()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->find( 'invalid' );
	}


	public function testFindMissing()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->find( 'm', [], 'product' );
	}


	public function testGet()
	{
		$itemA = $this->object->find( 'black', [], 'product', 'color' );
		$itemB = $this->object->get( $itemA->getId(), ['attribute/property', 'attribute/property/type', 'attribute/type', 'text'] );

		$this->assertEquals( $itemA->getId(), $itemB->getId() );
		$this->assertEquals( 1, count( $itemB->getPropertyItems() ) );
		$this->assertEquals( 1, count( $itemB->getListItems( null, null, null, false ) ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $itemB->getTypeItem() );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $itemB->getPropertyItems()->first()?->getTypeItem() );
	}


	public function testGetLists()
	{
		$itemA = $this->object->find( 'xxl', [], 'product', 'size' );
		$itemB = $this->object->get( $itemA->getId(), ['text'] );

		$this->assertEquals( $itemA->getId(), $itemB->getId() );
		$this->assertEquals( 1, count( $itemB->getListItems( 'text', null, null, false ) ) );
		$this->assertEquals( 1, count( $itemB->getRefItems( 'text', null, null, false ) ) );
	}


	public function testSaveUpdateDelete()
	{
		$item = $this->object->create();
		$item->setId( null );
		$item->setDomain( 'tmpDomainx' );
		$item->setCode( '106x' );
		$item->setLabel( '106x' );
		$item->setType( 'size' );
		$item->setPosition( 0 );
		$item->setStatus( 7 );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'tmpDomain' );
		$itemExp->setCode( '106' );
		$itemExp->setLabel( '106' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $item->getId() );

		$context = \TestHelper::context();

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->editor(), $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->editor(), $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $item->getId() );
	}


	public function testGetSavePropertyItems()
	{
		$item = $this->object->find( 'black', ['attribute/property'], 'product', 'color' );

		$item->setId( null )->setCode( 'xyz' );
		$this->object->save( $item );

		$item2 = $this->object->find( 'xyz', ['attribute/property'], 'product', 'color' );

		$this->object->delete( $item->getId() );

		$this->assertEquals( 1, count( $item->getPropertyItems() ) );
		$this->assertEquals( 1, count( $item2->getPropertyItems() ) );
	}


	public function testFilter()
	{
		$search = $this->object->filter();
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $search );
	}


	public function testSearch()
	{
		$item = $this->object->find( 'black', ['text'], 'product', 'color' );

		if( ( $listItem = $item->getListItems( 'text', 'default', null, false )->first() ) === null ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'attribute.id', null );
		$expr[] = $search->compare( '!=', 'attribute.siteid', null );
		$expr[] = $search->compare( '==', 'attribute.key', 'product|color|black' );
		$expr[] = $search->compare( '==', 'attribute.domain', 'product' );
		$expr[] = $search->compare( '==', 'attribute.type', 'color' );
		$expr[] = $search->compare( '==', 'attribute.code', 'black' );
		$expr[] = $search->compare( '==', 'attribute.position', 5 );
		$expr[] = $search->compare( '==', 'attribute.status', 0 );
		$expr[] = $search->compare( '==', 'attribute.label', 'product/color/black' );
		$expr[] = $search->compare( '>=', 'attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.editor', $this->context->editor() );

		$param = array( 'text', 'default', $listItem->getRefId() );
		$expr[] = $search->compare( '!=', $search->make( 'attribute:has', $param ), null );

		$param = array( 'text', 'default' );
		$expr[] = $search->compare( '!=', $search->make( 'attribute:has', $param ), null );

		$param = array( 'text' );
		$expr[] = $search->compare( '!=', $search->make( 'attribute:has', $param ), null );

		$param = array( 'htmlcolor', 'de', '#000000' );
		$expr[] = $search->compare( '!=', $search->make( 'attribute:prop', $param ), null );

		$param = array( 'htmlcolor', 'de' );
		$expr[] = $search->compare( '!=', $search->make( 'attribute:prop', $param ), null );

		$param = array( 'htmlcolor' );
		$expr[] = $search->compare( '!=', $search->make( 'attribute:prop', $param ), null );

		$total = 0;
		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, ['attribute/property'], $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, count( $results->first()->getPropertyItems() ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchBase()
	{
		//search with base criteria
		$search = $this->object->filter( true );
		$expr = array(
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '~=', 'attribute.code', '3' ),
			$search->compare( '==', 'attribute.editor', $this->context->editor() ),
			$search->getConditions(),
		);
		$search->setConditions( $search->and( $expr ) );
		$search->slice( 0, 5 );

		$total = 0;
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 10, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchTotal()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'attribute.type', 'size' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.editor', $this->context->editor() )
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 1 );

		$total = 0;
		$items = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 6, $total );
	}
}
