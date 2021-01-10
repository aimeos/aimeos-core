<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Supplier\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object = null;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Supplier\Manager\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'supplier', $result );
		$this->assertContains( 'supplier/address', $result );
		$this->assertContains( 'supplier/lists', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute )
		{
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}

	public function testCreateItem()
	{
		$item = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Supplier\Item\Iface::class, $item );
	}


	public function testFindItem()
	{
		$item = $this->object->find( 'unitSupplier001' );

		$this->assertEquals( 'unitSupplier001', $item->getCode() );
	}


	public function testGetItem()
	{
		$item = $this->object->find( 'unitSupplier001' );

		$this->assertEquals( $item, $this->object->get( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->find( 'unitSupplier001' );

		$item->setId( null );
		$item->setCode( 'unitTest01' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'unitTest' );
		$itemExp->setStatus( 2 );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}

	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter() );
	}


	public function testSearchItem()
	{
		$item = $this->object->find( 'unitSupplier001', ['text'] );
		$listItem = $item->getListItems( 'text', 'default' )->first( new \RuntimeException( 'No list item found' ) );

		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'supplier.id', null );
		$expr[] = $search->compare( '!=', 'supplier.siteid', null );
		$expr[] = $search->compare( '==', 'supplier.label', 'Unit Supplier 001' );
		$expr[] = $search->compare( '==', 'supplier.code', 'unitSupplier001' );
		$expr[] = $search->compare( '==', 'supplier.status', 1 );
		$expr[] = $search->compare( '>=', 'supplier.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'supplier.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.editor', $this->editor );

		$param = ['text', 'default', $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->make( 'supplier:has', $param ), null );

		$param = ['text', 'default'];
		$expr[] = $search->compare( '!=', $search->make( 'supplier:has', $param ), null );

		$param = ['text'];
		$expr[] = $search->compare( '!=', $search->make( 'supplier:has', $param ), null );

		$expr[] = $search->compare( '!=', 'supplier.address.id', null );
		$expr[] = $search->compare( '!=', 'supplier.address.siteid', null );
		$expr[] = $search->compare( '!=', 'supplier.address.parentid', 0 );
		$expr[] = $search->compare( '==', 'supplier.address.company', 'Example company' );
		$expr[] = $search->compare( '==', 'supplier.address.vatid', 'DE999999999' );
		$expr[] = $search->compare( '==', 'supplier.address.salutation', 'mrs' );
		$expr[] = $search->compare( '==', 'supplier.address.title', '' );
		$expr[] = $search->compare( '==', 'supplier.address.firstname', 'Our' );
		$expr[] = $search->compare( '==', 'supplier.address.lastname', 'Unittest' );
		$expr[] = $search->compare( '==', 'supplier.address.address1', 'Pickhuben' );
		$expr[] = $search->compare( '==', 'supplier.address.address2', '2' );
		$expr[] = $search->compare( '==', 'supplier.address.address3', '' );
		$expr[] = $search->compare( '==', 'supplier.address.postal', '20457' );
		$expr[] = $search->compare( '==', 'supplier.address.city', 'Hamburg' );
		$expr[] = $search->compare( '==', 'supplier.address.languageid', 'de' );
		$expr[] = $search->compare( '==', 'supplier.address.countryid', 'DE' );
		$expr[] = $search->compare( '==', 'supplier.address.telephone', '055544332211' );
		$expr[] = $search->compare( '==', 'supplier.address.telefax', '055544332212' );
		$expr[] = $search->compare( '==', 'supplier.address.email', 'test@example.com' );
		$expr[] = $search->compare( '==', 'supplier.address.website', 'www.example.com' );
		$expr[] = $search->compare( '==', 'supplier.address.position', 0 );
		$expr[] = $search->compare( '>=', 'supplier.address.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'supplier.address.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.address.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search )->toArray();
		$this->assertEquals( 1, count( $result ) );
	}


	public function testSearchItemTotal()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'supplier.editor', $this->editor ) );
		$search->slice( 0, 2 );

		$total = 0;
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 2, count( $results ) );
		$this->assertEquals( 3, $total );
	}


	public function testSearchItemCriteria()
	{
		$search = $this->object->filter( true );
		$conditions = array(
			$search->compare( '==', 'supplier.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		$this->assertEquals( 2, count( $results ) );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsRef()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'supplier.code', 'unitSupplier001' ) );

		$item = $this->object->search( $search, ['supplier/address', 'text'] )->first();

		$this->assertInstanceOf( \Aimeos\MShop\Supplier\Item\Iface::class, $item );
		$this->assertEquals( 3, count( $item->getRefItems( 'text', null, null, false ) ) );
		$this->assertEquals( 1, count( $item->getAddressItems() ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'address' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'address', 'Standard' ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'address', 'unknown' );
	}
}
