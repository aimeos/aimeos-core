<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2022
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
		$this->object->config();
	}


	public function testGetDatabaseManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->db();
	}


	public function testGetDateTime()
	{
		$this->assertRegexp( '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/', $this->object->datetime() );
	}


	public function testGetFilesystem()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->fs( 'fs' );
	}


	public function testGetLocale()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->locale();
	}


	public function testGetI18n()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->i18n();
	}


	public function testGetLogger()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->logger();
	}


	public function testGetMail()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->mail();
	}


	public function testGetQueue()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->queue( 'email', 'test' );
	}


	public function testGetPassword()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->password();
	}


	public function testGetProcess()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->process();
	}


	public function testGetSession()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->session();
	}


	public function testGetView()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->view();
	}


	public function testSetConfig()
	{
		$context = \TestHelper::context();
		$return = $this->object->setConfig( $context->config() );

		$this->assertSame( $context->config(), $this->object->config() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testSetDatabaseManager()
	{
		$context = \TestHelper::context();
		$return = $this->object->setDatabaseManager( $context->db() );

		$this->assertSame( $context->db(), $this->object->db() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testSetDateTime()
	{
		$return = $this->object->setDateTime( '2000-01-01 00:00:00' );

		$this->assertEquals( '2000-01-01 00:00:00', $this->object->datetime() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testSetFilesystemManager()
	{
		$context = \TestHelper::context();
		$r = $this->object->setFilesystemManager( new \Aimeos\Base\Filesystem\Manager\Standard( $context->config()->get( 'resource' ) ) );

		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $r );

		$this->object->fs( 'fs-admin' );
	}


	public function testSetI18n()
	{
		$context = \TestHelper::context();

		$locale = \Aimeos\MShop\Locale\Manager\Factory::create( \TestHelper::context() )->create();
		$this->object->setLocale( $locale->setLanguageId( 'en' ) );

		$return = $this->object->setI18n( ['en' => $context->i18n()] );

		$this->assertSame( $context->i18n(), $this->object->i18n() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testTranslate()
	{
		$context = \TestHelper::context();

		$this->assertEquals( 'mr', $context->translate( 'mshop/code', 'mr' ) );
		$this->assertEquals( 'two apples', $context->translate( 'mshop', 'one apple', 'two apples', 2 ) );
	}


	public function testSetLocale()
	{
		$locale = \Aimeos\MShop\Locale\Manager\Factory::create( \TestHelper::context() )->create();
		$return = $this->object->setLocale( $locale );

		$this->assertSame( $locale, $this->object->locale() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testSetLogger()
	{
		$context = \TestHelper::context();
		$return = $this->object->setLogger( $context->logger() );

		$this->assertSame( $context->logger(), $this->object->logger() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testSetMail()
	{
		$mail = new \Aimeos\Base\Mail\None();
		$return = $this->object->setMail( $mail );

		$this->assertSame( $mail, $this->object->mail() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testSetMessageQueueManager()
	{
		$context = \TestHelper::context();
		$mq = new \Aimeos\Base\MQueue\Manager\Standard( $context->config() );
		$return = $this->object->setMessageQueueManager( $mq );

		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );

		$this->object->queue( 'mq-test', 'test' );
	}


	public function testSetNonce()
	{
		$return = $this->object->setNonce( 'abcdef' );

		$this->assertEquals( 'abcdef', $this->object->nonce() );
		$this->assertNull( $this->object->setNonce( null )->nonce() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testSetPassword()
	{
		$password = new \Aimeos\Base\Password\Standard();
		$return = $this->object->setPassword( $password );

		$this->assertSame( $password, $this->object->password() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testSetProcess()
	{
		$process = new \Aimeos\Base\Process\Pcntl();
		$return = $this->object->setProcess( $process );

		$this->assertSame( $process, $this->object->process() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testSetSession()
	{
		$context = \TestHelper::context();
		$return = $this->object->setSession( $context->session() );

		$this->assertSame( $context->session(), $this->object->session() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testSetView()
	{
		$view = new \Aimeos\Base\View\Standard();
		$return = $this->object->setView( $view );

		$this->assertInstanceOf( \Aimeos\Base\View\Iface::class, $this->object->view() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testGetSetEditor()
	{
		$this->assertEquals( '', $this->object->editor() );

		$return = $this->object->setEditor( 'testuser' );

		$this->assertEquals( 'testuser', $this->object->editor() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testGetSetUserId()
	{
		$this->assertEquals( null, $this->object->user() );

		$return = $this->object->setUserId( 123 );
		$this->assertEquals( '123', $this->object->user() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );

		$return = $this->object->setUserId( function() { return 456; } );
		$this->assertEquals( '456', $this->object->user() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}


	public function testGetSetGroupIds()
	{
		$this->assertEquals( [], $this->object->groups() );

		$return = $this->object->setGroupIds( array( 123 ) );
		$this->assertEquals( array( '123' ), $this->object->groups() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );

		$return = $this->object->setGroupIds( function() { return array( 456 ); } );
		$this->assertEquals( array( '456' ), $this->object->groups() );
		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $return );
	}
}
