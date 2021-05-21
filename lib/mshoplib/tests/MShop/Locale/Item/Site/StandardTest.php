<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MShop\Locale\Item\Site;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'locale.site.id' => 12,
			'locale.site.siteid' => 12,
			'locale.site.code' => 'ExtID',
			'locale.site.label' => 'My Site',
			'locale.site.config' => array( 'timezone' => 'Europe/Berlin' ),
			'locale.site.icon' => 'path/to/site-icon.png',
			'locale.site.logo' => [1 => 'path/to/site-logo.png'],
			'locale.site.supplierid' => '1234',
			'locale.site.theme' => 'elegance',
			'locale.site.status' => 1,
			'locale.site.mtime' => '2011-01-01 00:00:02',
			'locale.site.ctime' => '2011-01-01 00:00:01',
			'locale.site.editor' => 'unitTestUser'
		);

		$children = array( new \Aimeos\MShop\Locale\Item\Site\Standard() );
		$this->object = new \Aimeos\MShop\Locale\Item\Site\Standard( $this->values, $children );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetId()
	{
		$this->assertEquals( 12, $this->object->getId() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 12, $this->object->getSiteId() );
	}

	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 12 );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'ExtID', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'OtherExtID' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertEquals( 'OtherExtID', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'My Site', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'Other Name' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertEquals( 'Other Name', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetIcon()
	{
		$this->assertEquals( 'path/to/site-icon.png', $this->object->getIcon() );
	}


	public function testSetIcon()
	{
		$return = $this->object->setIcon( 'path/site-icon2.png' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertEquals( 'path/site-icon2.png', $this->object->getIcon() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLogo()
	{
		$this->assertEquals( 'path/to/site-logo.png', $this->object->getLogo() );
	}


	public function testGetLogos()
	{
		$this->assertEquals( [1 => 'path/to/site-logo.png'], $this->object->getLogos() );
	}


	public function testSetLogo()
	{
		$return = $this->object->setLogo( '/path/site-logo2.png' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertEquals( '/path/site-logo2.png', $this->object->getLogo() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetLogos()
	{
		$return = $this->object->setLogos( [1 => '/path/site-logo2.png'] );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertEquals( [1 => '/path/site-logo2.png'], $this->object->getLogos() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetConfig()
	{
		$this->assertEquals( array( 'timezone' => 'Europe/Berlin' ), $this->object->getConfig() );
	}


	public function testGetConfigValue()
	{
		$this->assertEquals( 'Europe/Berlin', $this->object->getConfigValue( 'timezone' ) );
	}


	public function testSetConfig()
	{
		$return = $this->object->setConfig( array( 'timezone' => 'Europe/Paris' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertEquals( array( 'timezone' => 'Europe/Paris' ), $this->object->getConfig() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSupplierId()
	{
		$this->assertEquals( '1234', $this->object->getSupplierId() );
	}


	public function testSetSupplierId()
	{
		$return = $this->object->setSupplierId( '5678' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertEquals( '5678', $this->object->getSupplierId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTheme()
	{
		$this->assertEquals( 'elegance', $this->object->getTheme() );
	}


	public function testSetTheme()
	{
		$return = $this->object->setTheme( 'shop' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $return );
		$this->assertEquals( 'shop', $this->object->getTheme() );
		$this->assertTrue( $this->object->isModified() );
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
		$this->assertEquals( 'locale/site', $this->object->getResourceType() );
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


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Locale\Item\Site\Standard();

		$list = $entries = array(
			'locale.site.id' => 2,
			'locale.site.code' => 'test',
			'locale.site.label' => 'test item',
			'locale.site.status' => 1,
			'locale.site.config' => ['test'],
			'locale.site.icon' => 'site-icon.jpg',
			'locale.site.logo' => [1 => 'site-logo.jpg'],
			'locale.site.supplierid' => '9876',
			'locale.site.theme' => 'shop',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['locale.site.id'], $item->getId() );
		$this->assertEquals( $list['locale.site.code'], $item->getCode() );
		$this->assertEquals( $list['locale.site.label'], $item->getLabel() );
		$this->assertEquals( $list['locale.site.status'], $item->getStatus() );
		$this->assertEquals( $list['locale.site.config'], $item->getConfig() );
		$this->assertEquals( $list['locale.site.supplierid'], $item->getSupplierId() );
		$this->assertEquals( $list['locale.site.theme'], $item->getTheme() );
		$this->assertEquals( $list['locale.site.logo'], $item->getLogos() );
		$this->assertEquals( $list['locale.site.icon'], $item->getIcon() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ) + 3, count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['locale.site.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['locale.site.siteid'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['locale.site.code'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['locale.site.label'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['locale.site.status'] );
		$this->assertEquals( $this->object->getConfig(), $arrayObject['locale.site.config'] );
		$this->assertEquals( $this->object->getLogos(), $arrayObject['locale.site.logo'] );
		$this->assertEquals( $this->object->getIcon(), $arrayObject['locale.site.icon'] );
		$this->assertEquals( $this->object->getTheme(), $arrayObject['locale.site.theme'] );
		$this->assertEquals( $this->object->getSupplierId(), $arrayObject['locale.site.supplierid'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['locale.site.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['locale.site.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['locale.site.editor'] );
	}


	public function testAddChild()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Tree\Iface::class, $this->object->addChild( $this->object ) );
	}


	public function testDeleteChild()
	{
		$result = $this->object->deleteChild( $this->object );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $result );
		$this->assertEquals( 0, count( $this->object->getChildren() ) );
	}


	public function testGetChild()
	{
		$this->expectException( \Aimeos\MShop\Locale\Exception::class );
		$this->object->getChild( 0 );
	}


	public function testGetChildren()
	{
		$this->assertInstanceOf( \Aimeos\Map::class, $this->object->getChildren() );
		$this->assertEquals( [], $this->object->getChildren()->toArray() );
	}


	public function testGetChildrenDeleted()
	{
		$result = $this->object->getChildrenDeleted();

		$this->assertInstanceOf( \Aimeos\Map::class, $result );
		$this->assertEquals( [], $result->toArray() );
	}


	public function testToList()
	{
		$list = $this->object->toList();

		$this->assertEquals( 1, count( $list ) );
		$this->assertInstanceOf( \Aimeos\Map::class, $list );
		$this->assertEquals( [$this->object->getId() => $this->object], $list->toArray() );
	}


	public function testHasChildren()
	{
		$this->assertFalse( $this->object->hasChildren() );
	}
}
