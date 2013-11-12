<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class MShop_Context_Item_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;

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

	public function testGetI18n()
	{
		$this->setExpectedException('MShop_Exception');
		$locale = $this->_object->getI18n();
	}

	public function testGetLogger()
	{
		$this->setExpectedException('MShop_Exception');
		$logger = $this->_object->getLogger();
	}

	public function testGetSession()
	{
		$this->setExpectedException('MShop_Exception');
		$config = $this->_object->getSession();
	}

	public function testGetMail()
	{
		$this->setExpectedException('MShop_Exception');
		$config = $this->_object->getMail();
	}

	public function testGetView()
	{
		$this->setExpectedException('MShop_Exception');
		$config = $this->_object->getView();
	}

	public function testSetConfig()
	{
		$context = TestHelper::getContext();
		$this->_object->setConfig( $context->getConfig() );
		$this->assertSame( $context->getConfig(), $this->_object->getConfig() );
	}

	public function testSetDatabaseManager()
	{
		$context = TestHelper::getContext();
		$this->_object->setDatabaseManager( $context->getDatabaseManager() );
		$this->assertSame( $context->getDatabaseManager(), $this->_object->getDatabaseManager() );
	}

	public function testSetI18n()
	{
		$context = TestHelper::getContext();

		$locale = MShop_Locale_Manager_Factory::createManager(TestHelper::getContext())->createItem();
		$locale->setLanguageId( 'en' );
		$this->_object->setLocale( $locale );

		$this->_object->setI18n( array( 'en' => $context->getI18n() ) );
		$this->assertSame( $context->getI18n(), $this->_object->getI18n() );
	}

	public function testSetLocale()
	{
		$locale = MShop_Locale_Manager_Factory::createManager(TestHelper::getContext())->createItem();
		$this->_object->setLocale( $locale );
		$this->assertSame( $locale, $this->_object->getLocale() );
	}

	public function testSetLogger()
	{
		$context = TestHelper::getContext();
		$this->_object->setLogger( $context->getLogger() );
		$this->assertSame( $context->getLogger(), $this->_object->getLogger() );
	}

	public function testSetSession()
	{
		$context = TestHelper::getContext();
		$this->_object->setSession( $context->getSession() );
		$this->assertSame( $context->getSession(), $this->_object->getSession() );
	}

	/**
	 * @todo Implement test for setMail() as soon as a default implementation is available
	 */
	public function testSetMail()
	{
	}

	public function testSetView()
	{
		$view = new MW_View_Default();
		$this->_object->setView( $view );
		$this->assertInstanceOf( 'MW_View_Interface', $this->_object->getView() );
	}

	public function testGetSetEditor()
	{
		$this->assertEquals( '', $this->_object->getEditor() );

		$this->_object->setEditor( 'testuser' );
		$this->assertEquals( 'testuser', $this->_object->getEditor() );
	}

	public function testGetSetUserId()
	{
		$this->assertEquals( null, $this->_object->getUserId() );

		$this->_object->setUserId( 123 );
		$this->assertEquals( '123', $this->_object->getUserId() );
	}
}