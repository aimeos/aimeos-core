<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Order\Item\Address;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'order.address.id' => 23,
			'order.address.siteid' => 123,
			'order.address.parentid' => 99,
			'order.address.addressid' => 11,
			'order.address.type' => \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY,
			'order.address.company' => 'unitCompany',
			'order.address.vatid' => 'DE999999999',
			'order.address.salutation' => \Aimeos\MShop\Order\Item\Address\Base::SALUTATION_MR,
			'order.address.title' => 'Herr',
			'order.address.firstname' => 'firstunit',
			'order.address.lastname' => 'lastunit',
			'order.address.address1' => 'unit str.',
			'order.address.address2' => '166',
			'order.address.address3' => '4.OG',
			'order.address.postal' => '22769',
			'order.address.city' => 'Hamburg',
			'order.address.state' => 'Hamburg',
			'order.address.countryid' => 'DE',
			'order.address.telephone' => '05554433221',
			'order.address.email' => 'test@example.com',
			'order.address.telefax' => '05554433222',
			'order.address.website' => 'www.example.com',
			'order.address.longitude' => '10.0',
			'order.address.latitude' => '50.0',
			'order.address.languageid' => 'de',
			'order.address.position' => 1,
			'order.address.birthday' => '2000-01-01',
			'order.address.mtime' => '2011-01-01 00:00:02',
			'order.address.ctime' => '2011-01-01 00:00:01',
			'order.address.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Order\Item\Address\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( 23, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 123, $this->object->getSiteId() );
	}


	public function testGetParentId()
	{
		$this->assertEquals( 99, $this->object->getParentId() );
	}


	public function testSetParentId()
	{
		$return = $this->object->setParentId( 66 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 66, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetParentIdReset()
	{
		$return = $this->object->setParentId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( null, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetAddressId()
	{
		$this->assertEquals( 11, $this->object->getAddressId() );
	}


	public function testSetAddressId()
	{
		$return = $this->object->setAddressId( 22 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 22, $this->object->getAddressId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY, $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT, $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCompany()
	{
		$this->assertEquals( 'unitCompany', $this->object->getCompany() );
	}


	public function testSetCompany()
	{
		$return = $this->object->setCompany( 'company' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 'vatid', $this->object->getVatID() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSalutation()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Address\Base::SALUTATION_MR, $this->object->getSalutation() );
	}


	public function testSetSalutation()
	{
		$return = $this->object->setSalutation( \Aimeos\MShop\Order\Item\Address\Base::SALUTATION_COMPANY );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Address\Base::SALUTATION_COMPANY, $this->object->getSalutation() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTitle()
	{
		$this->assertEquals( 'Herr', $this->object->getTitle() );
	}


	public function testSetTitle()
	{
		$return = $this->object->setTitle( 'Dr.' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 'hans', $this->object->getFirstname() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLastname()
	{
		$this->assertEquals( 'lastunit', $this->object->getLastname() );
	}


	public function testSetLastname()
	{
		$return = $this->object->setLastname( 'im Glück' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 'im Glück', $this->object->getLastname() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetAddress1()
	{
		$this->assertEquals( 'unit str.', $this->object->getAddress1() );
	}


	public function testSetAddress1()
	{
		$return = $this->object->setAddress1( 'unitallee' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 'UK', $this->object->getCountryId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTelephone()
	{
		$this->assertEquals( '05554433221', $this->object->getTelephone() );
	}


	public function testSetTelephone()
	{
		$return = $this->object->setTelephone( '55512345' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 'unit@test.de', $this->object->getEmail() );
		$this->assertTrue( $this->object->isModified() );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setEmail( 'a@.' );
	}


	public function testGetTelefax()
	{
		$this->assertEquals( '05554433222', $this->object->getTelefax() );
	}


	public function testSetTelefax()
	{
		$return = $this->object->setTelefax( '55512345' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 'www.test.de', $this->object->getWebsite() );
		$this->assertTrue( $this->object->isModified() );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setWebsite( 'abcde:abc' );
	}


	public function testGetLongitude()
	{
		$this->assertEquals( '10.0', $this->object->getLongitude() );
	}


	public function testSetLongitude()
	{
		$return = $this->object->setLongitude( '10.5' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( '53.5', $this->object->getLatitude() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLanguageId()
	{
		$this->assertEquals( 'de', $this->object->getLanguageId() );
	}


	public function testSetLanguageId()
	{
		$return = $this->object->setLanguageId( 'en' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 'en', $this->object->getLanguageId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->object->getPosition() );
	}


	public function testSetPosition()
	{
		$return = $this->object->setPosition( 2 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 2, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPositionReset()
	{
		$return = $this->object->setPosition( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getPosition() );
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
		$this->assertEquals( 'unitTestUser', $this->object->editor() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'order/address', $this->object->getResourceType() );
	}


	public function testCopyFrom()
	{
		$address = new \Aimeos\MShop\Order\Item\Address\Standard();
		$return = $this->object->copyFrom( $address );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $return );
	}


	public function testFromArray()
	{
		$list = $entries = array(
			'order.address.id' => 1,
			'order.address.parentid' => 2,
			'order.address.addressid' => 3,
			'order.address.position' => 4,
			'order.address.type' => 'payment',
		);

		$object = new \Aimeos\MShop\Order\Item\Address\Standard();
		$object = $object->fromArray( $entries, true );

		$this->assertEquals( $list['order.address.id'], $object->getId() );
		$this->assertEquals( $list['order.address.parentid'], $object->getParentId() );
		$this->assertEquals( $list['order.address.addressid'], $object->getAddressId() );
		$this->assertEquals( $list['order.address.position'], $object->getPosition() );
		$this->assertEquals( $list['order.address.type'], $object->getType() );
	}

	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['order.address.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['order.address.siteid'] );
		$this->assertEquals( $this->object->getAddressId(), $arrayObject['order.address.addressid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['order.address.type'] );
		$this->assertEquals( $this->object->getCompany(), $arrayObject['order.address.company'] );
		$this->assertEquals( $this->object->getVatID(), $arrayObject['order.address.vatid'] );
		$this->assertEquals( $this->object->getSalutation(), $arrayObject['order.address.salutation'] );
		$this->assertEquals( $this->object->getTitle(), $arrayObject['order.address.title'] );
		$this->assertEquals( $this->object->getFirstname(), $arrayObject['order.address.firstname'] );
		$this->assertEquals( $this->object->getLastname(), $arrayObject['order.address.lastname'] );
		$this->assertEquals( $this->object->getAddress1(), $arrayObject['order.address.address1'] );
		$this->assertEquals( $this->object->getAddress2(), $arrayObject['order.address.address2'] );
		$this->assertEquals( $this->object->getAddress3(), $arrayObject['order.address.address3'] );
		$this->assertEquals( $this->object->getPostal(), $arrayObject['order.address.postal'] );
		$this->assertEquals( $this->object->getCity(), $arrayObject['order.address.city'] );
		$this->assertEquals( $this->object->getState(), $arrayObject['order.address.state'] );
		$this->assertEquals( $this->object->getCountryId(), $arrayObject['order.address.countryid'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['order.address.languageid'] );
		$this->assertEquals( $this->object->getTelephone(), $arrayObject['order.address.telephone'] );
		$this->assertEquals( $this->object->getEmail(), $arrayObject['order.address.email'] );
		$this->assertEquals( $this->object->getTelefax(), $arrayObject['order.address.telefax'] );
		$this->assertEquals( $this->object->getWebsite(), $arrayObject['order.address.website'] );
		$this->assertEquals( $this->object->getLongitude(), $arrayObject['order.address.longitude'] );
		$this->assertEquals( $this->object->getLatitude(), $arrayObject['order.address.latitude'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['order.address.position'] );
		$this->assertEquals( $this->object->getBirthday(), $arrayObject['order.address.birthday'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['order.address.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['order.address.mtime'] );
		$this->assertEquals( $this->object->editor(), $arrayObject['order.address.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
