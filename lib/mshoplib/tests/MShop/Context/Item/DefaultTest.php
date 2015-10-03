<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MShop_Context_Item_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new MShop_Context_Item_Default();
	}

	public function testGetConfig()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getConfig();
	}

	public function testGetDatabaseManager()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getDatabaseManager();
	}

	public function testGetLocale()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getLocale();
	}

	public function testGetI18n()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getI18n();
	}

	public function testGetLogger()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getLogger();
	}

	public function testGetSession()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSession();
	}

	public function testGetMail()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getMail();
	}

	public function testGetView()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getView();
	}

	public function testSetConfig()
	{
		$context = TestHelper::getContext();
		$this->object->setConfig( $context->getConfig() );
		$this->assertSame( $context->getConfig(), $this->object->getConfig() );
	}

	public function testSetDatabaseManager()
	{
		$context = TestHelper::getContext();
		$this->object->setDatabaseManager( $context->getDatabaseManager() );
		$this->assertSame( $context->getDatabaseManager(), $this->object->getDatabaseManager() );
	}

	public function testSetI18n()
	{
		$context = TestHelper::getContext();

		$locale = MShop_Locale_Manager_Factory::createManager( TestHelper::getContext() )->createItem();
		$locale->setLanguageId( 'en' );
		$this->object->setLocale( $locale );

		$this->object->setI18n( array( 'en' => $context->getI18n() ) );
		$this->assertSame( $context->getI18n(), $this->object->getI18n() );
	}

	public function testSetLocale()
	{
		$locale = MShop_Locale_Manager_Factory::createManager( TestHelper::getContext() )->createItem();
		$this->object->setLocale( $locale );
		$this->assertSame( $locale, $this->object->getLocale() );
	}

	public function testSetLogger()
	{
		$context = TestHelper::getContext();
		$this->object->setLogger( $context->getLogger() );
		$this->assertSame( $context->getLogger(), $this->object->getLogger() );
	}

	public function testSetSession()
	{
		$context = TestHelper::getContext();
		$this->object->setSession( $context->getSession() );
		$this->assertSame( $context->getSession(), $this->object->getSession() );
	}

	public function testSetMail()
	{
		$mail = new MW_Mail_None();
		$this->object->setMail( $mail );
		$this->assertInstanceOf( 'MW_Mail_Interface', $this->object->getMail() );
	}

	public function testSetView()
	{
		$view = new MW_View_Default();
		$this->object->setView( $view );
		$this->assertInstanceOf( 'MW_View_Interface', $this->object->getView() );
	}

	public function testGetSetEditor()
	{
		$this->assertEquals( '', $this->object->getEditor() );

		$this->object->setEditor( 'testuser' );
		$this->assertEquals( 'testuser', $this->object->getEditor() );
	}

	public function testGetSetUserId()
	{
		$this->assertEquals( null, $this->object->getUserId() );

		$this->object->setUserId( 123 );
		$this->assertEquals( '123', $this->object->getUserId() );

		$this->object->setUserId( function() { return 456; } );
		$this->assertEquals( '456', $this->object->getUserId() );
	}

	public function testGetSetGroupIds()
	{
		$this->assertEquals( array(), $this->object->getGroupIds() );

		$this->object->setGroupIds( array( 123 ) );
		$this->assertEquals( array( '123' ), $this->object->getGroupIds() );

		$this->object->setGroupIds( function() { return array( 456 ); } );
		$this->assertEquals( array( '456' ), $this->object->getGroupIds() );
	}
}