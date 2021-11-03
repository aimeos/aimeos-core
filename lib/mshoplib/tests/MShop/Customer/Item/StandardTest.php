<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Customer\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;
	private $address;


	protected function setUp() : void
	{
		$this->values = array(
			'customer.id' => 541,
			'customer.siteid' => 123,
			'customer.label' => 'unitObject',
			'customer.code' => '12345ABCDEF',
			'customer.status' => 1,
			'customer.groups' => [1, 2],
			'customer.password' => '',
			'customer.dateverified' => null,
			'customer.company' => 'unitCompany',
			'customer.vatid' => 'DE999999999',
			'customer.salutation' => \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR,
			'customer.title' => 'Dr.',
			'customer.firstname' => 'firstunit',
			'customer.lastname' => 'lastunit',
			'customer.address1' => 'unit str.',
			'customer.address2' => ' 166',
			'customer.address3' => '4.OG',
			'customer.postal' => '22769',
			'customer.city' => 'Hamburg',
			'customer.state' => 'Hamburg',
			'customer.countryid' => 'DE',
			'customer.languageid' => 'de',
			'customer.telephone' => '05554433221',
			'customer.email' => 'test@example.com',
			'customer.telefax' => '05554433222',
			'customer.website' => 'www.example.com',
			'customer.longitude' => '10.0',
			'customer.latitude' => '50.0',
			'customer.birthday' => '2000-01-01',
			'customer.mtime'=> '2010-01-05 00:00:05',
			'customer.ctime'=> '2010-01-01 00:00:00',
			'customer.editor' => 'unitTestUser',
			'additional' => 'something',
		);

		$this->address = new \Aimeos\MShop\Common\Item\Address\Standard( 'customer.', $this->values );

		$addresses = array(
			new \Aimeos\MShop\Customer\Item\Address\Standard( 'customer.address.', ['customer.address.position' => 0] ),
			new \Aimeos\MShop\Customer\Item\Address\Standard( 'customer.address.', ['customer.address.position' => 1] ),
		);

		$this->object = new \Aimeos\MShop\Customer\Item\Standard( $this->address, $this->values, [], [], $addresses, [], null, 'mshop' );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->address, $this->values );
	}


	public function testGet()
	{
		$this->assertEquals( 'something', $this->object->additional );
		$this->assertNull( $this->object->missing );
	}


	public function testIsset()
	{
		$this->assertTrue( isset( $this->object->additional ) );
		$this->assertFalse( isset( $this->object->missing ) );
	}


	public function testGetId()
	{
		$this->assertEquals( 541, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Customer\Item\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 123, $this->object->getSiteId() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unitObject', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'newName' );

		$this->assertInstanceOf( \Aimeos\MShop\Customer\Item\Iface::class, $return );
		$this->assertEquals( 'newName', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( '12345ABCDEF', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'neuerUser@unittest.com' );

		$this->assertInstanceOf( \Aimeos\MShop\Customer\Item\Iface::class, $return );
		$this->assertEquals( 'neuerUser@unittest.com', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Customer\Item\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSetPassword()
	{
		$return = $this->object->setPassword( '08154712' );

		$this->assertInstanceOf( \Aimeos\MShop\Customer\Item\Iface::class, $return );
		$this->assertEquals( '08154712', $this->object->getPassword() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2010-01-01 00:00:00', $this->object->getTimeCreated() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2010-01-05 00:00:05', $this->object->getTimeModified() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testGetDateVerified()
	{
		$this->assertEquals( null, $this->object->getDateVerified() );
	}


	public function testSetDateVerified()
	{
		$return = $this->object->setDateVerified( '2010-02-01' );

		$this->assertInstanceOf( \Aimeos\MShop\Customer\Item\Iface::class, $return );
		$this->assertEquals( '2010-02-01', $this->object->getDateVerified() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetGroups()
	{
		$listValues = array( 'domain' => 'customer/group', 'type' => 'default', 'refid' => 123 );
		$listItems = array( 'customer/group' => array( new \Aimeos\MShop\Common\Item\Lists\Standard( '', $listValues ) ) );
		$object = new \Aimeos\MShop\Customer\Item\Standard( $this->address, [], $listItems );

		$this->assertEquals( array( 123 ), $object->getGroups() );
	}


	public function testSetGroups()
	{
		$this->object->setGroups( array( 1, 2, 3 ) );

		$this->assertEquals( array( 1, 2, 3 ), $this->object->getGroups() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPaymentAddress()
	{
		$address = $this->object->getPaymentAddress();
		$this->assertEquals( $address->getCompany(), 'unitCompany' );
		$this->assertEquals( $address->getVatID(), 'DE999999999' );
		$this->assertEquals( $address->getSalutation(), \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR );
		$this->assertEquals( $address->getTitle(), 'Dr.' );
		$this->assertEquals( $address->getFirstname(), 'firstunit' );
		$this->assertEquals( $address->getLastname(), 'lastunit' );
		$this->assertEquals( $address->getAddress1(), 'unit str.' );
		$this->assertEquals( $address->getAddress2(), ' 166' );
		$this->assertEquals( $address->getAddress3(), '4.OG' );
		$this->assertEquals( $address->getPostal(), '22769' );
		$this->assertEquals( $address->getCity(), 'Hamburg' );
		$this->assertEquals( $address->getState(), 'Hamburg' );
		$this->assertEquals( $address->getCountryId(), 'DE' );
		$this->assertEquals( $address->getLanguageId(), 'de' );
		$this->assertEquals( $address->getTelephone(), '05554433221' );
		$this->assertEquals( $address->getEmail(), 'test@example.com' );
		$this->assertEquals( $address->getTelefax(), '05554433222' );
		$this->assertEquals( $address->getWebsite(), 'www.example.com' );
		$this->assertEquals( $address->getLongitude(), '10.0' );
		$this->assertEquals( $address->getLatitude(), '50.0' );
		$this->assertEquals( $address->getBirthday(), '2000-01-01' );
	}


	public function testSetPaymentAddress()
	{
		$this->address->setCompany( 'unitCompany0815' );
		$return = $this->object->setPaymentAddress( $this->address );

		$this->assertInstanceOf( \Aimeos\MShop\Customer\Item\Iface::class, $return );
		$this->assertEquals( $this->address, $this->object->getPaymentAddress() );
	}


	public function testGetAddressItems()
	{
		$i = 0;
		$list = $this->object->getAddressItems();
		$this->assertEquals( 2, count( $list ) );

		foreach( $list as $item )
		{
			$this->assertEquals( $i++, $item->getPosition() );
			$this->assertInstanceOf( \Aimeos\MShop\Customer\Item\Address\Iface::class, $item );
		}
	}


	public function testAddAddressItem()
	{
		$this->object->addAddressItem( $this->address );

		$this->assertEquals( 3, count( $this->object->getAddressItems() ) );
	}


	public function testDeleteAddressItem()
	{
		$this->object->addAddressItem( $this->address );
		$this->object->deleteAddressItem( $this->address );

		$this->assertEquals( 2, count( $this->object->getAddressItems() ) );
		$this->assertEquals( 1, count( $this->object->getAddressItemsDeleted() ) );
	}


	public function testDeleteAddressItems()
	{
		$this->object->addAddressItem( $this->address );
		$this->object->deleteAddressItems( [$this->address] );

		$this->assertEquals( 2, count( $this->object->getAddressItems() ) );
		$this->assertEquals( 1, count( $this->object->getAddressItemsDeleted() ) );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'customer', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$address = new \Aimeos\MShop\Common\Item\Address\Standard( 'customer.' );
		$item = new \Aimeos\MShop\Customer\Item\Standard( $address );

		$list = $entries = array(
			'customer.id' => 1,
			'customer.code' => '12345ABCDEF',
			'customer.label' => 'unitObject',
			'customer.status' => 1,
			'customer.password' => '',
			'customer.dateverified' => null,
			'customer.company' => 'unitCompany',
			'customer.vatid' => 'DE999999999',
			'customer.salutation' => \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR,
			'customer.title' => 'Dr.',
			'customer.firstname' => 'firstunit',
			'customer.lastname' => 'lastunit',
			'customer.address1' => 'unit str.',
			'customer.address2' => ' 166',
			'customer.address3' => '4.OG',
			'customer.postal' => '22769',
			'customer.city' => 'Hamburg',
			'customer.state' => 'Hamburg',
			'customer.countryid' => 'DE',
			'customer.languageid' => 'de',
			'customer.telephone' => '05554433221',
			'customer.email' => 'test@example.com',
			'customer.telefax' => '05554433222',
			'customer.website' => 'www.example.com',
			'customer.longitude' => '10.0',
			'customer.latitude' => '53.5',
			'customer.birthday' => '1999-01-01',
			'customer.groups' => [1, 2],
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['customer.id'], $item->getId() );
		$this->assertEquals( $list['customer.code'], $item->getCode() );
		$this->assertEquals( $list['customer.label'], $item->getLabel() );
		$this->assertEquals( $list['customer.status'], $item->getStatus() );
		$this->assertEquals( $list['customer.groups'], $item->getGroups() );
		$this->assertEquals( $list['customer.password'], $item->getPassword() );
		$this->assertEquals( $list['customer.dateverified'], $item->getDateVerified() );

		$address = $item->getPaymentAddress();
		$this->assertEquals( $list['customer.company'], $address->getCompany() );
		$this->assertEquals( $list['customer.vatid'], $address->getVatID() );
		$this->assertEquals( $list['customer.salutation'], $address->getSalutation() );
		$this->assertEquals( $list['customer.title'], $address->getTitle() );
		$this->assertEquals( $list['customer.firstname'], $address->getFirstname() );
		$this->assertEquals( $list['customer.lastname'], $address->getLastname() );
		$this->assertEquals( $list['customer.address1'], $address->getAddress1() );
		$this->assertEquals( $list['customer.address2'], $address->getAddress2() );
		$this->assertEquals( $list['customer.address3'], $address->getAddress3() );
		$this->assertEquals( $list['customer.postal'], $address->getPostal() );
		$this->assertEquals( $list['customer.city'], $address->getCity() );
		$this->assertEquals( $list['customer.state'], $address->getState() );
		$this->assertEquals( $list['customer.countryid'], $address->getCountryId() );
		$this->assertEquals( $list['customer.languageid'], $address->getLanguageId() );
		$this->assertEquals( $list['customer.telephone'], $address->getTelephone() );
		$this->assertEquals( $list['customer.email'], $address->getEmail() );
		$this->assertEquals( $list['customer.telefax'], $address->getTelefax() );
		$this->assertEquals( $list['customer.website'], $address->getWebsite() );
		$this->assertEquals( $list['customer.longitude'], $address->getLongitude() );
		$this->assertEquals( $list['customer.latitude'], $address->getLatitude() );
		$this->assertEquals( $list['customer.birthday'], $address->getBirthday() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['customer.id'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['customer.label'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['customer.code'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['customer.status'] );
		$this->assertEquals( $this->object->getGroups(), $arrayObject['customer.groups'] );
		$this->assertEquals( $this->object->getPassword(), $arrayObject['customer.password'] );
		$this->assertEquals( $this->object->getDateVerified(), $arrayObject['customer.dateverified'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['customer.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['customer.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['customer.editor'] );

		$address = $this->object->getPaymentAddress();
		$this->assertEquals( $address->getCompany(), $arrayObject['customer.company'] );
		$this->assertEquals( $address->getVatID(), $arrayObject['customer.vatid'] );
		$this->assertEquals( $address->getSalutation(), $arrayObject['customer.salutation'] );
		$this->assertEquals( $address->getTitle(), $arrayObject['customer.title'] );
		$this->assertEquals( $address->getFirstname(), $arrayObject['customer.firstname'] );
		$this->assertEquals( $address->getLastname(), $arrayObject['customer.lastname'] );
		$this->assertEquals( $address->getAddress1(), $arrayObject['customer.address1'] );
		$this->assertEquals( $address->getAddress2(), $arrayObject['customer.address2'] );
		$this->assertEquals( $address->getAddress3(), $arrayObject['customer.address3'] );
		$this->assertEquals( $address->getPostal(), $arrayObject['customer.postal'] );
		$this->assertEquals( $address->getCity(), $arrayObject['customer.city'] );
		$this->assertEquals( $address->getState(), $arrayObject['customer.state'] );
		$this->assertEquals( $address->getCountryId(), $arrayObject['customer.countryid'] );
		$this->assertEquals( $address->getLanguageId(), $arrayObject['customer.languageid'] );
		$this->assertEquals( $address->getTelephone(), $arrayObject['customer.telephone'] );
		$this->assertEquals( $address->getEmail(), $arrayObject['customer.email'] );
		$this->assertEquals( $address->getTelefax(), $arrayObject['customer.telefax'] );
		$this->assertEquals( $address->getWebsite(), $arrayObject['customer.website'] );
		$this->assertEquals( $address->getLongitude(), $arrayObject['customer.longitude'] );
		$this->assertEquals( $address->getLatitude(), $arrayObject['customer.latitude'] );
		$this->assertEquals( $address->getBirthday(), $arrayObject['customer.birthday'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setAvailable( false );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsAvailableOnStatus()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setStatus( 0 );
		$this->assertFalse( $this->object->isAvailable() );
		$this->object->setStatus( -1 );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsSuper()
	{
		$this->assertFalse( $this->object->isSuper() );

		$this->object->set( '.super', 1 );
		$this->assertTrue( $this->object->isSuper() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );

		$this->object->getPaymentAddress()->setState( 'HH' );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testIsModifiedUpdate()
	{
		$list = $this->object->toArray( true );
		$this->object->fromArray( $list, true );

		$this->assertFalse( $this->object->isModified() );
	}
}
