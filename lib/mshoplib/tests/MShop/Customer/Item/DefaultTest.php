<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Customer_Item_Default.
 */
class MShop_Customer_Item_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $values;
	private $address;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$addressValues = array(
			'refid' => 'referenceid',
			'pos' => 1,
		);

		$this->address = new MShop_Common_Item_Address_Default( 'common.address.', $addressValues );

		$this->values = array(
			'id' => 541,
			'siteid' => 123,
			'label' => 'unitObject',
			'code' => '12345ABCDEF',
			'birthday' => '2010-01-01',
			'status' => 1,
			'password' => '',
			'vdate' => null,
			'company' => 'unitCompany',
			'vatid' => 'DE999999999',
			'salutation' => MShop_Common_Item_Address_Abstract::SALUTATION_MR,
			'title' => 'Dr.',
			'firstname' => 'firstunit',
			'lastname' => 'lastunit',
			'address1' => 'unit str.',
			'address2' => ' 166',
			'address3' => '4.OG',
			'postal' => '22769',
			'city' => 'Hamburg',
			'state' => 'Hamburg',
			'countryid' => 'de',
			'langid' => 'de',
			'telephone' => '05554433221',
			'email' => 'test@example.com',
			'telefax' => '05554433222',
			'website' => 'www.example.com',
			'mtime'=> '2010-01-05 00:00:05',
			'ctime'=> '2010-01-01 00:00:00',
			'editor' => 'unitTestUser'
		);

		$this->object = new MShop_Customer_Item_Default( $this->address, $this->values, array(), array(), 'mshop', null );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
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
		$this->object->setId( null );
		$this->assertTrue( $this->object->isModified() );
		$this->assertNull( $this->object->getId() );
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
		$this->object->setLabel( 'newName' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'newName', $this->object->getLabel() );
	}

	public function testGetCode()
	{
		$this->assertEquals( '12345ABCDEF', $this->object->getCode() );
	}

	public function testSetCode()
	{
		$this->object->setCode( 'neuerUser@unittest.com' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'neuerUser@unittest.com', $this->object->getCode() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->object->setStatus( 0 );
		$this->assertEquals( 0, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testSetAndGetPassword()
	{
		$this->object->setPassword( '08154712' );
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
		$this->object->setBirthday( '2010-02-01' );
		$this->assertEquals( '2010-02-01', $this->object->getBirthday() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetDateVerified()
	{
		$this->assertEquals( null, $this->object->getDateVerified() );
	}

	public function testSetDateVerified()
	{
		$this->object->setDateVerified( '2010-02-01' );
		$this->assertEquals( '2010-02-01', $this->object->getDateVerified() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetGroups()
	{
		$listValues = array( 'domain' => 'customer/group', 'refid' => 123 );
		$listItems = array( 'customer/group' => array( new MShop_Common_Item_List_Default( '', $listValues ) ) );
		$object = new MShop_Customer_Item_Default( $this->address, array(), $listItems );

		$this->assertEquals( array( 123 ), $object->getGroups() );
	}

	public function testGetPaymentAddress()
	{
		$address = $this->object->getPaymentAddress();
		$this->assertEquals( $address->getRefId(), 'referenceid' );
		$this->assertEquals( $address->getCompany(), 'unitCompany' );
		$this->assertEquals( $address->getVatID(), 'DE999999999' );
		$this->assertEquals( $address->getSalutation(), MShop_Common_Item_Address_Abstract::SALUTATION_MR );
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
	}

	public function testSetPaymentAddress()
	{
		$this->address->setCompany( 'unitCompany0815' );
		$this->object->setPaymentAddress( $this->address );
		$this->assertEquals( $this->address, $this->object->getPaymentAddress() );
	}


	public function testFromArray()
	{
		$address = new MShop_Common_Item_Address_Default( 'common.address.' );
		$item = new MShop_Customer_Item_Default( $address );

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
			'customer.salutation' => MShop_Common_Item_Address_Abstract::SALUTATION_MR,
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
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['customer.id'], $item->getId() );
		$this->assertEquals( $list['customer.code'], $item->getCode() );
		$this->assertEquals( $list['customer.label'], $item->getLabel() );
		$this->assertEquals( $list['customer.birthday'], $item->getBirthday() );
		$this->assertEquals( $list['customer.status'], $item->getStatus() );
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
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['customer.id'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['customer.label'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['customer.code'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['customer.status'] );
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
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
