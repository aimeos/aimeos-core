<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 1365 2012-10-31 13:54:32Z doleiynyk $
 */


/**
 * Test class for MShop_Product_Manager_Site_Default.
 */
class MShop_Product_Manager_Site_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * @var MShop_Product_Manager_Site_Default
	 */
	protected $_object;

	/**
	 * @var string
	 * @access protected
	 */
	protected $_editor = '';

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Product_Manager_Site_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}



	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$this->_object = $manager->getSubManager( 'site' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Common_Item_Site_Interface', $this->_object->createItem() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $manager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'U:WH' ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $manager->searchItems( $search );

		if( ( $abcdItem = reset( $items ) ) === false ) {
			throw new Exception( 'No product item found with code ABCD' );
		}

		$search = $this->_object->createSearch();
		$results = $this->_object->searchItems($search);

		if( ( $item = reset($results) ) === false ) {
			throw new Exception( 'No site item found' );
		}

		$item->setId(null);
		$item->setValue( 1 );
		$item->setParentId( $abcdItem->getId() );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setValue( 0 );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.site.value', 0 ),
			$search->compare( '==', 'product.site.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->_object->searchItems( $search );

		if( ($expected = reset($result)) === false ) {
			throw new Exception( sprintf( 'No site item found for value "0"' ) );
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

		$expr[] = $search->compare( '!=', 'product.site.id', null );
		$expr[] = $search->compare( '!=', 'product.site.parentid', null );
		$expr[] = $search->compare( '!=', 'product.site.siteid', null );
		$expr[] = $search->compare( '==', 'product.site.value', 0 );
		$expr[] = $search->compare( '>=', 'product.site.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.site.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.site.editor', $this->_editor );

		$search->setConditions( $search->combine('&&', $expr) );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 7, count( $results ) );


		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '>=', 'product.site.value', 0 ),
			$search->compare( '==', 'product.site.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 2 );
		$items = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 2, count( $items ) );
		$this->assertEquals( 20, $total );

		foreach($items as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}

}