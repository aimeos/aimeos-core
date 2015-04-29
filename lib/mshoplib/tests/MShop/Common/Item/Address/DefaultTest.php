<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Common_Item_Address_Default.
 */
class MShop_Common_Item_Address_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_values;



	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_values = array(
			'id' => 23,
			'siteid' => 12,
			'refid' => 'referenceid',
			'company' => 'unitCompany',
			'vatid' => 'DE999999999',
			'salutation' => MShop_Common_Item_Address_Abstract::SALUTATION_MR,
			'title' => 'Herr',
			'firstname' => 'firstunit',
			'lastname' => 'lastunit',
			'address1' => 'unit str.',
			'address2' => ' 166',
			'address3' => '4.OG',
			'postal' => '22769',
			'city' => 'Hamburg',
			'state' => 'Hamburg',
			'countryid' => 'DE',
			'langid' => 'de',
			'telephone' => '05554433221',
			'email' => 'unit.test@metaways.de',
			'telefax' => '05554433222',
			'website' => 'www.metaways.de',
			'pos' => 1,
			'flag' => 2,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser',
		);

		$this->_object = new MShop_Common_Item_Address_Default( 'common.address.', $this->_values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}

	public function testGetId()
	{
		$this->assertEquals( 23, $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertTrue($this->_object->isModified());
		$this->assertNull( $this->_object->getId());
	}

	public function testGetRefId()
	{
		$this->assertEquals( 'referenceid', $this->_object->getRefId() );
	}

	public function testSetRefId()
	{
		$this->_object->setRefId( 'unitreference' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'unitreference', $this->_object->getRefId() );
	}

	public function testGetCompany()
	{
		$this->assertEquals( 'unitCompany', $this->_object->getCompany() );
	}

	public function testSetCompany()
	{
		$this->_object->setCompany( 'company' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'company', $this->_object->getCompany() );
	}

	public function testGetVatID()
	{
		$this->assertEquals( 'DE999999999', $this->_object->getVatID() );
	}

	public function testSetVatID()
	{
		$this->_object->setVatID( 'vatid' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'vatid', $this->_object->getVatID() );
	}

	public function testGetSalutation()
	{
		$this->assertEquals( MShop_Common_Item_Address_Abstract::SALUTATION_MR, $this->_object->getSalutation() );
	}

	public function testSetSalutation()
	{
		$this->_object->setSalutation( MShop_Common_Item_Address_Abstract::SALUTATION_COMPANY );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( MShop_Common_Item_Address_Abstract::SALUTATION_COMPANY, $this->_object->getSalutation() );
	}

	public function testGetTitle()
	{
		$this->assertEquals( 'Herr', $this->_object->getTitle() );
	}

	public function testSetTitle()
	{
		$this->_object->setTitle( 'Dr.' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'Dr.', $this->_object->getTitle() );
	}

	public function testGetFirstname()
	{
		$this->assertEquals( 'firstunit', $this->_object->getFirstname() );
	}

	public function testSetFirstname()
	{
		$this->_object->setFirstname( 'hans' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'hans', $this->_object->getFirstname() );
	}

	public function testGetLastname()
	{
		$this->assertEquals( 'lastunit', $this->_object->getLastname() );
	}

	public function testSetLastname()
	{
		$this->_object->setLastname( 'im Glueck' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'im Glueck', $this->_object->getLastname() );
	}

	public function testGetAddress1()
	{
		$this->assertEquals( 'unit str.', $this->_object->getAddress1() );
	}

	public function testSetAddress1()
	{
		$this->_object->setAddress1( 'unitallee' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'unitallee', $this->_object->getAddress1() );
	}

	public function testGetAddress2()
	{
		$this->assertEquals( '166', $this->_object->getAddress2() );
	}

	public function testSetAddress2()
	{
		$this->_object->setAddress2( '12' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( '12', $this->_object->getAddress2() );
	}

	public function testGetAddress3()
	{
		$this->assertEquals( '4.OG', $this->_object->getAddress3() );
	}

	public function testSetAddress3()
	{
		$this->_object->setAddress3( 'EG' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'EG', $this->_object->getAddress3() );
	}

	public function testGetPostal()
	{
		$this->assertEquals( '22769', $this->_object->getPostal() );
	}

	public function testSetPostal()
	{
		$this->_object->setPostal( '11111' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( '11111', $this->_object->getPostal() );
	}

	public function testGetCity()
	{
		$this->assertEquals( 'Hamburg', $this->_object->getCity() );
	}

	public function testSetCity()
	{
		$this->_object->setCity( 'unitCity' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'unitCity', $this->_object->getCity() );
	}

	public function testGetState()
	{
		$this->assertEquals( 'Hamburg', $this->_object->getState() );
	}

	public function testSetState()
	{
		$this->_object->setState( 'unitState' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'unitState', $this->_object->getState() );
	}

	public function testGetCountryId()
	{
		$this->assertEquals( 'DE', $this->_object->getCountryId());
	}

	public function testSetCountryId()
	{
		$this->_object->setCountryId('uk');
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'UK', $this->_object->getCountryId());
	}

	public function testGetLanguageId()
	{
		$this->assertEquals( 'de', $this->_object->getLanguageId());
	}

	public function testSetLanguageId()
	{
		$this->_object->setLanguageId('en');
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'en', $this->_object->getLanguageId());
	}

	public function testGetTelephone()
	{
		$this->assertEquals( '05554433221', $this->_object->getTelephone() );
	}

	public function testSetTelephone()
	{
		$this->_object->setTelephone( '55512345' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( '55512345', $this->_object->getTelephone() );
	}

	public function testGetEmail()
	{
		$this->assertEquals( 'unit.test@metaways.de', $this->_object->getEmail());
	}

	public function testSetEmail()
	{
		$this->_object->setEmail('unit@test.de');
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'unit@test.de', $this->_object->getEmail());

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setEmail('unittest.de');
	}

	public function testGetTelefax()
	{
		$this->assertEquals( '05554433222', $this->_object->getTelefax());
	}

	public function testSetTelefax()
	{
		$this->_object->setTelefax('55512345');
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( '55512345', $this->_object->getTelefax());
	}

	public function testGetWebsite()
	{
		$this->assertEquals( 'www.metaways.de', $this->_object->getWebsite());
	}

	public function testSetWebsite()
	{
		$this->_object->setWebsite('www.test.de');
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'www.test.de', $this->_object->getWebsite());

		$this->_object->setWebsite('http://xn--ses-5ka8l.de');
		$this->_object->setWebsite('http://www.test.de:443');
		$this->_object->setWebsite('https://www.test.de:8080/abc?123');

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setWebsite('_test:de');
	}

	public function testSetWebsiteHostException()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setWebsite('localhost');
	}

	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->_object->getPosition());
	}

	public function testSetPosition()
	{
		$this->_object->setPosition(555);
		$this->assertEquals( 555, $this->_object->getPosition());
	}

	public function testGetFlag()
	{
		$this->assertEquals( 2, $this->_object->getFlag());
	}

	public function testSetFlag()
	{
		$this->_object->setFlag(5);
		$this->assertEquals( 5, $this->_object->getFlag());
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->_object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->_object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}

	public function testCopyFrom()
	{
		$object = new MShop_Common_Item_Address_Default( 'common.address.' );
		$address = new MShop_Order_Item_Base_Address_Default( $this->_values );
		$object->copyFrom( $address );

		$this->assertNull( $object->getId() );
		$this->assertEquals( $this->_values['salutation'], $object->getSalutation() );
		$this->assertEquals( $this->_values['company'], $object->getCompany() );
		$this->assertEquals( $this->_values['vatid'], $object->getVatID() );
		$this->assertEquals( $this->_values['title'], $object->getTitle() );
		$this->assertEquals( $this->_values['firstname'], $object->getFirstname() );
		$this->assertEquals( $this->_values['lastname'], $object->getLastname() );
		$this->assertEquals( $this->_values['address1'], $object->getAddress1() );
		$this->assertEquals( $this->_values['address2'], $object->getAddress2() );
		$this->assertEquals( $this->_values['address3'], $object->getAddress3() );
		$this->assertEquals( $this->_values['postal'], $object->getPostal() );
		$this->assertEquals( $this->_values['city'], $object->getCity() );
		$this->assertEquals( $this->_values['state'], $object->getState() );
		$this->assertEquals( $this->_values['countryid'], $object->getCountryId() );
		$this->assertEquals( $this->_values['langid'], $object->getLanguageId() );
		$this->assertEquals( $this->_values['telephone'], $object->getTelephone() );
		$this->assertEquals( $this->_values['telefax'], $object->getTelefax() );
		$this->assertEquals( $this->_values['email'], $object->getEmail() );
		$this->assertEquals( $this->_values['website'], $object->getWebsite() );
		$this->assertEquals( $this->_values['flag'], $object->getFlag() );
	}

	public function testFromArray()
	{
		$list = array(
			'common.address.id' => 1,
			'common.address.refid' => 2,
			'common.address.salutation' => 'mr',
			'common.address.company' => 'mw',
			'common.address.vatid' => 'vatnumber',
			'common.address.title' => 'dr',
			'common.address.firstname' => 'first',
			'common.address.lastname' => 'last',
			'common.address.address1' => 'street',
			'common.address.address2' => 'no',
			'common.address.address3' => 'flat',
			'common.address.postal' => '12345',
			'common.address.city' => 'city',
			'common.address.state' => 'state',
			'common.address.countryid' => 'DE',
			'common.address.languageid' => 'de',
			'common.address.telephone' => '01234',
			'common.address.telefax' => '02345',
			'common.address.email' => 'a@b',
			'common.address.website' => 'example.com',
			'common.address.flag' => 3,
			'common.address.position' => 4,
		);

		$object = new MShop_Common_Item_Address_Default( 'common.address.' );
		$unknown = $object->fromArray( $list );

		$this->assertEquals(array(), $unknown);

		$this->assertEquals( $list['common.address.id'], $object->getId() );
		$this->assertEquals( $list['common.address.refid'], $object->getRefId() );
		$this->assertEquals( $list['common.address.salutation'], $object->getSalutation() );
		$this->assertEquals( $list['common.address.company'], $object->getCompany() );
		$this->assertEquals( $list['common.address.vatid'], $object->getVatID() );
		$this->assertEquals( $list['common.address.title'], $object->getTitle() );
		$this->assertEquals( $list['common.address.firstname'], $object->getFirstname() );
		$this->assertEquals( $list['common.address.lastname'], $object->getLastname() );
		$this->assertEquals( $list['common.address.address1'], $object->getAddress1() );
		$this->assertEquals( $list['common.address.address2'], $object->getAddress2() );
		$this->assertEquals( $list['common.address.address3'], $object->getAddress3() );
		$this->assertEquals( $list['common.address.postal'], $object->getPostal() );
		$this->assertEquals( $list['common.address.city'], $object->getCity() );
		$this->assertEquals( $list['common.address.state'], $object->getState() );
		$this->assertEquals( $list['common.address.countryid'], $object->getCountryId() );
		$this->assertEquals( $list['common.address.languageid'], $object->getLanguageId() );
		$this->assertEquals( $list['common.address.telephone'], $object->getTelephone() );
		$this->assertEquals( $list['common.address.telefax'], $object->getTelefax() );
		$this->assertEquals( $list['common.address.email'], $object->getEmail() );
		$this->assertEquals( $list['common.address.website'], $object->getWebsite() );
		$this->assertEquals( $list['common.address.flag'], $object->getFlag() );
		$this->assertEquals( $list['common.address.position'], $object->getPosition() );
	}

	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['common.address.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['common.address.siteid'] );
		$this->assertEquals( $this->_object->getRefID(), $arrayObject['common.address.refid'] );
		$this->assertEquals( $this->_object->getPosition(), $arrayObject['common.address.position'] );
		$this->assertEquals( $this->_object->getCompany(), $arrayObject['common.address.company'] );
		$this->assertEquals( $this->_object->getVatID(), $arrayObject['common.address.vatid'] );
		$this->assertEquals( $this->_object->getSalutation(), $arrayObject['common.address.salutation'] );
		$this->assertEquals( $this->_object->getTitle(), $arrayObject['common.address.title'] );
		$this->assertEquals( $this->_object->getFirstname(), $arrayObject['common.address.firstname'] );
		$this->assertEquals( $this->_object->getLastname(), $arrayObject['common.address.lastname'] );
		$this->assertEquals( $this->_object->getAddress1(), $arrayObject['common.address.address1'] );
		$this->assertEquals( $this->_object->getAddress2(), $arrayObject['common.address.address2'] );
		$this->assertEquals( $this->_object->getAddress3(), $arrayObject['common.address.address3'] );
		$this->assertEquals( $this->_object->getPostal(), $arrayObject['common.address.postal'] );
		$this->assertEquals( $this->_object->getCity(), $arrayObject['common.address.city'] );
		$this->assertEquals( $this->_object->getState(), $arrayObject['common.address.state'] );
		$this->assertEquals( $this->_object->getCountryId(), $arrayObject['common.address.countryid'] );
		$this->assertEquals( $this->_object->getTelephone(), $arrayObject['common.address.telephone'] );
		$this->assertEquals( $this->_object->getEmail(), $arrayObject['common.address.email'] );
		$this->assertEquals( $this->_object->getTelefax(), $arrayObject['common.address.telefax'] );
		$this->assertEquals( $this->_object->getWebsite(), $arrayObject['common.address.website'] );
		$this->assertEquals( $this->_object->getLanguageId(), $arrayObject['common.address.languageid'] );
		$this->assertEquals( $this->_object->getFlag(), $arrayObject['common.address.flag'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['common.address.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['common.address.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['common.address.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}
}
