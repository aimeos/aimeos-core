<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Context\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MShop\Context\Item\Standard();
	}

	public function testGetConfig()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getConfig();
	}

	public function testGetDatabaseManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getDatabaseManager();
	}

	public function testGetDateTime()
	{
		$this->assertRegexp( '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/', $this->object->getDateTime() );
	}

	public function testGetFilesystemManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getFilesystemManager();
	}

	public function testGetFilesystem()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getFilesystem( 'fs' );
	}

	public function testGetLocale()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getLocale();
	}

	public function testGetI18n()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getI18n();
	}

	public function testGetLogger()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getLogger();
	}

	public function testGetMail()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getMail();
	}

	public function testGetMessageQueueManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getMessageQueueManager();
	}

	public function testGetMessageQueue()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getMessageQueue( 'email', 'test' );
	}

	public function testGetProcess()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getProcess();
	}

	public function testGetSession()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSession();
	}

	public function testGetView()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getView();
	}

	public function testSetConfig()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setConfig( $context->getConfig() );

		$this->assertSame( $context->getConfig(), $this->object->getConfig() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testSetDatabaseManager()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setDatabaseManager( $context->getDatabaseManager() );

		$this->assertSame( $context->getDatabaseManager(), $this->object->getDatabaseManager() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testSetDateTime()
	{
		$return = $this->object->setDateTime( '2000-01-01 00:00:00' );

		$this->assertEquals( '2000-01-01 00:00:00', $this->object->getDateTime() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testSetFilesystemManager()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setFilesystemManager( $context->getFilesystemManager() );

		$this->assertSame( $context->getFilesystemManager(), $this->object->getFilesystemManager() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );

		$this->object->getFilesystem( 'fs-admin' );
	}

	public function testSetI18n()
	{
		$context = \TestHelperMShop::getContext();

		$locale = \Aimeos\MShop\Locale\Manager\Factory::create( \TestHelperMShop::getContext() )->createItem();
		$locale->setLanguageId( 'en' );
		$this->object->setLocale( $locale );

		$return = $this->object->setI18n( array( 'en' => $context->getI18n() ) );

		$this->assertSame( $context->getI18n(), $this->object->getI18n() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testSetLocale()
	{
		$locale = \Aimeos\MShop\Locale\Manager\Factory::create( \TestHelperMShop::getContext() )->createItem();
		$return = $this->object->setLocale( $locale );

		$this->assertSame( $locale, $this->object->getLocale() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testSetLogger()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setLogger( $context->getLogger() );

		$this->assertSame( $context->getLogger(), $this->object->getLogger() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testSetMail()
	{
		$mail = new \Aimeos\MW\Mail\None();
		$return = $this->object->setMail( $mail );

		$this->assertInstanceOf( \Aimeos\MW\Mail\Iface::class, $this->object->getMail() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testSetMessageQueueManager()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setMessageQueueManager( $context->getMessageQueueManager() );

		$this->assertSame( $context->getMessageQueueManager(), $this->object->getMessageQueueManager() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );

		$this->object->getMessageQueue( 'mq-test', 'test' );
	}

	public function testSetSession()
	{
		$context = \TestHelperMShop::getContext();
		$return = $this->object->setSession( $context->getSession() );

		$this->assertSame( $context->getSession(), $this->object->getSession() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testSetProcess()
	{
		$process = new \Aimeos\MW\Process\Pcntl();
		$return = $this->object->setProcess( $process );

		$this->assertSame( $process, $this->object->getProcess() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testSetView()
	{
		$view = new \Aimeos\MW\View\Standard();
		$return = $this->object->setView( $view );

		$this->assertInstanceOf( \Aimeos\MW\View\Iface::class, $this->object->getView() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testGetSetEditor()
	{
		$this->assertEquals( '', $this->object->getEditor() );

		$return = $this->object->setEditor( 'testuser' );

		$this->assertEquals( 'testuser', $this->object->getEditor() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testGetSetUserId()
	{
		$this->assertEquals( null, $this->object->getUserId() );

		$return = $this->object->setUserId( 123 );
		$this->assertEquals( '123', $this->object->getUserId() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );

		$return = $this->object->setUserId( function() { return 456; } );
		$this->assertEquals( '456', $this->object->getUserId() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}

	public function testGetSetGroupIds()
	{
		$this->assertEquals( [], $this->object->getGroupIds() );

		$return = $this->object->setGroupIds( array( 123 ) );
		$this->assertEquals( array( '123' ), $this->object->getGroupIds() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );

		$return = $this->object->setGroupIds( function() { return array( 456 ); } );
		$this->assertEquals( array( '456' ), $this->object->getGroupIds() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}
}
