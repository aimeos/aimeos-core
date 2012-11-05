<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Test class for MShop_Locale_Item_Default.
 */
class MShop_Locale_Item_DefaultTest extends MW_Unittest_Testcase
{

	protected $_object;
	protected $_siteItem;
	protected $_values;


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('MShop_Locale_Item_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	protected function setUp()
	{
		$manager = MShop_Locale_Manager_Factory::createManager(TestHelper::getContext());
		$this->_siteItem = $manager->getSubManager('site')->createItem();

		$this->_values = array(
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

		$this->_object = new MShop_Locale_Item_Default(
			$this->_values,
			$this->_siteItem,
			array( 1, 2 ),
			array( 1, 3, 4 )
		);
	}


	protected function tearDown()
	{
		unset($this->_object, $this->_values);
	}


	public function testGetSite()
	{
		$this->assertInstanceOf('MShop_Locale_Item_Site_Interface', $this->_object->getSite());

		$wrongobject = new MShop_Locale_Item_Default();
		$this->setExpectedException('MShop_Locale_Exception');
		$wrongobject->getSite();
	}


	public function testGetSiteId()
	{
		$this->assertEquals('1', $this->_object->getSiteId());
	}


	public function testGetSitePath()
	{
		$this->assertEquals( array( 1, 2 ), $this->_object->getSitePath() );
	}


	public function testGetSiteSubTree()
	{
		$this->assertEquals( array( 1, 3, 4 ), $this->_object->getSiteSubTree() );
	}


	public function testSetSiteId()
	{
		$this->_object->setSiteId(5);
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals('5', $this->_object->getSiteId());
		$this->assertEquals( array( 5 ), $this->_object->getSitePath());
		$this->assertEquals( array( 5 ), $this->_object->getSiteSubTree());
	}


	public function testGetLanguageId()
	{
		$this->assertEquals('de', $this->_object->getLanguageId());
	}


	public function testSetLanguageId()
	{
		$this->_object->setLanguageId('en');
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals('en', $this->_object->getLanguageId());

		$this->setExpectedException('MShop_Locale_Exception');
		$this->_object->setLanguageId('test');
	}


	public function testGetCurrencyId()
	{
		$this->assertEquals('EUR', $this->_object->getCurrencyId());
	}


	public function testSetCurrencyId()
	{
		$this->_object->setCurrencyId('AWG');
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals('AWG', $this->_object->getCurrencyId());

		$this->setExpectedException('MShop_Locale_Exception');
		$this->_object->setCurrencyId('TEST');
	}


	public function testGetPosition()
	{
		$this->assertEquals(1, $this->_object->getPosition());
	}


	public function testSetPosition()
	{
		$this->_object->setPosition(2);
		$this->assertTrue($this->_object->isModified());
		$this->assertEquals(2, $this->_object->getPosition());
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


	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals(count($this->_values), count($arrayObject));

		$this->assertEquals($this->_object->getId(), $arrayObject['locale.id']);
		$this->assertEquals($this->_object->getSiteId(), $arrayObject['locale.siteid']);
		$this->assertEquals($this->_object->getLanguageId(), $arrayObject['locale.languageid']);
		$this->assertEquals($this->_object->getCurrencyId(), $arrayObject['locale.currencyid']);
		$this->assertEquals($this->_object->getPosition(), $arrayObject['locale.position']);
		$this->assertEquals($this->_object->getStatus(), $arrayObject['locale.status']);
		$this->assertEquals($this->_object->getTimeCreated(), $arrayObject['locale.ctime'] );
		$this->assertEquals($this->_object->getTimeModified(), $arrayObject['locale.mtime'] );
		$this->assertEquals($this->_object->getEditor(), $arrayObject['locale.editor'] );
	}

}
