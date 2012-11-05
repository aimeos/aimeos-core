<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14602 2011-12-27 15:27:08Z gwussow $
 */

class MShop_Context_Item_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;

	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Context_Item_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	protected function setUp()
	{
		$this->_object = new MShop_Context_Item_Default();
	}

	public function testGetConfig()
	{
		$this->setExpectedException('MShop_Exception');
		$config = $this->_object->getConfig();
	}

	public function testGetSession()
	{
		$this->setExpectedException('MShop_Exception');
		$config = $this->_object->getSession();
	}

	public function testGetDatabaseManager()
	{
		$this->setExpectedException('MShop_Exception');
		$dbm = $this->_object->getDatabaseManager();
	}

	public function testGetLocale()
	{
		$this->setExpectedException('MShop_Exception');
		$locale = $this->_object->getLocale();
	}

	public function testGetLogger()
	{
		$this->setExpectedException('MShop_Exception');
		$logger = $this->_object->getLogger();
	}

	public function testSetEditor()
	{
		$this->_object->setEditor( 'testuser' );
		$this->assertEquals( 'testuser', $this->_object->getEditor() );
		$this->_object->setEditor( '' );
		$this->assertEquals( '', $this->_object->getEditor() );
	}

	public function testSetLocale()
	{
		$locale = MShop_Locale_Manager_Factory::createManager(TestHelper::getContext())->createItem();
		$this->_object->setLocale($locale);
		$this->assertSame($locale, $this->_object->getLocale());
	}

	public function testSetSession()
	{
		$context = TestHelper::getContext();
		$this->_object->setSession($context->getSession());
		$this->assertEquals($this->_object->getSession(), $context->getSession());
	}

	public function testSetDatabaseManager()
	{
		$context = TestHelper::getContext();
		$this->_object->setDatabaseManager($context->getDatabaseManager());
		$this->assertEquals($this->_object->getDatabaseManager(), $context->getDatabaseManager());
	}

	public function testSetConfig()
	{
		$context = TestHelper::getContext();
		$this->_object->setConfig($context->getConfig());
		$this->assertEquals($this->_object->getConfig(), $context->getConfig());
	}

	public function testSetLogger()
	{
		$context = TestHelper::getContext();
		$this->_object->setLogger($context->getLogger());
		$this->assertEquals($this->_object->getLogger(), $context->getLogger());
	}
}