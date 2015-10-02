<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Locale_Manager_Default.
 */
class MShop_Locale_Manager_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = TestHelper::getContext()->getEditor();
		$this->object = MShop_Locale_Manager_Factory::createManager( TestHelper::getContext() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testBootstrap()
	{
		$item = $this->object->bootstrap( 'unittest', 'de', 'EUR', false );
		$this->assertInstanceOf( 'MShop_Locale_Item_Interface', $item );
		$this->assertEquals( 'de', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertInstanceOf( 'MShop_Locale_Item_Site_Interface', $item->getSite() );
		$this->assertEquals( 'unittest', $item->getSite()->getCode() );
		$this->assertEquals( 1, count( $item->getSitePath() ) );

		$item = $this->object->bootstrap( 'unittest', 'de', '', false );
		$this->assertInstanceOf( 'MShop_Locale_Item_Interface', $item );
		$this->assertEquals( 'de', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertInstanceOf( 'MShop_Locale_Item_Site_Interface', $item->getSite() );
		$this->assertEquals( 'unittest', $item->getSite()->getCode() );
		$this->assertEquals( 1, count( $item->getSitePath() ) );

		$item = $this->object->bootstrap( 'unittest', '', '', false );
		$this->assertInstanceOf( 'MShop_Locale_Item_Interface', $item );
		$this->assertEquals( 'de', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertInstanceOf( 'MShop_Locale_Item_Site_Interface', $item->getSite() );
		$this->assertEquals( 'unittest', $item->getSite()->getCode() );
		$this->assertEquals( 1, count( $item->getSitePath() ) );

		$this->setExpectedException( 'MShop_Locale_Exception' );
		$this->object->bootstrap( '', '', '', true );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Locale_Item_Interface', $this->object->createItem() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'site' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'site', 'Default' ) );

		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'language' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'language', 'Default' ) );

		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'currency' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'currency', 'Default' ) );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'site', 'unknown' );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );
		$items = $this->object->searchItems( $search );

		if( ( $tmpItem = reset( $items ) ) === false ) {
			throw new Exception( 'No locale item found for code: "unit"' );
		}

		$item = $this->object->getItem( $tmpItem->getId() );

		$this->assertEquals( $tmpItem->getId(), $item->getId() );
		$this->assertEquals( $tmpItem->getSiteId(), $item->getSiteId() );
		$this->assertEquals( 'de', $item->getLanguageId() );
		$this->assertEquals( 'EUR', $item->getCurrencyId() );
		$this->assertEquals( 0, $item->getPosition() );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();

		$expr = array();
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
		$expr[] = $search->compare( '==', 'locale.site.status', 0 );
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
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );
		$results = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No locale item found for code: "unit"' );
		}

		$item->setId( null );
		$item->setLanguageId( 'es' );
		$item->setCurrencyId( 'USD' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLanguageId( 'it' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $item->getId() );

		$context = TestHelper::getContext();

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

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getItem( $item->getId() );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}

}
