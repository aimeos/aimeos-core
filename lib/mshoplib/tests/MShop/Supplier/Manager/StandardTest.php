<?php

namespace Aimeos\MShop\Supplier\Manager;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object = null;
	private $editor = '';


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->editor = \TestHelper::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Supplier\Manager\Standard( \TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}

	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute )
		{
			$this->assertInstanceOf( '\\Aimeos\\MW\\Common\\Criteria\\Attribute\\Iface', $attribute );
		}
	}

	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Supplier\\Item\\Iface', $item );
	}

	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'supplier.label', 'unitSupplier' ),
			$search->compare( '==', 'supplier.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'No supplier item with label "unitSupplier" found' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId() ) );
	}

	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'supplier.label', 'unitSupplier001' ),
			$search->compare( '==', 'supplier.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'No supplier item found' );
		}

		$item->setId( null );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'unitTest' );
		$itemExp->setStatus( 2 );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


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

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}

	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Common\\Criteria\\Iface', $this->object->createSearch() );
	}


	public function testSearchItem()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'supplier.id', null );
		$expr[] = $search->compare( '!=', 'supplier.siteid', null );
		$expr[] = $search->compare( '==', 'supplier.label', 'unitSupplier001' );
		$expr[] = $search->compare( '==', 'supplier.code', 'unitCode001' );
		$expr[] = $search->compare( '==', 'supplier.status', 1 );
		$expr[] = $search->compare( '>=', 'supplier.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'supplier.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'supplier.address.id', null );
		$expr[] = $search->compare( '!=', 'supplier.address.siteid', null );
		$expr[] = $search->compare( '!=', 'supplier.address.refid', '' );
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
		$expr[] = $search->compare( '==', 'supplier.address.countryid', 'de' );
		$expr[] = $search->compare( '==', 'supplier.address.telephone', '055544332211' );
		$expr[] = $search->compare( '==', 'supplier.address.telefax', '055544332212' );
		$expr[] = $search->compare( '==', 'supplier.address.email', 'test@example.com' );
		$expr[] = $search->compare( '==', 'supplier.address.website', 'www.example.com' );
		$expr[] = $search->compare( '==', 'supplier.address.flag', 0 );
		$expr[] = $search->compare( '==', 'supplier.address.position', 0 );
		$expr[] = $search->compare( '>=', 'supplier.address.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'supplier.address.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.address.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $result ) );


		//search without base criteria
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'supplier.editor', $this->editor ) );
		$search->setSlice( 0, 2 );
		$results = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 2, count( $results ) );
		$this->assertEquals( 3, $total );

		//search with base criteria
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'supplier.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );
		$this->assertEquals( 2, count( $results ) );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'address' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'address', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'address', 'unknown' );
	}
}