<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Item\Base\Address;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'order.base.address.id' => 23,
			'order.base.address.siteid' => 123,
			'order.base.address.baseid' => 99,
			'order.base.address.addressid' => 11,
			'order.base.address.type' => \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY,
			'order.base.address.company' => 'unitCompany',
			'order.base.address.vatid' => 'DE999999999',
			'order.base.address.salutation' => \Aimeos\MShop\Order\Item\Base\Address\Base::SALUTATION_MR,
			'order.base.address.title' => 'Herr',
			'order.base.address.firstname' => 'firstunit',
			'order.base.address.lastname' => 'lastunit',
			'order.base.address.address1' => 'unit str.',
			'order.base.address.address2' => '166',
			'order.base.address.address3' => '4.OG',
			'order.base.address.postal' => '22769',
			'order.base.address.city' => 'Hamburg',
			'order.base.address.state' => 'Hamburg',
			'order.base.address.countryid' => 'DE',
			'order.base.address.telephone' => '05554433221',
			'order.base.address.email' => 'test@example.com',
			'order.base.address.telefax' => '05554433222',
			'order.base.address.website' => 'www.example.com',
			'order.base.address.longitude' => '10.0',
			'order.base.address.latitude' => '50.0',
			'order.base.address.languageid' => 'de',
			'order.base.address.position' => 1,
			'order.base.address.birthday' => '2000-01-01',
			'order.base.address.mtime' => '2011-01-01 00:00:02',
			'order.base.address.ctime' => '2011-01-01 00:00:01',
			'order.base.address.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Order\Item\Base\Address\Standard( $this->values );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 123, $this->object->getSiteId() );
	}


	public function testGetBaseId()
	{
		$this->assertEquals( 99, $this->object->getBaseId() );
	}


	public function testSetBaseId()
	{
		$return = $this->object->setBaseId( 66 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
		$this->assertEquals( 66, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetBaseIdReset()
	{
		$return = $this->object->setBaseId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
		$this->assertEquals( null, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetAddressId()
	{
		$this->assertEquals( 11, $this->object->getAddressId() );
	}


	public function testSetAddressId()
	{
		$return = $this->object->setAddressId( 22 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
		$this->assertEquals( 22, $this->object->getAddressId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY, $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCompany()
	{
		$this->assertEquals( 'unitCompany', $this->object->getCompany() );
	}


	public function testSetCompany()
	{
		$return = $this->object->setCompany( 'company' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
		$this->assertEquals( 'vatid', $this->object->getVatID() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSalutation()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base\Address\Base::SALUTATION_MR, $this->object->getSalutation() );
	}


	public function testSetSalutation()
	{
		$return = $this->object->setSalutation( \Aimeos\MShop\Order\Item\Base\Address\Base::SALUTATION_COMPANY );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base\Address\Base::SALUTATION_COMPANY, $this->object->getSalutation() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTitle()
	{
		$this->assertEquals( 'Herr', $this->object->getTitle() );
	}


	public function testSetTitle()
	{
		$return = $this->object->setTitle( 'Dr.' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
		$this->assertEquals( 2, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPositionReset()
	{
		$return = $this->object->setPosition( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
		$this->assertEquals( null, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPositionInvalid()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->setPosition( -1 );
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
		$this->assertEquals( 'order/base/address', $this->object->getResourceType() );
	}


	public function testCopyFrom()
	{
		$address = new \Aimeos\MShop\Order\Item\Base\Address\Standard();
		$return = $this->object->copyFrom( $address );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Address\Iface::class, $return );
	}


	public function testFromArray()
	{
		$list = $entries = array(
			'order.base.address.id' => 1,
			'order.base.address.baseid' => 2,
			'order.base.address.addressid' => 3,
			'order.base.address.position' => 4,
			'order.base.address.type' => 'payment',
		);

		$object = new \Aimeos\MShop\Order\Item\Base\Address\Standard();
		$object = $object->fromArray( $entries, true );

		$this->assertEquals( $list['order.base.address.id'], $object->getId() );
		$this->assertEquals( $list['order.base.address.baseid'], $object->getBaseId() );
		$this->assertEquals( $list['order.base.address.addressid'], $object->getAddressId() );
		$this->assertEquals( $list['order.base.address.position'], $object->getPosition() );
		$this->assertEquals( $list['order.base.address.type'], $object->getType() );
	}

	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['order.base.address.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['order.base.address.siteid'] );
		$this->assertEquals( $this->object->getAddressId(), $arrayObject['order.base.address.addressid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['order.base.address.type'] );
		$this->assertEquals( $this->object->getCompany(), $arrayObject['order.base.address.company'] );
		$this->assertEquals( $this->object->getVatID(), $arrayObject['order.base.address.vatid'] );
		$this->assertEquals( $this->object->getSalutation(), $arrayObject['order.base.address.salutation'] );
		$this->assertEquals( $this->object->getTitle(), $arrayObject['order.base.address.title'] );
		$this->assertEquals( $this->object->getFirstname(), $arrayObject['order.base.address.firstname'] );
		$this->assertEquals( $this->object->getLastname(), $arrayObject['order.base.address.lastname'] );
		$this->assertEquals( $this->object->getAddress1(), $arrayObject['order.base.address.address1'] );
		$this->assertEquals( $this->object->getAddress2(), $arrayObject['order.base.address.address2'] );
		$this->assertEquals( $this->object->getAddress3(), $arrayObject['order.base.address.address3'] );
		$this->assertEquals( $this->object->getPostal(), $arrayObject['order.base.address.postal'] );
		$this->assertEquals( $this->object->getCity(), $arrayObject['order.base.address.city'] );
		$this->assertEquals( $this->object->getState(), $arrayObject['order.base.address.state'] );
		$this->assertEquals( $this->object->getCountryId(), $arrayObject['order.base.address.countryid'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['order.base.address.languageid'] );
		$this->assertEquals( $this->object->getTelephone(), $arrayObject['order.base.address.telephone'] );
		$this->assertEquals( $this->object->getEmail(), $arrayObject['order.base.address.email'] );
		$this->assertEquals( $this->object->getTelefax(), $arrayObject['order.base.address.telefax'] );
		$this->assertEquals( $this->object->getWebsite(), $arrayObject['order.base.address.website'] );
		$this->assertEquals( $this->object->getLongitude(), $arrayObject['order.base.address.longitude'] );
		$this->assertEquals( $this->object->getLatitude(), $arrayObject['order.base.address.latitude'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['order.base.address.position'] );
		$this->assertEquals( $this->object->getBirthday(), $arrayObject['order.base.address.birthday'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['order.base.address.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['order.base.address.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['order.base.address.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
