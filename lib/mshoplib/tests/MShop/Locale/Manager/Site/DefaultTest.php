<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Locale_Manager_Site_Default.
 */
class MShop_Locale_Manager_Site_DefaultTest extends MW_Unittest_Testcase
{
	private $_context;
	private $_object;


	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_object = new MShop_Locale_Manager_Site_Default($this->_context);
	}


	protected function tearDown()
	{
		$this->_object = null;
	}


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('MShop_Locale_Manager_Site_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf('MShop_Locale_Item_Site_Interface', $this->_object->createItem());
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->_object->createItem();
		$item->setLabel( 'new name' );
		$item->setStatus( true );
		$item->setCode( 'xx' );
		$this->_object->insertItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel('new new name' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $item->getId() );

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getChildren(), $itemSaved->getChildren() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getChildren(), $itemUpd->getChildren() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $item->getId() );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions($search->compare('~=', 'locale.site.code', 'unittest'));

		$a = $this->_object->searchItems($search);
		if ( ($expected = reset($a) ) === false ) {
			throw new Exception('Site item not found');
		}

		$actual = $this->_object->getItem($expected->getId());

		$this->assertInstanceOf('MShop_Locale_Item_Site_Interface', $actual);
		$this->assertEquals($expected, $actual);
	}


	public function testSearchItems()
	{
		$siteid = $this->_context->getLocale()->getSiteId();

		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'locale.site.id', null );
		$expr[] = $search->compare( '==', 'locale.site.siteid', $siteid );
		$expr[] = $search->compare( '==', 'locale.site.code', 'unittest' );
		$expr[] = $search->compare( '==', 'locale.site.label', 'Unit test site' );
		$expr[] = $search->compare( '~=', 'locale.site.config', '{' );
		$expr[] = $search->compare( '==', 'locale.site.status', 0 );
		$expr[] = $search->compare( '>=', 'locale.site.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.site.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'locale.site.editor', '' );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		//search with base criteria and total
		$search = $this->_object->createSearch(true);
		$search->setConditions( $search->compare( '==', 'locale.site.code', array( 'unittest' ) ) );
		$search->setSlice( 0, 1 );

		$results = $this->_object->searchItems($search, array(), $total);
		$this->assertEquals(1, count( $results ));
		$this->assertGreaterThanOrEqual(1, $total);

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSearchAttributes()
	{
		foreach ( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf('MW_Common_Criteria_Attribute_Interface', $attribute);
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testGetPath()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$results = $this->_object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$list = $this->_object->getPath( $expected->getId() );
		$this->assertArrayHasKey( $expected->getId(), $list );
		$this->assertEquals( $expected, $list[$expected->getId()] );
	}


	public function testGetTree()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$results = $this->_object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item = $this->_object->getTree( $expected->getId() );
		$this->assertEquals( $expected, $item );
	}


	public function testGetTreeCache()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'unittest' ) );

		$results = $this->_object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item = $this->_object->getTree( $expected->getId() );
		$item2 = $this->_object->getTree( $expected->getId() );

		$this->assertSame( $item, $item2 );
	}


	public function testGetTreeDefault()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', 'default' ) );

		$results = $this->_object->searchItems( $search );

		if( ( $expected = reset( $results ) ) !== false )
		{
			$item = $this->_object->getTree();
			$this->assertEquals( $expected, $item );
		}
	}


	public function testMoveItem()
	{
		$this->setExpectedException('MShop_Locale_Exception');
		$this->_object->moveItem( null, null, null );
	}
}
