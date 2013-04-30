<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Test class for MShop_Customer_Item_Default.
 */
class MShop_Customer_Item_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;
	protected $_values;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'MShop_Customer_Item_DefaultTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$addressValues = array(
			'id' => 23,
			'refid' => 'referenceid',
			'pos' => 1,
		);

		$this->_address = new MShop_Common_Item_Address_Default( 'common.address.', $addressValues );

		$this->_values = array(
			'id' => 541,
			'siteid' => 123,
			'label' => 'unitObject',
			'code' => '12345ABCDEF',
			'birthday' => '2010-01-01',
			'status' => 1,
			'password' => '',
			'company' => 'unitCompany',
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
			'email' => 'unit.test@metaways.de',
			'telefax' => '05554433222',
			'website' => 'www.metaways.de',
			'mtime'=> '2010-01-05 00:00:05',
			'ctime'=> '2010-01-01 00:00:00',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Customer_Item_Default( $this->_address, $this->_values, array(), array(), 'mshop' );
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
		$this->_address = null;
		$this->_values = null;
	}

	public function testGetId()
	{
		$this->assertEquals( 541, $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId( null );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertNull( $this->_object->getId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 123, $this->_object->getSiteId() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'unitObject', $this->_object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->_object->setLabel( 'newName' );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 'newName', $this->_object->getLabel() );
	}

	public function testGetCode()
	{
		$this->assertEquals( '12345ABCDEF', $this->_object->getCode() );
	}

	public function testSetCode()
	{
		$this->_object->setCode( 'neuerUser@unittest.com' );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 'neuerUser@unittest.com', $this->_object->getCode() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->_object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->_object->setStatus( 0 );
		$this->assertEquals( 0, $this->_object->getStatus() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testSetAndGetPassword()
	{
		$this->_object->setPassword( '08154712' );
		$this->assertEquals( $this->_object->getPassword(), sha1( '08154712mshop' ) );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2010-01-01 00:00:00', $this->_object->getTimeCreated() );
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( '2010-01-05 00:00:05', $this->_object->getTimeModified() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}

	public function testGetBirthday()
	{
		$this->assertEquals( '2010-01-01', $this->_object->getBirthday() );
	}

	public function testSetBirthday()
	{
		$this->_object->setBirthday( '2010-02-01' );
		$this->assertEquals( '2010-02-01', $this->_object->getBirthday() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetBillingAddress()
	{
		$address = $this->_object->getBillingAddress();
		$this->assertEquals( $address->getRefId(), 'referenceid' );
		$this->assertEquals( $address->getCompany(), 'unitCompany' );
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
		$this->assertEquals( $address->getEmail(), 'unit.test@metaways.de' );
		$this->assertEquals( $address->getTelefax(), '05554433222' );
		$this->assertEquals( $address->getWebsite(), 'www.metaways.de' );
	}

	public function testSetBillingAddress()
	{
		$this->_address->setCompany('unitCompany0815');
		$this->_object->setBillingAddress( $this->_address );
		$this->assertEquals( $this->_address, $this->_object->getBillingAddress() );
	}

	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['customer.id'] );
		$this->assertEquals( $this->_object->getLabel(), $arrayObject['customer.label'] );
		$this->assertEquals( $this->_object->getCode(), $arrayObject['customer.code'] );
		$this->assertEquals( $this->_object->getStatus(), $arrayObject['customer.status'] );
		$this->assertEquals( $this->_object->getPassword(), $arrayObject['customer.password'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['customer.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['customer.mtime'] );
		$this->assertEquals( $this->_object->getBirthday(), $arrayObject['customer.birthday'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['customer.editor'] );
		$address = $this->_object->getBillingAddress();
		$this->assertEquals( $address->getCompany(), $arrayObject['customer.company'] );
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
		$this->assertFalse( $this->_object->isModified() );
	}
}
