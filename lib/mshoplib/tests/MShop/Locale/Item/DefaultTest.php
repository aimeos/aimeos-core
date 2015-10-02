<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Locale_Item_Default.
 */
class MShop_Locale_Item_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $siteItem;
	private $values;


	protected function setUp()
	{
		$manager = MShop_Locale_Manager_Factory::createManager( TestHelper::getContext() );
		$this->siteItem = $manager->getSubManager( 'site' )->createItem();

		$this->values = array(
			'id' => 1,
			'siteid' => 1,
			'langid' => 'de',
			'currencyid' => 'EUR',
			'pos' => 1,
			'status' => 1,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new MShop_Locale_Item_Default(
			$this->values,
			$this->siteItem,
			array( 1, 2 ),
			array( 1, 3, 4 )
		);
	}


	protected function tearDown()
	{
		unset( $this->object, $this->values );
	}


	public function testGetSite()
	{
		$this->assertInstanceOf( 'MShop_Locale_Item_Site_Interface', $this->object->getSite() );

		$wrongobject = new MShop_Locale_Item_Default();
		$this->setExpectedException( 'MShop_Locale_Exception' );
		$wrongobject->getSite();
	}


	public function testGetSiteId()
	{
		$this->assertEquals( '1', $this->object->getSiteId() );
	}


	public function testGetSitePath()
	{
		$this->assertEquals( array( 1, 2 ), $this->object->getSitePath() );
	}


	public function testGetSiteSubTree()
	{
		$this->assertEquals( array( 1, 3, 4 ), $this->object->getSiteSubTree() );
	}


	public function testSetSiteId()
	{
		$this->object->setSiteId( 5 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( '5', $this->object->getSiteId() );
		$this->assertEquals( array( 5 ), $this->object->getSitePath() );
		$this->assertEquals( array( 5 ), $this->object->getSiteSubTree() );
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


	public function testSetLanguageIdNull()
	{
		$this->object->setLanguageId( null );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( null, $this->object->getLanguageId() );
	}


	public function testSetLanguageIdCountry()
	{
		$this->object->setLanguageId( 'en_GB' );
		$this->assertEquals( 'en_GB', $this->object->getLanguageId() );
	}


	public function testSetLanguageIdInvalid()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->setLanguageId( 'test' );
	}


	public function testSetLanguageIdCountryInvalid()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->setLanguageId( 'en-GB' );
	}


	public function testSetLanguageIdCountryInvalidLowerCase()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->setLanguageId( 'en_gb' );
	}


	public function testGetCurrencyId()
	{
		$this->assertEquals( 'EUR', $this->object->getCurrencyId() );
	}


	public function testSetCurrencyId()
	{
		$this->object->setCurrencyId( 'AWG' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'AWG', $this->object->getCurrencyId() );
	}


	public function testSetCurrencyIdNull()
	{
		$this->object->setCurrencyId( null );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( null, $this->object->getCurrencyId() );
	}


	public function testSetCurrencyIdInvalid()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->setCurrencyId( 'TEST' );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->object->getPosition() );
	}


	public function testSetPosition()
	{
		$this->object->setPosition( 2 );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 2, $this->object->getPosition() );
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


	public function testFromArray()
	{
		$item = new MShop_Locale_Item_Default();

		$list = array(
			'locale.id' => 1,
			'locale.siteid' => 2,
			'locale.languageid' => 'de',
			'locale.currencyid' => 'EUR',
			'locale.position' => 10,
			'locale.status' => 0,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['locale.id'], $item->getId() );
		$this->assertEquals( $list['locale.siteid'], $item->getSiteId() );
		$this->assertEquals( $list['locale.languageid'], $item->getLanguageId() );
		$this->assertEquals( $list['locale.currencyid'], $item->getCurrencyId() );
		$this->assertEquals( $list['locale.position'], $item->getPosition() );
		$this->assertEquals( $list['locale.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['locale.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['locale.siteid'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['locale.languageid'] );
		$this->assertEquals( $this->object->getCurrencyId(), $arrayObject['locale.currencyid'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['locale.position'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['locale.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['locale.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['locale.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['locale.editor'] );
	}

}
