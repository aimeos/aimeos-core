<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Product_Manager_Tag_Default.
 */
class MShop_Product_Manager_Tag_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->editor = TestHelper::getContext()->getEditor();
		$this->object = new MShop_Product_Manager_Tag_Default( TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( 'MShop_Product_Item_Tag_Iface', $item );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.tag.editor', $this->editor ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No tag item found' );
		}

		$item->setId( null );
		$item->setLanguageId( 'en' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'unittest' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'product.tag.label', 'herb' ),
			$search->compare( '==', 'product.tag.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( sprintf( 'No product tag item found for label "%1$s".', 'herb' ) );
		}

		$actual = $this->object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Iface', $attribute );
		}
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'product.tag.id', null );
		$expr[] = $search->compare( '!=', 'product.tag.siteid', null );
		$expr[] = $search->compare( '!=', 'product.tag.typeid', null );
		$expr[] = $search->compare( '==', 'product.tag.languageid', 'de' );
		$expr[] = $search->compare( '==', 'product.tag.label', 'Kaffee' );
		$expr[] = $search->compare( '==', 'product.tag.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'product.tag.type.id', null );
		$expr[] = $search->compare( '!=', 'product.tag.type.siteid', null );
		$expr[] = $search->compare( '==', 'product.tag.type.domain', 'product/tag' );
		$expr[] = $search->compare( '==', 'product.tag.type.code', 'sort' );
		$expr[] = $search->compare( '>', 'product.tag.type.label', '' );
		$expr[] = $search->compare( '==', 'product.tag.type.status', 1 );
		$expr[] = $search->compare( '>=', 'product.tag.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.tag.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.tag.type.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );


		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'product.tag.type.code', 'taste' ),
			$search->compare( '~=', 'product.tag.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );
		$items = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 3, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'type', 'Default' ) );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'type', 'unknown' );
	}
}
