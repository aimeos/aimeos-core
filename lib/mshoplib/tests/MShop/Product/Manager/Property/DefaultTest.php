<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 */


/**
 * Test class for MShop_Product_Manager_Property_Default.
 */
class MShop_Product_Manager_Property_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$this->_object = new MShop_Product_Manager_Property_Default( TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$item = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Product_Item_Property_Interface', $item );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.property.editor', $this->_editor ) );
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset($results) ) === false ) {
			throw new Exception( 'No property item found' );
		}

		$item->setId(null);
		$item->setLanguageId( 'en' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setValue( 'unittest' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'product.property.value', '25.0'),
			$search->compare( '==', 'product.property.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems( $search );

		if( ($expected = reset($results)) === false ) {
			throw new Exception( sprintf( 'No product property item found for value "%1$s".', '25.0' ) );
		}

		$actual = $this->_object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'product.property.id', null );
		$expr[] = $search->compare( '!=', 'product.property.parentid', null );
		$expr[] = $search->compare( '!=', 'product.property.siteid', null );
		$expr[] = $search->compare( '!=', 'product.property.typeid', null );
		$expr[] = $search->compare( '==', 'product.property.languageid', null );
		$expr[] = $search->compare( '==', 'product.property.value', '25.00' );
		$expr[] = $search->compare( '==', 'product.property.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'product.property.type.id', null );
		$expr[] = $search->compare( '!=', 'product.property.type.siteid', null );
		$expr[] = $search->compare( '==', 'product.property.type.domain', 'product/property' );
		$expr[] = $search->compare( '==', 'product.property.type.code', 'package-length' );
		$expr[] = $search->compare( '>', 'product.property.type.label', '' );
		$expr[] = $search->compare( '==', 'product.property.type.status', 1 );
		$expr[] = $search->compare( '>=', 'product.property.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.property.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.property.type.editor', $this->_editor );

		$search->setConditions( $search->combine('&&', $expr) );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );


		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '=~', 'product.property.type.code', 'package-' ),
			$search->compare( '==', 'product.property.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice(0, 1);
		$items = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 6, $total );

		foreach($items as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type', 'Default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('type', 'unknown');
	}
}
