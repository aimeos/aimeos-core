<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


/**
 * Test class for MShop_Customer_Item_Address_Standard.
 */
class MShop_Customer_Item_Address_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->values = array(
			'id' => 23,
			'siteid' => 12,
			'refid' => 'referenceid',
			'company' => 'unitCompany',
			'vatid' => 'DE999999999',
			'salutation' => MShop_Common_Item_Address_Base::SALUTATION_MR,
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
			'email' => 'test@example.com',
			'telefax' => '05554433222',
			'website' => 'www.example.com',
			'pos' => 1,
			'flag' => 2,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser',
		);

		$this->object = new MShop_Common_Item_Address_Standard( 'customer.address.', $this->values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}

	public function testGetId()
	{
		$this->assertEquals( 23, $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertTrue( $this->object->isModified() );
		$this->assertNull( $this->object->getId() );
	}

	public function testGetRefId()
	{
		$this->assertEquals( 'referenceid', $this->object->getRefId() );
	}

	public function testSetRefId()
	{
		$this->object->setRefId( 'unitreference' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'unitreference', $this->object->getRefId() );
	}

	public function testGetCompany()
	{
		$this->assertEquals( 'unitCompany', $this->object->getCompany() );
	}

	public function testSetCompany()
	{
		$this->object->setCompany( 'company' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'company', $this->object->getCompany() );
	}

	public function testGetVatID()
	{
		$this->assertEquals( 'DE999999999', $this->object->getVatID() );
	}

	public function testSetVatID()
	{
		$this->object->setVatID( 'vatid' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'vatid', $this->object->getVatID() );
	}

	public function testGetSalutation()
	{
		$this->assertEquals( MShop_Common_Item_Address_Base::SALUTATION_MR, $this->object->getSalutation() );
	}

	public function testSetSalutation()
	{
		$this->object->setSalutation( MShop_Common_Item_Address_Base::SALUTATION_COMPANY );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( MShop_Common_Item_Address_Base::SALUTATION_COMPANY, $this->object->getSalutation() );
	}

	public function testGetTitle()
	{
		$this->assertEquals( 'Herr', $this->object->getTitle() );
	}

	public function testSetTitle()
	{
		$this->object->setTitle( 'Dr.' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'Dr.', $this->object->getTitle() );
	}

	public function testGetFirstname()
	{
		$this->assertEquals( 'firstunit', $this->object->getFirstname() );
	}

	public function testSetFirstname()
	{
		$this->object->setFirstname( 'hans' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'hans', $this->object->getFirstname() );
	}

	public function testGetLastname()
	{
		$this->assertEquals( 'lastunit', $this->object->getLastname() );
	}

	public function testSetLastname()
	{
		$this->object->setLastname( 'im Glueck' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'im Glueck', $this->object->getLastname() );
	}

	public function testGetAddress1()
	{
		$this->assertEquals( 'unit str.', $this->object->getAddress1() );
	}

	public function testSetAddress1()
	{
		$this->object->setAddress1( 'unitallee' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'unitallee', $this->object->getAddress1() );
	}

	public function testGetAddress2()
	{
		$this->assertEquals( '166', $this->object->getAddress2() );
	}

	public function testSetAddress2()
	{
		$this->object->setAddress2( '12' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( '12', $this->object->getAddress2() );
	}

	public function testGetAddress3()
	{
		$this->assertEquals( '4.OG', $this->object->getAddress3() );
	}

	public function testSetAddress3()
	{
		$this->object->setAddress3( 'EG' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'EG', $this->object->getAddress3() );
	}

	public function testGetPostal()
	{
		$this->assertEquals( '22769', $this->object->getPostal() );
	}

	public function testSetPostal()
	{
		$this->object->setPostal( '11111' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( '11111', $this->object->getPostal() );
	}

	public function testGetCity()
	{
		$this->assertEquals( 'Hamburg', $this->object->getCity() );
	}

	public function testSetCity()
	{
		$this->object->setCity( 'unitCity' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'unitCity', $this->object->getCity() );
	}

	public function testGetState()
	{
		$this->assertEquals( 'Hamburg', $this->object->getState() );
	}

	public function testSetState()
	{
		$this->object->setState( 'unitState' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'unitState', $this->object->getState() );
	}

	public function testGetCountryId()
	{
		$this->assertEquals( 'DE', $this->object->getCountryId() );
	}

	public function testSetCountryId()
	{
		$this->object->setCountryId( 'uk' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'UK', $this->object->getCountryId() );
	}

	public function testGetLanguageId()
	{
		$this->assertEquals( 'de', $this->object->getLanguageId() );
	}

	public function testSetLanguageId()
	{
		$this->object->setLanguageId( 'en' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'en', $this->object->getLanguageId() );
	}

	public function testGetTelephone()
	{
		$this->assertEquals( '05554433221', $this->object->getTelephone() );
	}

	public function testSetTelephone()
	{
		$this->object->setTelephone( '55512345' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( '55512345', $this->object->getTelephone() );
	}

	public function testGetEmail()
	{
		$this->assertEquals( 'test@example.com', $this->object->getEmail() );
	}

	public function testSetEmail()
	{
		$this->object->setEmail( 'unit@test.de' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'unit@test.de', $this->object->getEmail() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->setEmail( 'unittest.de' );
	}

	public function testGetTelefax()
	{
		$this->assertEquals( '05554433222', $this->object->getTelefax() );
	}

	public function testSetTelefax()
	{
		$this->object->setTelefax( '55512345' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( '55512345', $this->object->getTelefax() );
	}

	public function testGetWebsite()
	{
		$this->assertEquals( 'www.example.com', $this->object->getWebsite() );
	}

	public function testSetWebsite()
	{
		$this->object->setWebsite( 'www.test.de' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'www.test.de', $this->object->getWebsite() );

		$this->object->setWebsite( 'http://xn--ses-5ka8l.de' );
		$this->object->setWebsite( 'http://www.test.de:443' );
		$this->object->setWebsite( 'https://www.test.de:8080/abc?123' );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->setWebsite( '_test:de' );
	}

	public function testSetWebsiteHostException()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->setWebsite( 'localhost' );
	}

	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->object->getPosition() );
	}

	public function testSetPosition()
	{
		$this->object->setPosition( 555 );
		$this->assertEquals( 555, $this->object->getPosition() );
	}

	public function testGetFlag()
	{
		$this->assertEquals( 2, $this->object->getFlag() );
	}

	public function testSetFlag()
	{
		$this->object->setFlag( 5 );
		$this->assertEquals( 5, $this->object->getFlag() );
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}

	public function testCopyFrom()
	{
		$object = new MShop_Common_Item_Address_Standard( 'customer.address.' );
		$address = new MShop_Order_Item_Base_Address_Standard( $this->values );
		$object->copyFrom( $address );

		$this->assertNull( $object->getId() );
		$this->assertEquals( $this->values['salutation'], $object->getSalutation() );
		$this->assertEquals( $this->values['company'], $object->getCompany() );
		$this->assertEquals( $this->values['vatid'], $object->getVatID() );
		$this->assertEquals( $this->values['title'], $object->getTitle() );
		$this->assertEquals( $this->values['firstname'], $object->getFirstname() );
		$this->assertEquals( $this->values['lastname'], $object->getLastname() );
		$this->assertEquals( $this->values['address1'], $object->getAddress1() );
		$this->assertEquals( $this->values['address2'], $object->getAddress2() );
		$this->assertEquals( $this->values['address3'], $object->getAddress3() );
		$this->assertEquals( $this->values['postal'], $object->getPostal() );
		$this->assertEquals( $this->values['city'], $object->getCity() );
		$this->assertEquals( $this->values['state'], $object->getState() );
		$this->assertEquals( $this->values['countryid'], $object->getCountryId() );
		$this->assertEquals( $this->values['langid'], $object->getLanguageId() );
		$this->assertEquals( $this->values['telephone'], $object->getTelephone() );
		$this->assertEquals( $this->values['telefax'], $object->getTelefax() );
		$this->assertEquals( $this->values['email'], $object->getEmail() );
		$this->assertEquals( $this->values['website'], $object->getWebsite() );
		$this->assertEquals( $this->values['flag'], $object->getFlag() );
	}

	public function testFromArray()
	{
		$list = array(
			'customer.address.id' => 1,
			'customer.address.refid' => 2,
			'customer.address.salutation' => 'mr',
			'customer.address.company' => 'mw',
			'customer.address.vatid' => 'vatnumber',
			'customer.address.title' => 'dr',
			'customer.address.firstname' => 'first',
			'customer.address.lastname' => 'last',
			'customer.address.address1' => 'street',
			'customer.address.address2' => 'no',
			'customer.address.address3' => 'flat',
			'customer.address.postal' => '12345',
			'customer.address.city' => 'city',
			'customer.address.state' => 'state',
			'customer.address.countryid' => 'DE',
			'customer.address.languageid' => 'de',
			'customer.address.telephone' => '01234',
			'customer.address.telefax' => '02345',
			'customer.address.email' => 'a@b',
			'customer.address.website' => 'example.com',
			'customer.address.flag' => 3,
			'customer.address.position' => 4,
		);

		$object = new MShop_Common_Item_Address_Standard( 'customer.address.' );
		$unknown = $object->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['customer.address.id'], $object->getId() );
		$this->assertEquals( $list['customer.address.refid'], $object->getRefId() );
		$this->assertEquals( $list['customer.address.salutation'], $object->getSalutation() );
		$this->assertEquals( $list['customer.address.company'], $object->getCompany() );
		$this->assertEquals( $list['customer.address.vatid'], $object->getVatID() );
		$this->assertEquals( $list['customer.address.title'], $object->getTitle() );
		$this->assertEquals( $list['customer.address.firstname'], $object->getFirstname() );
		$this->assertEquals( $list['customer.address.lastname'], $object->getLastname() );
		$this->assertEquals( $list['customer.address.address1'], $object->getAddress1() );
		$this->assertEquals( $list['customer.address.address2'], $object->getAddress2() );
		$this->assertEquals( $list['customer.address.address3'], $object->getAddress3() );
		$this->assertEquals( $list['customer.address.postal'], $object->getPostal() );
		$this->assertEquals( $list['customer.address.city'], $object->getCity() );
		$this->assertEquals( $list['customer.address.state'], $object->getState() );
		$this->assertEquals( $list['customer.address.countryid'], $object->getCountryId() );
		$this->assertEquals( $list['customer.address.languageid'], $object->getLanguageId() );
		$this->assertEquals( $list['customer.address.telephone'], $object->getTelephone() );
		$this->assertEquals( $list['customer.address.telefax'], $object->getTelefax() );
		$this->assertEquals( $list['customer.address.email'], $object->getEmail() );
		$this->assertEquals( $list['customer.address.website'], $object->getWebsite() );
		$this->assertEquals( $list['customer.address.flag'], $object->getFlag() );
		$this->assertEquals( $list['customer.address.position'], $object->getPosition() );
	}

	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['customer.address.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['customer.address.siteid'] );
		$this->assertEquals( $this->object->getRefID(), $arrayObject['customer.address.refid'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['customer.address.position'] );
		$this->assertEquals( $this->object->getCompany(), $arrayObject['customer.address.company'] );
		$this->assertEquals( $this->object->getVatID(), $arrayObject['customer.address.vatid'] );
		$this->assertEquals( $this->object->getSalutation(), $arrayObject['customer.address.salutation'] );
		$this->assertEquals( $this->object->getTitle(), $arrayObject['customer.address.title'] );
		$this->assertEquals( $this->object->getFirstname(), $arrayObject['customer.address.firstname'] );
		$this->assertEquals( $this->object->getLastname(), $arrayObject['customer.address.lastname'] );
		$this->assertEquals( $this->object->getAddress1(), $arrayObject['customer.address.address1'] );
		$this->assertEquals( $this->object->getAddress2(), $arrayObject['customer.address.address2'] );
		$this->assertEquals( $this->object->getAddress3(), $arrayObject['customer.address.address3'] );
		$this->assertEquals( $this->object->getPostal(), $arrayObject['customer.address.postal'] );
		$this->assertEquals( $this->object->getCity(), $arrayObject['customer.address.city'] );
		$this->assertEquals( $this->object->getState(), $arrayObject['customer.address.state'] );
		$this->assertEquals( $this->object->getCountryId(), $arrayObject['customer.address.countryid'] );
		$this->assertEquals( $this->object->getTelephone(), $arrayObject['customer.address.telephone'] );
		$this->assertEquals( $this->object->getEmail(), $arrayObject['customer.address.email'] );
		$this->assertEquals( $this->object->getTelefax(), $arrayObject['customer.address.telefax'] );
		$this->assertEquals( $this->object->getWebsite(), $arrayObject['customer.address.website'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['customer.address.languageid'] );
		$this->assertEquals( $this->object->getFlag(), $arrayObject['customer.address.flag'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['customer.address.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['customer.address.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['customer.address.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
