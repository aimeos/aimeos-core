<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2017
 */


namespace Aimeos\MShop\Customer\Item;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;
	private $address;


	protected function setUp()
	{
		$addressValues = array(
			'common.address.parentid' => 'referenceid',
			'common.address.position' => 1,
		);

		$this->address = new \Aimeos\MShop\Common\Item\Address\Standard( 'common.address.', $addressValues );

		$this->values = array(
			'customer.id' => 541,
			'customer.siteid' => 123,
			'customer.label' => 'unitObject',
			'customer.code' => '12345ABCDEF',
			'customer.birthday' => '2010-01-01',
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
			'customer.countryid' => 'de',
			'customer.languageid' => 'de',
			'customer.telephone' => '05554433221',
			'customer.email' => 'test@example.com',
			'customer.telefax' => '05554433222',
			'customer.website' => 'www.example.com',
			'customer.longitude' => '10.0',
			'customer.latitude' => '50.0',
			'customer.mtime'=> '2010-01-05 00:00:05',
			'customer.ctime'=> '2010-01-01 00:00:00',
			'customer.editor' => 'unitTestUser'
		);

		$addresses = array(
			-1 => new \Aimeos\MShop\Customer\Item\Address\Standard( 'customer.address.', ['customer.address.position' => 1] ),
			-2 => new \Aimeos\MShop\Customer\Item\Address\Standard( 'customer.address.', ['customer.address.position' => 0] ),
		);

		$this->object = new \Aimeos\MShop\Customer\Item\Standard( $this->address, $this->values, [], [], 'mshop', null, $addresses );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->address, $this->values );
	}

	public function testGetId()
	{
		$this->assertEquals( 541, $this->object->getId() );
	}

	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Customer\Item\Iface', $return );
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

		$this->assertInstanceOf( '\Aimeos\MShop\Customer\Item\Iface', $return );
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

		$this->assertInstanceOf( '\Aimeos\MShop\Customer\Item\Iface', $return );
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

		$this->assertInstanceOf( '\Aimeos\MShop\Customer\Item\Iface', $return );
		$this->assertEquals( 0, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testSetAndGetPassword()
	{
		$return = $this->object->setPassword( '08154712' );

		$this->assertInstanceOf( '\Aimeos\MShop\Customer\Item\Iface', $return );
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

	public function testGetBirthday()
	{
		$this->assertEquals( '2010-01-01', $this->object->getBirthday() );
	}

	public function testSetBirthday()
	{
		$return = $this->object->setBirthday( '2010-02-01' );

		$this->assertInstanceOf( '\Aimeos\MShop\Customer\Item\Iface', $return );
		$this->assertEquals( '2010-02-01', $this->object->getBirthday() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetDateVerified()
	{
		$this->assertEquals( null, $this->object->getDateVerified() );
	}

	public function testSetDateVerified()
	{
		$return = $this->object->setDateVerified( '2010-02-01' );

		$this->assertInstanceOf( '\Aimeos\MShop\Customer\Item\Iface', $return );
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
	}

	public function testGetPaymentAddress()
	{
		$address = $this->object->getPaymentAddress();
		$this->assertEquals( $address->getParentId(), 'referenceid' );
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
	}

	public function testSetPaymentAddress()
	{
		$this->address->setCompany( 'unitCompany0815' );
		$return = $this->object->setPaymentAddress( $this->address );

		$this->assertInstanceOf( '\Aimeos\MShop\Customer\Item\Iface', $return );
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
			$this->assertInstanceOf( '\Aimeos\MShop\Customer\Item\Address\Iface', $item );
		}
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'customer', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$address = new \Aimeos\MShop\Common\Item\Address\Standard( 'common.address.' );
		$item = new \Aimeos\MShop\Customer\Item\Standard( $address );

		$list = array(
			'customer.id' => 1,
			'customer.code' => '12345ABCDEF',
			'customer.label' => 'unitObject',
			'customer.birthday' => '2010-01-01',
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
			'customer.groups' => [1, 2],
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['customer.id'], $item->getId() );
		$this->assertEquals( $list['customer.code'], $item->getCode() );
		$this->assertEquals( $list['customer.label'], $item->getLabel() );
		$this->assertEquals( $list['customer.birthday'], $item->getBirthday() );
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
		$this->assertEquals( $this->object->getBirthday(), $arrayObject['customer.birthday'] );
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
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );

		$this->object->getPaymentAddress()->setState( 'HH' );
		$this->assertTrue( $this->object->isModified() );
	}
}
