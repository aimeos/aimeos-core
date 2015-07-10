<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Order_Item_Base_Address_Default.
 */
class MShop_Order_Item_Base_Address_DefaultTest extends MW_Unittest_Testcase
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
			'siteid' => 123,
			'baseid' => 99,
			'addrid' => 11,
			'type' => MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY,
			'company' => 'unitCompany',
			'vatid' => 'DE999999999',
			'salutation' => MShop_Order_Item_Base_Address_Abstract::SALUTATION_MR,
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
			'telephone' => '05554433221',
			'email' => 'unit.test@metaways.de',
			'telefax' => '05554433222',
			'website' => 'www.metaways.de',
			'langid' => 'de',
			'flag' => 2,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Order_Item_Base_Address_Default( $this->_values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset($this->_object);
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

	public function testGetSiteId()
	{
		$this->assertEquals( 123, $this->_object->getSiteId() );
	}

	public function testGetBaseId()
	{
		$this->assertEquals( 99, $this->_object->getBaseId() );
	}

	public function testSetBaseId()
	{
		$this->_object->setBaseId(66);
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 66, $this->_object->getBaseId());
	}

	public function testSetBaseIdReset()
	{
		$this->_object->setBaseId( null );
		$this->assertEquals( null, $this->_object->getBaseId() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetAddressId()
	{
		$this->assertEquals( 11, $this->_object->getAddressId() );
	}

	public function testSetAddressId()
	{
		$this->_object->setAddressId( 22 );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 22, $this->_object->getAddressId() );
	}

	public function testSetAddressIdNull()
	{
		$this->_object->setAddressId( null );
		$this->assertEquals( '', $this->_object->getAddressId() );
	}

	public function testGetType()
	{
		$this->assertEquals( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY, $this->_object->getType() );
	}

	public function testSetType()
	{
		$this->_object->setType( MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT, $this->_object->getType() );
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
		$this->assertEquals( MShop_Order_item_Base_Address_Abstract::SALUTATION_MR, $this->_object->getSalutation() );
	}

	public function testSetSalutation()
	{
		$this->_object->setSalutation( MShop_Order_item_Base_Address_Abstract::SALUTATION_COMPANY );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( MShop_Order_item_Base_Address_Abstract::SALUTATION_COMPANY, $this->_object->getSalutation() );
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
		$this->_object->setLastname( 'im Glück' );
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 'im Glück', $this->_object->getLastname() );
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
		$this->_object->setEmail( 'a@.' );
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

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setWebsite( 'abcde:abc' );
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

	public function testGetFlag()
	{
		$this->assertEquals( 2, $this->_object->getFlag());
	}

	public function testSetFlag()
	{
		$this->_object->setFlag(4);
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals( 4, $this->_object->getFlag());
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
		$address = new MShop_Common_Item_Address_Default( 'common.address.', $this->_values );

		$addressCopy = new MShop_Order_Item_Base_Address_Default();
		$addressCopy->copyFrom( $address );

		$this->assertEquals( 23, $addressCopy->getAddressId() );
		$this->assertEquals( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY, $addressCopy->getType() );
		$this->assertEquals( 'unitCompany', $addressCopy->getCompany() );
		$this->assertEquals( 'DE999999999', $addressCopy->getVatID() );
		$this->assertEquals( MShop_Order_item_Base_Address_Abstract::SALUTATION_MR, $addressCopy->getSalutation() );
		$this->assertEquals( 'Herr', $addressCopy->getTitle() );
		$this->assertEquals( 'firstunit', $addressCopy->getFirstname() );
		$this->assertEquals( 'lastunit', $addressCopy->getLastname() );
		$this->assertEquals( 'unit str.', $addressCopy->getAddress1() );
		$this->assertEquals( '166', $addressCopy->getAddress2() );
		$this->assertEquals( '4.OG', $addressCopy->getAddress3() );
		$this->assertEquals( '22769', $addressCopy->getPostal() );
		$this->assertEquals( 'Hamburg', $addressCopy->getCity() );
		$this->assertEquals( 'Hamburg', $addressCopy->getState() );
		$this->assertEquals( 'DE', $addressCopy->getCountryId() );
		$this->assertEquals( '05554433221', $addressCopy->getTelephone() );
		$this->assertEquals( 'unit.test@metaways.de', $addressCopy->getEmail() );
		$this->assertEquals( '05554433222', $addressCopy->getTelefax() );
		$this->assertEquals( 'www.metaways.de', $addressCopy->getWebsite() );
		$this->assertEquals( 'de', $addressCopy->getLanguageId() );
		$this->assertEquals( 2, $addressCopy->getFlag() );

		$this->assertTrue( $addressCopy->isModified() );
	}

	public function testFromArray()
	{
		$list = array(
			'order.base.address.id' => 1,
			'order.base.address.baseid' => 2,
			'order.base.address.addressid' => 3,
			'order.base.address.type' => 'payment',
		);

		$object = new MShop_Order_Item_Base_Address_Default();
		$object->fromArray( $list );

		$this->assertEquals( $list['order.base.address.id'], $object->getId() );
		$this->assertEquals( $list['order.base.address.baseid'], $object->getBaseId() );
		$this->assertEquals( $list['order.base.address.addressid'], $object->getAddressId() );
		$this->assertEquals( $list['order.base.address.type'], $object->getType() );
	}

	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['order.base.address.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['order.base.address.siteid'] );
		$this->assertEquals( $this->_object->getAddressId(), $arrayObject['order.base.address.addressid'] );
		$this->assertEquals( $this->_object->getType(), $arrayObject['order.base.address.type'] );
		$this->assertEquals( $this->_object->getCompany(), $arrayObject['order.base.address.company'] );
		$this->assertEquals( $this->_object->getVatID(), $arrayObject['order.base.address.vatid'] );
		$this->assertEquals( $this->_object->getSalutation(), $arrayObject['order.base.address.salutation'] );
		$this->assertEquals( $this->_object->getTitle(), $arrayObject['order.base.address.title'] );
		$this->assertEquals( $this->_object->getFirstname(), $arrayObject['order.base.address.firstname'] );
		$this->assertEquals( $this->_object->getLastname(), $arrayObject['order.base.address.lastname'] );
		$this->assertEquals( $this->_object->getAddress1(), $arrayObject['order.base.address.address1'] );
		$this->assertEquals( $this->_object->getAddress2(), $arrayObject['order.base.address.address2'] );
		$this->assertEquals( $this->_object->getAddress3(), $arrayObject['order.base.address.address3'] );
		$this->assertEquals( $this->_object->getPostal(), $arrayObject['order.base.address.postal'] );
		$this->assertEquals( $this->_object->getCity(), $arrayObject['order.base.address.city'] );
		$this->assertEquals( $this->_object->getState(), $arrayObject['order.base.address.state'] );
		$this->assertEquals( $this->_object->getCountryId(), $arrayObject['order.base.address.countryid'] );
		$this->assertEquals( $this->_object->getTelephone(), $arrayObject['order.base.address.telephone'] );
		$this->assertEquals( $this->_object->getEmail(), $arrayObject['order.base.address.email'] );
		$this->assertEquals( $this->_object->getTelefax(), $arrayObject['order.base.address.telefax'] );
		$this->assertEquals( $this->_object->getWebsite(), $arrayObject['order.base.address.website'] );
		$this->assertEquals( $this->_object->getLanguageId(), $arrayObject['order.base.address.languageid'] );
		$this->assertEquals( $this->_object->getFlag(), $arrayObject['order.base.address.flag'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['order.base.address.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['order.base.address.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['order.base.address.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}
}
