<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Service\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Service\Manager\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $this->object->create() );
	}


	public function testCreateItemType()
	{
		$item = $this->object->create( ['service.type' => 'delivery'] );
		$this->assertEquals( 'delivery', $item->getType() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'service.code', 'unitdeliverycode' ),
			$search->compare( '==', 'service.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );

		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No service provider item found.' );
		}

		$item->setId( null );
		$item->setCode( 'newstaticdelivery' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( '2ndChang' );
		$itemExp->setLabel( '2ndNameChanged' );
		$itemExp->setPosition( '1' );
		$itemExp->setStatus( '1' );
		$itemExp->setProvider( 'HS' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $item->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getProvider(), $itemSaved->getProvider() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getProvider(), $itemUpd->getProvider() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testFindItem()
	{
		$item = $this->object->find( 'unitdeliverycode' );

		$this->assertEquals( 'unitdeliverycode', $item->getCode() );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'service.code', 'unitdeliverycode' ),
			$search->compare( '==', 'service.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$result = $this->object->search( $search, array( 'text' ) )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$this->assertEquals( $item, $this->object->get( $item->getId(), array( 'text' ) ) );
		$this->assertEquals( 5, count( $item->getRefItems( 'text' ) ) );
	}


	public function testSearchItem()
	{
		$item = $this->object->find( 'unitdeliverycode', ['text'] );

		if( ( $listItem = $item->getListItems( 'text', 'unittype1' )->first() ) === null ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'service.id', null );
		$expr[] = $search->compare( '!=', 'service.siteid', null );
		$expr[] = $search->compare( '==', 'service.type', 'delivery' );
		$expr[] = $search->compare( '>=', 'service.position', 0 );
		$expr[] = $search->compare( '==', 'service.code', 'unitdeliverycode' );
		$expr[] = $search->compare( '==', 'service.label', 'unitlabel' );
		$expr[] = $search->compare( '==', 'service.provider', 'Standard' );
		$expr[] = $search->compare( '==', 'service.datestart', null );
		$expr[] = $search->compare( '==', 'service.dateend', null );
		$expr[] = $search->compare( '!=', 'service.config', null );
		$expr[] = $search->compare( '==', 'service.status', 1 );
		$expr[] = $search->compare( '>=', 'service.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.editor', $this->editor );

		$param = ['text', 'unittype1', $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->make( 'service:has', $param ), null );

		$param = ['text', 'unittype1'];
		$expr[] = $search->compare( '!=', $search->make( 'service:has', $param ), null );

		$param = ['text'];
		$expr[] = $search->compare( '!=', $search->make( 'service:has', $param ), null );

		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemBase()
	{
		$search = $this->object->filter( true );
		$expr = array(
			$search->compare( '==', 'service.provider', 'unitprovider' ),
			$search->compare( '==', 'service.editor', $this->editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->and( $expr ) );
		$this->assertEquals( 0, count( $this->object->search( $search )->toArray() ) );
	}


	public function testGetProvider()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'service.type', 'delivery' ),
			$search->compare( '==', 'service.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 1 );
		$result = $this->object->search( $search )->toArray();

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No service item found' );
		}

		$item->setProvider( 'Standard,Example' );
		$provider = $this->object->getProvider( $item, 'delivery' );

		$this->assertInstanceOf( \Aimeos\MShop\Service\Provider\Iface::class, $provider );
		$this->assertInstanceOf( \Aimeos\MShop\Service\Provider\Decorator\Example::class, $provider );


		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getProvider( $this->object->create(), 'payment' );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'lists', 'unknown' );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'service', $result );
		$this->assertContains( 'service/lists', $result );
	}


	public function testGetSearchAttributes()
	{
		$attribs = $this->object->getSearchAttributes();
		foreach( $attribs as $obj ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $obj );
		}
	}


	public function testCreateSearch()
	{
		$search = $this->object->filter();
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $search );
	}
}
