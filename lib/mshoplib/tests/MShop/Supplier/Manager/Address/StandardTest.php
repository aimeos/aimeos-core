<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Supplier\Manager\Address;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object = null;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::create( \TestHelperMShop::getContext() );
		$this->object = $supplierManager->getSubManager( 'address' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'supplier/address', $result );
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
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Address\Iface::class, $item );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '~=', 'supplier.address.company', 'Example company' ),
			$search->compare( '==', 'supplier.address.editor', $this->editor ),
		);
		$search->setConditions( $search->and( $conditions ) );

		$items = $this->object->search( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No address item with company "Metaways" found' );
		}

		$this->assertEquals( $item, $this->object->get( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'supplier.address.editor', $this->editor ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No address item found' );
		}

		$item->setId( null );
		$this->object->save( $item );
		$resultSaved = $itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;

		$itemExp->setCity( 'Berlin' );
		$itemExp->setState( 'Berlin' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCompany(), $itemSaved->getCompany() );
		$this->assertEquals( $item->getVatID(), $itemSaved->getVatID() );
		$this->assertEquals( $item->getSalutation(), $itemSaved->getSalutation() );
		$this->assertEquals( $item->getTitle(), $itemSaved->getTitle() );
		$this->assertEquals( $item->getFirstname(), $itemSaved->getFirstname() );
		$this->assertEquals( $item->getLastname(), $itemSaved->getLastname() );
		$this->assertEquals( $item->getAddress1(), $itemSaved->getAddress1() );
		$this->assertEquals( $item->getAddress2(), $itemSaved->getAddress2() );
		$this->assertEquals( $item->getAddress3(), $itemSaved->getAddress3() );
		$this->assertEquals( $item->getPostal(), $itemSaved->getPostal() );
		$this->assertEquals( $item->getCity(), $itemSaved->getCity() );
		$this->assertEquals( $item->getState(), $itemSaved->getState() );
		$this->assertEquals( $item->getCountryId(), $itemSaved->getCountryId() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getTelephone(), $itemSaved->getTelephone() );
		$this->assertEquals( $item->getEmail(), $itemSaved->getEmail() );
		$this->assertEquals( $item->getTelefax(), $itemSaved->getTelefax() );
		$this->assertEquals( $item->getWebsite(), $itemSaved->getWebsite() );
		$this->assertEquals( $item->getLongitude(), $itemSaved->getLongitude() );
		$this->assertEquals( $item->getLatitude(), $itemSaved->getLatitude() );
		$this->assertEquals( $item->getBirthday(), $itemSaved->getBirthday() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCompany(), $itemUpd->getCompany() );
		$this->assertEquals( $itemExp->getVatID(), $itemUpd->getVatID() );
		$this->assertEquals( $itemExp->getSalutation(), $itemUpd->getSalutation() );
		$this->assertEquals( $itemExp->getTitle(), $itemUpd->getTitle() );
		$this->assertEquals( $itemExp->getFirstname(), $itemUpd->getFirstname() );
		$this->assertEquals( $itemExp->getLastname(), $itemUpd->getLastname() );
		$this->assertEquals( $itemExp->getAddress1(), $itemUpd->getAddress1() );
		$this->assertEquals( $itemExp->getAddress2(), $itemUpd->getAddress2() );
		$this->assertEquals( $itemExp->getAddress3(), $itemUpd->getAddress3() );
		$this->assertEquals( $itemExp->getPostal(), $itemUpd->getPostal() );
		$this->assertEquals( $itemExp->getCity(), $itemUpd->getCity() );
		$this->assertEquals( $itemExp->getState(), $itemUpd->getState() );
		$this->assertEquals( $itemExp->getCountryId(), $itemUpd->getCountryId() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getTelephone(), $itemUpd->getTelephone() );
		$this->assertEquals( $itemExp->getEmail(), $itemUpd->getEmail() );
		$this->assertEquals( $itemExp->getTelefax(), $itemUpd->getTelefax() );
		$this->assertEquals( $itemExp->getWebsite(), $itemUpd->getWebsite() );
		$this->assertEquals( $itemExp->getLongitude(), $itemUpd->getLongitude() );
		$this->assertEquals( $itemExp->getLatitude(), $itemUpd->getLatitude() );
		$this->assertEquals( $itemExp->getBirthday(), $itemUpd->getBirthday() );

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


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->filter();

		$conditions = [];
		$conditions[] = $search->compare( '!=', 'supplier.address.id', null );
		$conditions[] = $search->compare( '!=', 'supplier.address.siteid', null );
		$conditions[] = $search->compare( '!=', 'supplier.address.parentid', null );
		$conditions[] = $search->compare( '==', 'supplier.address.company', 'Example company LLC' );
		$conditions[] = $search->compare( '==', 'supplier.address.vatid', 'DE999999999' );
		$conditions[] = $search->compare( '==', 'supplier.address.salutation', \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MS );
		$conditions[] = $search->compare( '==', 'supplier.address.title', '' );
		$conditions[] = $search->compare( '==', 'supplier.address.firstname', 'Good' );
		$conditions[] = $search->compare( '==', 'supplier.address.lastname', 'Unittest' );
		$conditions[] = $search->compare( '==', 'supplier.address.address1', 'Pickhuben' );
		$conditions[] = $search->compare( '==', 'supplier.address.address2', '2' );
		$conditions[] = $search->compare( '==', 'supplier.address.address3', '' );
		$conditions[] = $search->compare( '==', 'supplier.address.postal', '20457' );
		$conditions[] = $search->compare( '==', 'supplier.address.city', 'Hamburg' );
		$conditions[] = $search->compare( '==', 'supplier.address.state', 'Hamburg' );
		$conditions[] = $search->compare( '==', 'supplier.address.countryid', 'DE' );
		$conditions[] = $search->compare( '==', 'supplier.address.languageid', 'de' );
		$conditions[] = $search->compare( '==', 'supplier.address.telephone', '055544332211' );
		$conditions[] = $search->compare( '==', 'supplier.address.email', 'test@example.com' );
		$conditions[] = $search->compare( '==', 'supplier.address.telefax', '055544332212' );
		$conditions[] = $search->compare( '==', 'supplier.address.website', 'www.example.com' );
		$conditions[] = $search->compare( '==', 'supplier.address.longitude', '13.0' );
		$conditions[] = $search->compare( '==', 'supplier.address.latitude', '52.5' );
		$conditions[] = $search->compare( '==', 'supplier.address.birthday', '2001-01-01' );
		$conditions[] = $search->compare( '>=', 'supplier.address.mtime', '1970-01-01 00:00:00' );
		$conditions[] = $search->compare( '>=', 'supplier.address.ctime', '1970-01-01 00:00:00' );
		$conditions[] = $search->compare( '==', 'supplier.address.editor', $this->editor );

		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 1 );
		$result = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );

		foreach( $result as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}
}
