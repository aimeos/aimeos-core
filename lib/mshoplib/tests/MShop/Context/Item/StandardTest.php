<?php

namespace Aimeos\MShop\Context\Item;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MShop\Context\Item\Standard();
	}

	public function testGetConfig()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getConfig();
	}

	public function testGetDatabaseManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getDatabaseManager();
	}

	public function testGetFilesystemManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getFilesystemManager();
	}

	public function testGetFilesystem()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getFilesystem( 'fs' );
	}

	public function testGetLocale()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getLocale();
	}

	public function testGetI18n()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getI18n();
	}

	public function testGetLogger()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getLogger();
	}

	public function testGetMail()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getMail();
	}

	public function testGetMessageQueueManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getMessageQueueManager();
	}

	public function testGetMessageQueue()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getMessageQueue( 'email', 'test' );
	}

	public function testGetSession()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSession();
	}

	public function testGetView()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getView();
	}

	public function testSetConfig()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setConfig( $context->getConfig() );

		$this->assertSame( $context->getConfig(), $this->object->getConfig() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}

	public function testSetDatabaseManager()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setDatabaseManager( $context->getDatabaseManager() );

		$this->assertSame( $context->getDatabaseManager(), $this->object->getDatabaseManager() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}

	public function testSetFilesystemManager()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setFilesystemManager( $context->getFilesystemManager() );

		$this->assertSame( $context->getFilesystemManager(), $this->object->getFilesystemManager() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );

		$this->object->getFilesystem( 'fs-admin' );
	}

	public function testSetI18n()
	{
		$context = \TestHelperMShop::getContext();

		$locale = \Aimeos\MShop\Locale\Manager\Factory::createManager( \TestHelperMShop::getContext() )->createItem();
		$locale->setLanguageId( 'en' );
		$this->object->setLocale( $locale );

		$return = $this->object->setI18n( array( 'en' => $context->getI18n() ) );

		$this->assertSame( $context->getI18n(), $this->object->getI18n() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}

	public function testSetLocale()
	{
		$locale = \Aimeos\MShop\Locale\Manager\Factory::createManager( \TestHelperMShop::getContext() )->createItem();
		$return = $this->object->setLocale( $locale );

		$this->assertSame( $locale, $this->object->getLocale() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}

	public function testSetLogger()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setLogger( $context->getLogger() );

		$this->assertSame( $context->getLogger(), $this->object->getLogger() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}

	public function testSetMail()
	{
		$mail = new \Aimeos\MW\Mail\None();
		$return = $this->object->setMail( $mail );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Mail\\Iface', $this->object->getMail() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}

	public function testSetMessageQueueManager()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setMessageQueueManager( $context->getMessageQueueManager() );

		$this->assertSame( $context->getMessageQueueManager(), $this->object->getMessageQueueManager() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );

		$this->object->getMessageQueue( 'mq-test', 'test' );
	}

	public function testSetSession()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setSession( $context->getSession() );

		$this->assertSame( $context->getSession(), $this->object->getSession() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}

	public function testSetView()
	{
		$view = new \Aimeos\MW\View\Standard();
		$return = $this->object->setView( $view );

		$this->assertInstanceOf( '\\Aimeos\\MW\\View\\Iface', $this->object->getView() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}

	public function testGetSetEditor()
	{
		$this->assertEquals( '', $this->object->getEditor() );

		$return = $this->object->setEditor( 'testuser' );

		$this->assertEquals( 'testuser', $this->object->getEditor() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}

	public function testGetSetUserId()
	{
		$this->assertEquals( null, $this->object->getUserId() );

		$return = $this->object->setUserId( 123 );
		$this->assertEquals( '123', $this->object->getUserId() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );

		$return = $this->object->setUserId( function() { return 456; } );
		$this->assertEquals( '456', $this->object->getUserId() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}

	public function testGetSetGroupIds()
	{
		$this->assertEquals( [], $this->object->getGroupIds() );

		$return = $this->object->setGroupIds( array( 123 ) );
		$this->assertEquals( array( '123' ), $this->object->getGroupIds() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );

		$return = $this->object->setGroupIds( function() { return array( 456 ); } );
		$this->assertEquals( array( '456' ), $this->object->getGroupIds() );
		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $return );
	}
}