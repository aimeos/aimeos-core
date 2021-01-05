<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MShop\Locale\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = \Aimeos\MShop\Locale\Manager\Factory::create( \TestHelperMShop::getContext() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testBootstrapMatch()
	{
		$item = $this->object->bootstrap( 'unittest', 'en', 'EUR', false );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $item );
		$this->assertEquals( 'en', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertEquals( 1, count( $item->getSites() ) );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $item->getSiteItem() );
		$this->assertEquals( 'unittest', $item->getSiteItem()->getCode() );
	}


	public function testBootstrapMatchNoCurrency()
	{
		$item = $this->object->bootstrap( 'unittest', 'en', '', false );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $item );
		$this->assertEquals( 'en', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertEquals( 1, count( $item->getSites() ) );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $item->getSiteItem() );
		$this->assertEquals( 'unittest', $item->getSiteItem()->getCode() );
	}


	public function testBootstrapMatchSiteOnly()
	{
		$item = $this->object->bootstrap( 'unittest', '', '', false );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $item );
		$this->assertEquals( 'de', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertEquals( 1, count( $item->getSites() ) );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $item->getSiteItem() );
		$this->assertEquals( 'unittest', $item->getSiteItem()->getCode() );
	}


	public function testBootstrapNoMatch()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->bootstrap( '', '', '', true );
	}


	public function testBootstrapClosest()
	{
		$item = $this->object->bootstrap( 'unittest', 'en', 'USD', false );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $item );
		$this->assertEquals( 'en', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $item->getSiteItem() );
		$this->assertEquals( 'unittest', $item->getSiteItem()->getCode() );
		$this->assertEquals( 1, count( $item->getSites() ) );
	}


	public function testBootstrapClosestLangid()
	{
		$item = $this->object->bootstrap( 'unittest', 'de', 'CHF', false );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $item );
		$this->assertEquals( 'de', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $item->getSiteItem() );
		$this->assertEquals( 'unittest', $item->getSiteItem()->getCode() );
		$this->assertEquals( 1, count( $item->getSites() ) );
	}


	public function testBootstrapClosestLangidWithCountry()
	{
		$item = $this->object->bootstrap( 'unittest', 'de_DE', 'CHF', false );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $item );
		$this->assertEquals( 'de', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $item->getSiteItem() );
		$this->assertEquals( 'unittest', $item->getSiteItem()->getCode() );
		$this->assertEquals( 1, count( $item->getSites() ) );
	}


	public function testBootstrapClosestSiteid()
	{
		$item = $this->object->bootstrap( 'unittest', 'it', 'CHF', false );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $item );
		$this->assertEquals( 'de', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Site\Iface::class, $item->getSiteItem() );
		$this->assertEquals( 'unittest', $item->getSiteItem()->getCode() );
		$this->assertEquals( 1, count( $item->getSites() ) );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Iface::class, $this->object->create() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter() );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter( true ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'site' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'site', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'language' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'language', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'currency' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'currency', 'Standard' ) );
	}


	public function testGetSubManagerInvalidType()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'site', 'unknown' );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );
		$items = $this->object->search( $search )->toArray();

		if( ( $tmpItem = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No locale item found for code: "unit"' );
		}

		$item = $this->object->get( $tmpItem->getId() );

		$this->assertEquals( $tmpItem->getId(), $item->getId() );
		$this->assertEquals( $tmpItem->getSiteId(), $item->getSiteId() );
		$this->assertEquals( 'de', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertEquals( 0, $item->getPosition() );
	}


	public function testSearchItems()
	{
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'locale.id', null );
		$expr[] = $search->compare( '!=', 'locale.siteid', null );
		$expr[] = $search->compare( '==', 'locale.languageid', 'de' );
		$expr[] = $search->compare( '==', 'locale.currencyid', 'EUR' );
		$expr[] = $search->compare( '==', 'locale.position', 0 );
		$expr[] = $search->compare( '==', 'locale.status', 0 );
		$expr[] = $search->compare( '>=', 'locale.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.editor', '' );

		$expr[] = $search->compare( '!=', 'locale.site.id', null );
		$expr[] = $search->compare( '==', 'locale.site.code', 'unittest' );
		$expr[] = $search->compare( '==', 'locale.site.label', 'Unit test site' );
		$expr[] = $search->compare( '~=', 'locale.site.config', '{' );
		$expr[] = $search->compare( '==', 'locale.site.status', 1 );
		$expr[] = $search->compare( '>=', 'locale.site.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.site.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.site.editor', '' );

		$expr[] = $search->compare( '==', 'locale.language.id', 'de' );

		$expr[] = $search->compare( '>=', 'locale.language.label', 'German' );
		$expr[] = $search->compare( '==', 'locale.language.code', 'de' );
		$expr[] = $search->compare( '==', 'locale.language.status', 1 );
		$expr[] = $search->compare( '>=', 'locale.language.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.language.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.language.editor', '' );

		$expr[] = $search->compare( '==', 'locale.currency.id', 'EUR' );

		$expr[] = $search->compare( '==', 'locale.currency.label', 'Euro' );
		$expr[] = $search->compare( '==', 'locale.currency.code', 'EUR' );
		$expr[] = $search->compare( '==', 'locale.currency.status', 1 );
		$expr[] = $search->compare( '>=', 'locale.currency.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.currency.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.currency.editor', '' );

		$total = 0;
		$search->setConditions( $search->and( $expr ) );
		$search->slice( 0, 1 );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );
		$items = $this->object->search( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No locale item found for code: "unit"' );
		}

		$item->setId( null );
		$item->setLanguageId( 'es' );
		$item->setCurrencyId( 'USD' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLanguageId( 'it' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $item->getId() );

		$context = \TestHelperMShop::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getCurrencyId(), $itemSaved->getCurrencyId() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getCurrencyId(), $itemUpd->getCurrencyId() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $item->getId() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'locale', $result );
		$this->assertContains( 'locale/currency', $result );
		$this->assertContains( 'locale/language', $result );
		$this->assertContains( 'locale/site', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}

}
