<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Locale\Item;

use \Aimeos\MShop\Locale\Manager\Base as Locale;


/**
 * Test class for \Aimeos\MShop\Locale\Item\Standard.
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $siteItem;
	private $values;


	protected function setUp() : void
	{
		$manager = \Aimeos\MShop\Locale\Manager\Factory::create( \TestHelperMShop::getContext() );
		$this->siteItem = $manager->getSubManager( 'site' )->create();

		$this->values = array(
			'locale.id' => 1,
			'locale.siteid' => 1,
			'locale.languageid' => 'de',
			'locale.currencyid' => 'EUR',
			'locale.position' => 1,
			'locale.status' => 1,
			'locale.mtime' => '2011-01-01 00:00:02',
			'locale.ctime' => '2011-01-01 00:00:01',
			'locale.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Locale\Item\Standard( $this->values, $this->siteItem,
			[Locale::SITE_ONE => '/1/2/', Locale::SITE_PATH => ['/1/', '/1/2'], Locale::SITE_SUBTREE => '/1/2/']
		);
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->values );
	}


	public function testGetSite()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $this->object->getSiteItem() );

		$wrongobject = new \Aimeos\MShop\Locale\Item\Standard();
		$this->expectException( \Aimeos\MShop\Locale\Exception::class );
		$wrongobject->getSiteItem();
	}


	public function testGetSiteId()
	{
		$this->assertEquals( '1', $this->object->getSiteId() );
	}


	public function testGetSites()
	{
		$expected = [Locale::SITE_ONE => '/1/2/', Locale::SITE_PATH => ['/1/', '/1/2'], Locale::SITE_SUBTREE => '/1/2/'];

		$this->assertEquals( $expected, $this->object->getSites() );
		$this->assertEquals( '/1/2/', $this->object->getSites( Locale::SITE_ONE ) );
		$this->assertEquals( ['/1/', '/1/2'], $this->object->getSites( Locale::SITE_PATH ) );
		$this->assertEquals( '/1/2/', $this->object->getSites( Locale::SITE_SUBTREE ) );
	}


	public function testSetSiteId()
	{
		$return = $this->object->setSiteId( '/5/' );

		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( '/5/', $this->object->getSiteId() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $return );
	}


	public function testGetLanguageId()
	{
		$this->assertEquals( 'de', $this->object->getLanguageId() );
	}


	public function testSetLanguageId()
	{
		$return = $this->object->setLanguageId( 'en' );

		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'en', $this->object->getLanguageId() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $return );
	}


	public function testSetLanguageIdNull()
	{
		$return = $this->object->setLanguageId( null );

		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( null, $this->object->getLanguageId() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $return );
	}


	public function testSetLanguageIdCountry()
	{
		$return = $this->object->setLanguageId( 'en_GB' );

		$this->assertEquals( 'en_GB', $this->object->getLanguageId() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $return );
	}


	public function testSetLanguageIdInvalid()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setLanguageId( 'test' );
	}


	public function testSetLanguageIdCountryInvalid()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setLanguageId( 'en-GB' );
	}


	public function testGetCurrencyId()
	{
		$this->assertEquals( 'EUR', $this->object->getCurrencyId() );
	}


	public function testSetCurrencyId()
	{
		$return = $this->object->setCurrencyId( 'AWG' );

		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'AWG', $this->object->getCurrencyId() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $return );
	}


	public function testSetCurrencyIdNull()
	{
		$return = $this->object->setCurrencyId( null );

		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( null, $this->object->getCurrencyId() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $return );
	}


	public function testSetCurrencyIdInvalid()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setCurrencyId( 'TEST' );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->object->getPosition() );
	}


	public function testSetPosition()
	{
		$return = $this->object->setPosition( 2 );

		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 2, $this->object->getPosition() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $return );
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
		$this->assertEquals( 'locale', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Locale\Item\Standard();

		$list = $entries = array(
			'locale.id' => 1,
			'locale.siteid' => 2,
			'locale.languageid' => 'de',
			'locale.currencyid' => 'EUR',
			'locale.position' => 10,
			'locale.status' => 0,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['locale.id'], $item->getId() );
		$this->assertEquals( $list['locale.siteid'], $item->getSiteId() );
		$this->assertEquals( $list['locale.languageid'], $item->getLanguageId() );
		$this->assertEquals( $list['locale.currencyid'], $item->getCurrencyId() );
		$this->assertEquals( $list['locale.position'], $item->getPosition() );
		$this->assertEquals( $list['locale.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );
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


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setAvailable( false );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsAvailableOnStatus()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setStatus( 0 );
		$this->assertFalse( $this->object->isAvailable() );
		$this->object->setStatus( -1 );
		$this->assertFalse( $this->object->isAvailable() );
	}

}
