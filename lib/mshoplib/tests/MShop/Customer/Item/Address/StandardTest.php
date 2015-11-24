<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Customer\Item\Address;


/**
 * Test class for \Aimeos\MShop\Customer\Item\Address\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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
			'customer.address.id' => 23,
			'customer.address.siteid' => 12,
			'customer.address.parentid' => 'referenceid',
			'customer.address.company' => 'unitCompany',
			'customer.address.vatid' => 'DE999999999',
			'customer.address.salutation' => \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR,
			'customer.address.title' => 'Herr',
			'customer.address.firstname' => 'firstunit',
			'customer.address.lastname' => 'lastunit',
			'customer.address.address1' => 'unit str.',
			'customer.address.address2' => ' 166',
			'customer.address.address3' => '4.OG',
			'customer.address.postal' => '22769',
			'customer.address.city' => 'Hamburg',
			'customer.address.state' => 'Hamburg',
			'customer.address.countryid' => 'DE',
			'customer.address.languageid' => 'de',
			'customer.address.telephone' => '05554433221',
			'customer.address.email' => 'test@example.com',
			'customer.address.telefax' => '05554433222',
			'customer.address.website' => 'www.example.com',
			'customer.address.position' => 1,
			'customer.address.flag' => 2,
			'customer.address.mtime' => '2011-01-01 00:00:02',
			'customer.address.ctime' => '2011-01-01 00:00:01',
			'customer.address.editor' => 'unitTestUser',
		);

		$this->object = new \Aimeos\MShop\Common\Item\Address\Standard( 'customer.address.', $this->values );
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

	public function testGetParentId()
	{
		$this->assertEquals( 'referenceid', $this->object->getParentId() );
	}

	public function testSetParentId()
	{
		$this->object->setParentId( 'unitreference' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'unitreference', $this->object->getParentId() );
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
		$this->assertEquals( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR, $this->object->getSalutation() );
	}

	public function testSetSalutation()
	{
		$this->object->setSalutation( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_COMPANY );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_COMPANY, $this->object->getSalutation() );
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

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
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

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setWebsite( '_test:de' );
	}

	public function testSetWebsiteHostException()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
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


	public function testGetResourceType()
	{
		$this->assertEquals( 'customer/address', $this->object->getResourceType() );
	}


	public function testCopyFrom()
	{
		$address = new \Aimeos\MShop\Order\Item\Base\Address\Standard();
		$this->object->copyFrom( $address );
	}


	public function testFromArray()
	{
		$list = array(
			'customer.address.id' => 1,
			'customer.address.parentid' => 2,
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

		$object = new \Aimeos\MShop\Common\Item\Address\Standard( 'customer.address.' );
		$unknown = $object->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['customer.address.id'], $object->getId() );
		$this->assertEquals( $list['customer.address.parentid'], $object->getParentId() );
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
		$this->assertEquals( $this->object->getParentId(), $arrayObject['customer.address.parentid'] );
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
