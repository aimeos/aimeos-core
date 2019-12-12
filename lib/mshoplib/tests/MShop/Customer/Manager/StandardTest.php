<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

namespace Aimeos\MShop\Customer\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $fixture;
	private $address;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Customer\Manager\Standard( \TestHelperMShop::getContext() );

		$this->fixture = array(
			'customer.label' => 'unitTest',
			'customer.status' => 2,
		);

		$this->address = new \Aimeos\MShop\Common\Item\Address\Standard( 'customer.' );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->fixture, $this->address );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'customer', $result );
		$this->assertContains( 'customer/address', $result );
		$this->assertContains( 'customer/group', $result );
		$this->assertContains( 'customer/lists', $result );
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
		$item = $this->object->createItem();
		$this->assertInstanceOf( \Aimeos\MShop\Customer\Item\Iface::class, $item );
	}


	public function testCreateAddressItem()
	{
		$item = $this->object->createAddressItem();
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Address\Iface::class, $item );
	}


	public function testCreateListsItem()
	{
		$item = $this->object->createListsItem();
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Lists\Iface::class, $item );
	}


	public function testCreatePropertyItem()
	{
		$item = $this->object->createPropertyItem();
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Property\Iface::class, $item );
	}


	public function testFindItem()
	{
		$item = $this->object->findItem( 'UTC003' );

		$this->assertEquals( 'UTC003', $item->getCode() );
	}


	public function testGetItem()
	{
		$domains = ['text', 'customer/property' => ['newsletter']];
		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'customer.code', 'UTC001' ),
			$search->compare( '==', 'customer.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search, $domains );

		if( ( $expected = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No customer item with code "UTC001" found' );
		}

		$actual = $this->object->getItem( $expected->getId(), $domains );
		$payAddr = $actual->getPaymentAddress();

		$this->assertEquals( 'unitCustomer001', $actual->getLabel() );
		$this->assertEquals( 'UTC001', $actual->getCode() );
		$this->assertEquals( 'mr', $payAddr->getSalutation() );
		$this->assertEquals( 'Example company', $payAddr->getCompany() );
		$this->assertEquals( 'Dr', $payAddr->getTitle() );
		$this->assertEquals( 'Our', $payAddr->getFirstname() );
		$this->assertEquals( 'Unittest', $payAddr->getLastname() );
		$this->assertEquals( 'Pickhuben', $payAddr->getAddress1() );
		$this->assertEquals( '2-4', $payAddr->getAddress2() );
		$this->assertEquals( '', $payAddr->getAddress3() );
		$this->assertEquals( '20457', $payAddr->getPostal() );
		$this->assertEquals( 'Hamburg', $payAddr->getCity() );
		$this->assertEquals( 'Hamburg', $payAddr->getState() );
		$this->assertEquals( 'de', $payAddr->getLanguageId() );
		$this->assertEquals( 'DE', $payAddr->getCountryId() );
		$this->assertEquals( '055544332211', $payAddr->getTelephone() );
		$this->assertEquals( 'test@example.com', $payAddr->getEMail() );
		$this->assertEquals( '055544332212', $payAddr->getTelefax() );
		$this->assertEquals( 'www.example.com', $payAddr->getWebsite() );
		$this->assertEquals( '10.0', $payAddr->getLongitude() );
		$this->assertEquals( '50.0', $payAddr->getLatitude() );
		$this->assertEquals( 1, $actual->getStatus() );
		$this->assertEquals( 'core:lib/mshoplib', $actual->getEditor() );

		$this->assertEquals( $expected, $actual );
		$this->assertEquals( 1, count( $actual->getListItems( 'text', null, null, false ) ) );
		$this->assertEquals( 1, count( $actual->getRefItems( 'text', null, null, false ) ) );
		$this->assertEquals( 1, count( $actual->getPropertyItems() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->createItem();

		$item->setCode( 'unitTest' );
		$item->setLabel( 'unitTest' );
		$item->setGroups( array( 1, 2, 3 ) );
		$item = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId(), array( 'customer/group' ) );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unitTest2' );
		$itemExp->setLabel( 'unitTest2' );
		$itemExp->setGroups( array( 2, 4 ) );
		$itemExp = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId(), array( 'customer/group' ) );

		$this->object->deleteItem( $item->getId() );


		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $itemSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $itemUpd );

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getBirthday(), $itemSaved->getBirthday() );
		$this->assertEquals( $item->getPassword(), $itemSaved->getPassword() );
		$this->assertEquals( $item->getGroups(), $itemSaved->getGroups() );
		$this->assertEquals( $itemSaved->getPaymentAddress()->getId(), $itemSaved->getId() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getBirthday(), $itemUpd->getBirthday() );
		$this->assertEquals( $itemExp->getPassword(), $itemUpd->getPassword() );
		$this->assertEquals( $itemExp->getGroups(), $itemUpd->getGroups() );
		$this->assertEquals( $itemUpd->getPaymentAddress()->getId(), $itemUpd->getId() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );


		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getItem( $item->getId() );
	}


	public function testGetSaveAddressItems()
	{
		$item = $this->object->findItem( 'UTC001', ['customer/address'] );

		$item->setId( null )->setCode( 'xyz' );
		$item->getPaymentAddress()->setEmail( 'unittest@xyz.com' );
		$item->addAddressItem( new \Aimeos\MShop\Common\Item\Address\Standard( 'customer.address.' ) );
		$this->object->saveItem( $item );

		$item2 = $this->object->findItem( 'xyz', ['customer/address'] );

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 2, count( $item->getAddressItems() ) );
		$this->assertEquals( 2, count( $item2->getAddressItems() ) );
	}


	public function testGetSavePropertyItems()
	{
		$item = $this->object->findItem( 'UTC001', ['customer/property'] );

		$item->setId( null )->setCode( 'xyz' );
		$item->getPaymentAddress()->setEmail( 'unittest@xyz.com' );
		$this->object->saveItem( $item );

		$item2 = $this->object->findItem( 'xyz', ['customer/property'] );

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 1, count( $item->getPropertyItems() ) );
		$this->assertEquals( 1, count( $item2->getPropertyItems() ) );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->createSearch() );
	}


	public function testSearchItems()
	{
		$item = $this->object->findItem( 'UTC001', ['text'] );

		if( ( $listItem = current( $item->getListItems( 'text', 'default' ) ) ) === false ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'customer.id', null );
		$expr[] = $search->compare( '==', 'customer.label', 'unitCustomer001' );
		$expr[] = $search->compare( '==', 'customer.code', 'UTC001' );
		$expr[] = $search->compare( '==', 'customer.birthday', null );
		$expr[] = $search->compare( '>=', 'customer.password', '' );
		$expr[] = $search->compare( '==', 'customer.status', 1 );
		$expr[] = $search->compare( '>', 'customer.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>', 'customer.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'customer.editor', $this->editor );

		$expr[] = $search->compare( '==', 'customer.salutation', 'mr' );
		$expr[] = $search->compare( '==', 'customer.company', 'Example company' );
		$expr[] = $search->compare( '==', 'customer.vatid', 'DE999999999' );
		$expr[] = $search->compare( '==', 'customer.title', 'Dr' );
		$expr[] = $search->compare( '==', 'customer.firstname', 'Our' );
		$expr[] = $search->compare( '==', 'customer.lastname', 'Unittest' );
		$expr[] = $search->compare( '==', 'customer.address1', 'Pickhuben' );
		$expr[] = $search->compare( '==', 'customer.address2', '2-4' );
		$expr[] = $search->compare( '==', 'customer.address3', '' );
		$expr[] = $search->compare( '==', 'customer.postal', '20457' );
		$expr[] = $search->compare( '==', 'customer.city', 'Hamburg' );
		$expr[] = $search->compare( '==', 'customer.state', 'Hamburg' );
		$expr[] = $search->compare( '==', 'customer.languageid', 'de' );
		$expr[] = $search->compare( '==', 'customer.countryid', 'DE' );
		$expr[] = $search->compare( '==', 'customer.telephone', '055544332211' );
		$expr[] = $search->compare( '==', 'customer.email', 'test@example.com' );
		$expr[] = $search->compare( '==', 'customer.telefax', '055544332212' );
		$expr[] = $search->compare( '==', 'customer.website', 'www.example.com' );
		$expr[] = $search->compare( '==', 'customer.longitude', '10.0' );
		$expr[] = $search->compare( '==', 'customer.latitude', '50.0' );

		$param = ['text', 'default', $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->createFunction( 'customer:has', $param ), null );

		$param = ['text', 'default'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'customer:has', $param ), null );

		$param = ['text'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'customer:has', $param ), null );

		$param = ['newsletter', null, '1'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'customer:prop', $param ), null );

		$param = ['newsletter', null];
		$expr[] = $search->compare( '!=', $search->createFunction( 'customer:prop', $param ), null );

		$param = ['newsletter'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'customer:prop', $param ), null );

		$expr[] = $search->compare( '!=', 'customer.address.id', null );
		$expr[] = $search->compare( '!=', 'customer.address.parentid', null );
		$expr[] = $search->compare( '==', 'customer.address.salutation', 'mr' );
		$expr[] = $search->compare( '==', 'customer.address.company', 'Example company' );
		$expr[] = $search->compare( '==', 'customer.address.vatid', 'DE999999999' );
		$expr[] = $search->compare( '==', 'customer.address.title', 'Dr' );
		$expr[] = $search->compare( '==', 'customer.address.firstname', 'Our' );
		$expr[] = $search->compare( '==', 'customer.address.lastname', 'Unittest' );
		$expr[] = $search->compare( '==', 'customer.address.address1', 'Pickhuben' );
		$expr[] = $search->compare( '==', 'customer.address.address2', '2-4' );
		$expr[] = $search->compare( '==', 'customer.address.address3', '' );
		$expr[] = $search->compare( '==', 'customer.address.postal', '20457' );
		$expr[] = $search->compare( '==', 'customer.address.city', 'Hamburg' );
		$expr[] = $search->compare( '==', 'customer.address.state', 'Hamburg' );
		$expr[] = $search->compare( '==', 'customer.address.languageid', 'de' );
		$expr[] = $search->compare( '==', 'customer.address.countryid', 'DE' );
		$expr[] = $search->compare( '==', 'customer.address.telephone', '055544332211' );
		$expr[] = $search->compare( '==', 'customer.address.email', 'test@example.com' );
		$expr[] = $search->compare( '==', 'customer.address.telefax', '055544332212' );
		$expr[] = $search->compare( '==', 'customer.address.website', 'www.example.com' );
		$expr[] = $search->compare( '==', 'customer.address.longitude', '10.0' );
		$expr[] = $search->compare( '==', 'customer.address.latitude', '50.0' );
		$expr[] = $search->compare( '==', 'customer.address.position', 0 );
		$expr[] = $search->compare( '>=', 'customer.address.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'customer.address.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'customer.address.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search );
		$this->assertEquals( 1, count( $result ) );
	}


	public function testSearchItemsTotal()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.address.editor', $this->editor ) );
		$search->setSlice( 0, 2 );

		$total = 0;
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 2, count( $results ) );
		$this->assertEquals( 3, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsCriteria()
	{
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'customer.address.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$this->assertEquals( 2, count( $this->object->searchItems( $search ) ) );
	}


	public function testSearchItemsRef()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );

		$results = $this->object->searchItems( $search, ['customer/address', 'text'] );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \Exception( 'No customer item for "UTC001" available' );
		}

		$this->assertEquals( 1, count( $item->getRefItems( 'text', null, null, false ) ) );
		$this->assertEquals( 1, count( $item->getAddressItems() ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'address' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'address', 'Standard' ) );

		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'address', 'unknown' );
	}
}
