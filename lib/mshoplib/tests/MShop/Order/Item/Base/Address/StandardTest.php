<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Order\Item\Base\Address;


/**
 * Test class for \Aimeos\MShop\Order\Item\Base\Address\Standard.
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
			'id' => 23,
			'siteid' => 123,
			'baseid' => 99,
			'addrid' => 11,
			'type' => \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY,
			'company' => 'unitCompany',
			'vatid' => 'DE999999999',
			'salutation' => \Aimeos\MShop\Order\Item\Base\Address\Base::SALUTATION_MR,
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
			'email' => 'test@example.com',
			'telefax' => '05554433222',
			'website' => 'www.example.com',
			'langid' => 'de',
			'flag' => 2,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Order\Item\Base\Address\Standard( $this->values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
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
		$this->object->setBaseId( 66 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 66, $this->object->getBaseId() );
	}

	public function testSetBaseIdReset()
	{
		$this->object->setBaseId( null );
		$this->assertEquals( null, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetAddressId()
	{
		$this->assertEquals( 11, $this->object->getAddressId() );
	}

	public function testSetAddressId()
	{
		$this->object->setAddressId( 22 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 22, $this->object->getAddressId() );
	}

	public function testSetAddressIdNull()
	{
		$this->object->setAddressId( null );
		$this->assertEquals( '', $this->object->getAddressId() );
	}

	public function testGetType()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY, $this->object->getType() );
	}

	public function testSetType()
	{
		$this->object->setType( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT, $this->object->getType() );
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
		$this->assertEquals( \Aimeos\MShop\Order\item\Base\Address\Base::SALUTATION_MR, $this->object->getSalutation() );
	}

	public function testSetSalutation()
	{
		$this->object->setSalutation( \Aimeos\MShop\Order\item\Base\Address\Base::SALUTATION_COMPANY );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( \Aimeos\MShop\Order\item\Base\Address\Base::SALUTATION_COMPANY, $this->object->getSalutation() );
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
		$this->object->setLastname( 'im Glück' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'im Glück', $this->object->getLastname() );
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
		$this->object->setEmail( 'a@.' );
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

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setWebsite( 'abcde:abc' );
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

	public function testGetFlag()
	{
		$this->assertEquals( 2, $this->object->getFlag() );
	}

	public function testSetFlag()
	{
		$this->object->setFlag( 4 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 4, $this->object->getFlag() );
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
		$address = new \Aimeos\MShop\Common\Item\Address\Standard( 'common.address.', $this->values );

		$addressCopy = new \Aimeos\MShop\Order\Item\Base\Address\Standard();
		$addressCopy->copyFrom( $address );

		$this->assertEquals( 23, $addressCopy->getAddressId() );
		$this->assertEquals( 'unitCompany', $addressCopy->getCompany() );
		$this->assertEquals( 'DE999999999', $addressCopy->getVatID() );
		$this->assertEquals( \Aimeos\MShop\Order\item\Base\Address\Base::SALUTATION_MR, $addressCopy->getSalutation() );
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
		$this->assertEquals( 'test@example.com', $addressCopy->getEmail() );
		$this->assertEquals( '05554433222', $addressCopy->getTelefax() );
		$this->assertEquals( 'www.example.com', $addressCopy->getWebsite() );
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

		$object = new \Aimeos\MShop\Order\Item\Base\Address\Standard();
		$object->fromArray( $list );

		$this->assertEquals( $list['order.base.address.id'], $object->getId() );
		$this->assertEquals( $list['order.base.address.baseid'], $object->getBaseId() );
		$this->assertEquals( $list['order.base.address.addressid'], $object->getAddressId() );
		$this->assertEquals( $list['order.base.address.type'], $object->getType() );
	}

	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
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
		$this->assertEquals( $this->object->getTelephone(), $arrayObject['order.base.address.telephone'] );
		$this->assertEquals( $this->object->getEmail(), $arrayObject['order.base.address.email'] );
		$this->assertEquals( $this->object->getTelefax(), $arrayObject['order.base.address.telefax'] );
		$this->assertEquals( $this->object->getWebsite(), $arrayObject['order.base.address.website'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['order.base.address.languageid'] );
		$this->assertEquals( $this->object->getFlag(), $arrayObject['order.base.address.flag'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['order.base.address.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['order.base.address.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['order.base.address.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}
