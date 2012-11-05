<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 1268 2012-10-08 17:14:29Z fblasel $
 */


/**
 * Test class for MShop_Catalog_Manager_Site_Default.
 */
class MShop_Catalog_Manager_Site_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;

	/**
	 * @var string
	 * @access protected
	 */
	protected $_editor = '';

	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Catalog_Manager_Site_DefaultTest');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$this->_object = $catalogManager->getSubManager('site');
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCreateItem()
	{
		$item = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Common_Item_Site_Interface', $item );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No site item found' );
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId() ) );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testSaveUpdateDeleteItem()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $catalogManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'catalog.label', 'Internet' ),
			$search->compare( '==', 'catalog.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $catalogManager->searchItems( $search );

		if( ( $inetItem = reset( $items ) ) === false ) {
			throw new Exception( 'No catalog item found with label Internet' );
		}

		$search = $this->_object->createSearch();
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No site item found' );
		}

		$item->setId( null );
		$item->setValue( 0 );
		$item->setParentId( $inetItem->getId() );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setValue( 1 );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemExp->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testSearchItems()
	{
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'catalog.site.id', null );
		$expr[] = $search->compare( '!=', 'catalog.site.parentid', null );
		$expr[] = $search->compare( '!=', 'catalog.site.siteid', null );
		$expr[] = $search->compare( '==', 'catalog.site.value', 0 );
		$expr[] = $search->compare( '>=', 'catalog.site.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.site.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.site.editor', $this->_editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 3, $total );

		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.site.editor', $this->_editor ) );
		$results = $this->_object->searchItems( $search );
		$this->assertEquals( 5, count( $results ) );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}

}