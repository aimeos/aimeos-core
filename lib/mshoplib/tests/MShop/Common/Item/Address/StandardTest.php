<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Common\Item\Address;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;



	protected function setUp()
	{
		$this->values = array(
			'common.address.id' => 23,
			'common.address.siteid' => 12,
			'common.address.parentid' => 'referenceid',
			'common.address.company' => 'unitCompany',
			'common.address.vatid' => 'DE999999999',
			'common.address.salutation' => \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR,
			'common.address.title' => 'Herr',
			'common.address.firstname' => 'firstunit',
			'common.address.lastname' => 'lastunit',
			'common.address.address1' => 'unit str.',
			'common.address.address2' => ' 166',
			'common.address.address3' => '4.OG',
			'common.address.postal' => '22769',
			'common.address.city' => 'Hamburg',
			'common.address.state' => 'Hamburg',
			'common.address.countryid' => 'DE',
			'common.address.languageid' => 'de',
			'common.address.telephone' => '05554433221',
			'common.address.email' => 'test@example.com',
			'common.address.telefax' => '05554433222',
			'common.address.website' => 'www.example.com',
			'common.address.longitude' => '10.0',
			'common.address.latitude' => '50.0',
			'common.address.position' => 1,
			'common.address.flag' => 2,
			'common.address.mtime' => '2011-01-01 00:00:02',
			'common.address.ctime' => '2011-01-01 00:00:01',
			'common.address.editor' => 'unitTestUser',
		);

		$this->object = new \Aimeos\MShop\Common\Item\Address\Standard( 'common.address.', $this->values );
	}

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
		$return = $this->object->setId( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertTrue( $this->object->isModified() );
		$this->assertNull( $this->object->getId() );
	}

	public function testGetParentfId()
	{
		$this->assertEquals( 'referenceid', $this->object->getParentId() );
	}

	public function testSetParentId()
	{
		$return = $this->object->setParentId( 'unitreference' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'unitreference', $this->object->getParentId() );
	}

	public function testGetCompany()
	{
		$this->assertEquals( 'unitCompany', $this->object->getCompany() );
	}

	public function testSetCompany()
	{
		$return = $this->object->setCompany( 'company' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'company', $this->object->getCompany() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetVatID()
	{
		$this->assertEquals( 'DE999999999', $this->object->getVatID() );
	}

	public function testSetVatID()
	{
		$return = $this->object->setVatID( 'vatid' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'vatid', $this->object->getVatID() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetSalutation()
	{
		$this->assertEquals( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_MR, $this->object->getSalutation() );
	}

	public function testSetSalutation()
	{
		$return = $this->object->setSalutation( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_COMPANY );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( \Aimeos\MShop\Common\Item\Address\Base::SALUTATION_COMPANY, $this->object->getSalutation() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetTitle()
	{
		$this->assertEquals( 'Herr', $this->object->getTitle() );
	}

	public function testSetTitle()
	{
		$return = $this->object->setTitle( 'Dr.' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'Dr.', $this->object->getTitle() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetFirstname()
	{
		$this->assertEquals( 'firstunit', $this->object->getFirstname() );
	}

	public function testSetFirstname()
	{
		$return = $this->object->setFirstname( 'hans' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'hans', $this->object->getFirstname() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetLastname()
	{
		$this->assertEquals( 'lastunit', $this->object->getLastname() );
	}

	public function testSetLastname()
	{
		$return = $this->object->setLastname( 'im Glueck' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'im Glueck', $this->object->getLastname() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetAddress1()
	{
		$this->assertEquals( 'unit str.', $this->object->getAddress1() );
	}

	public function testSetAddress1()
	{
		$return = $this->object->setAddress1( 'unitallee' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'unitallee', $this->object->getAddress1() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetAddress2()
	{
		$this->assertEquals( '166', $this->object->getAddress2() );
	}

	public function testSetAddress2()
	{
		$return = $this->object->setAddress2( '12' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( '12', $this->object->getAddress2() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetAddress3()
	{
		$this->assertEquals( '4.OG', $this->object->getAddress3() );
	}

	public function testSetAddress3()
	{
		$return = $this->object->setAddress3( 'EG' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'EG', $this->object->getAddress3() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetPostal()
	{
		$this->assertEquals( '22769', $this->object->getPostal() );
	}

	public function testSetPostal()
	{
		$return = $this->object->setPostal( '11111' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( '11111', $this->object->getPostal() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetCity()
	{
		$this->assertEquals( 'Hamburg', $this->object->getCity() );
	}

	public function testSetCity()
	{
		$return = $this->object->setCity( 'unitCity' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'unitCity', $this->object->getCity() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetState()
	{
		$this->assertEquals( 'Hamburg', $this->object->getState() );
	}

	public function testSetState()
	{
		$return = $this->object->setState( 'unitState' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'unitState', $this->object->getState() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetCountryId()
	{
		$this->assertEquals( 'DE', $this->object->getCountryId() );
	}

	public function testSetCountryId()
	{
		$return = $this->object->setCountryId( 'uk' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'UK', $this->object->getCountryId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetLanguageId()
	{
		$this->assertEquals( 'de', $this->object->getLanguageId() );
	}

	public function testSetLanguageId()
	{
		$return = $this->object->setLanguageId( 'en' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'en', $this->object->getLanguageId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetTelephone()
	{
		$this->assertEquals( '05554433221', $this->object->getTelephone() );
	}

	public function testSetTelephone()
	{
		$return = $this->object->setTelephone( '55512345' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( '55512345', $this->object->getTelephone() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetEmail()
	{
		$this->assertEquals( 'test@example.com', $this->object->getEmail() );
	}

	public function testSetEmail()
	{
		$return = $this->object->setEmail( 'unit@test.de' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'unit@test.de', $this->object->getEmail() );
		$this->assertTrue( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setEmail( 'unittest.de' );
	}

	public function testGetTelefax()
	{
		$this->assertEquals( '05554433222', $this->object->getTelefax() );
	}

	public function testSetTelefax()
	{
		$return = $this->object->setTelefax( '55512345' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( '55512345', $this->object->getTelefax() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetWebsite()
	{
		$this->assertEquals( 'www.example.com', $this->object->getWebsite() );
	}

	public function testSetWebsite()
	{
		$return = $this->object->setWebsite( 'www.test.de' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 'www.test.de', $this->object->getWebsite() );
		$this->assertTrue( $this->object->isModified() );

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

	public function testGetLongitude()
	{
		$this->assertEquals( '10.0', $this->object->getLongitude() );
	}

	public function testSetLongitude()
	{
		$return = $this->object->setLongitude( '10.5' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( '10.5', $this->object->getLongitude() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetLatitude()
	{
		$this->assertEquals( '50.0', $this->object->getLatitude() );
	}

	public function testSetLatitude()
	{
		$return = $this->object->setLatitude( '53.5' );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( '53.5', $this->object->getLatitude() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->object->getPosition() );
	}

	public function testSetPosition()
	{
		$return = $this->object->setPosition( 555 );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 555, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetFlag()
	{
		$this->assertEquals( 2, $this->object->getFlag() );
	}

	public function testSetFlag()
	{
		$return = $this->object->setFlag( 5 );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
		$this->assertEquals( 5, $this->object->getFlag() );
		$this->assertTrue( $this->object->isModified() );
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
		$this->assertEquals( 'common/address', $this->object->getResourceType() );
	}


	public function testCopyFrom()
	{
		$address = new \Aimeos\MShop\Order\Item\Base\Address\Standard();
		$return = $this->object->copyFrom( $address );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\Address\Iface', $return );
	}

	public function testFromArray()
	{
		$list = array(
			'common.address.id' => 1,
			'common.address.parentid' => 2,
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
			'common.address.longitude' => '10.0',
			'common.address.latitude' => '53.5',
			'common.address.flag' => 3,
			'common.address.position' => 4,
		);

		$object = new \Aimeos\MShop\Common\Item\Address\Standard( 'common.address.' );
		$unknown = $object->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['common.address.id'], $object->getId() );
		$this->assertEquals( $list['common.address.parentid'], $object->getParentId() );
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
		$this->assertEquals( $list['common.address.longitude'], $object->getLongitude() );
		$this->assertEquals( $list['common.address.latitude'], $object->getLatitude() );
		$this->assertEquals( $list['common.address.flag'], $object->getFlag() );
		$this->assertEquals( $list['common.address.position'], $object->getPosition() );
	}

	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['common.address.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['common.address.siteid'] );
		$this->assertEquals( $this->object->getParentId(), $arrayObject['common.address.parentid'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['common.address.position'] );
		$this->assertEquals( $this->object->getCompany(), $arrayObject['common.address.company'] );
		$this->assertEquals( $this->object->getVatID(), $arrayObject['common.address.vatid'] );
		$this->assertEquals( $this->object->getSalutation(), $arrayObject['common.address.salutation'] );
		$this->assertEquals( $this->object->getTitle(), $arrayObject['common.address.title'] );
		$this->assertEquals( $this->object->getFirstname(), $arrayObject['common.address.firstname'] );
		$this->assertEquals( $this->object->getLastname(), $arrayObject['common.address.lastname'] );
		$this->assertEquals( $this->object->getAddress1(), $arrayObject['common.address.address1'] );
		$this->assertEquals( $this->object->getAddress2(), $arrayObject['common.address.address2'] );
		$this->assertEquals( $this->object->getAddress3(), $arrayObject['common.address.address3'] );
		$this->assertEquals( $this->object->getPostal(), $arrayObject['common.address.postal'] );
		$this->assertEquals( $this->object->getCity(), $arrayObject['common.address.city'] );
		$this->assertEquals( $this->object->getState(), $arrayObject['common.address.state'] );
		$this->assertEquals( $this->object->getCountryId(), $arrayObject['common.address.countryid'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['common.address.languageid'] );
		$this->assertEquals( $this->object->getTelephone(), $arrayObject['common.address.telephone'] );
		$this->assertEquals( $this->object->getEmail(), $arrayObject['common.address.email'] );
		$this->assertEquals( $this->object->getTelefax(), $arrayObject['common.address.telefax'] );
		$this->assertEquals( $this->object->getWebsite(), $arrayObject['common.address.website'] );
		$this->assertEquals( $this->object->getLongitude(), $arrayObject['common.address.longitude'] );
		$this->assertEquals( $this->object->getLatitude(), $arrayObject['common.address.latitude'] );
		$this->assertEquals( $this->object->getFlag(), $arrayObject['common.address.flag'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['common.address.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['common.address.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['common.address.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
