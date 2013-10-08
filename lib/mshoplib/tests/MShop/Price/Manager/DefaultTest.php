<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Price_Manager_Default.
 */
class MShop_Price_Manager_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;

	/**
	 * @var string
	 * @access protected
	 */
	private $_editor = '';

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Price_Manager_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$this->_object = MShop_Price_Manager_Factory::createManager( TestHelper::getContext() );
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

	public function testGetSearchAttributes()
	{
		foreach($this->_object->getSearchAttributes() as $object) {
			$this->assertInstanceOf('MW_Common_Criteria_Attribute_Interface', $object );
		}
	}

	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Price_Item_Interface', $this->_object->createItem() );
	}

	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'price.value', 12.00 ),
			$search->compare( '==', 'price.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems( $search );

		if ( ( $item = reset($results) ) === false) {
			throw new Exception( 'No results available' );
		}

		$itemB = $this->_object->getItem( $item->getId() );

		$this->assertEquals( 19.00, $itemB->getTaxRate() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'price.editor', $this->_editor ) );
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item->setId(null);
		$item->setLabel( 'price label' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getCurrencyId(), $itemSaved->getCurrencyId() );
		$this->assertEquals( $item->getQuantity(), $itemSaved->getQuantity() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );
		$this->assertEquals( $item->getCosts(), $itemSaved->getCosts() );
		$this->assertEquals( $item->getRebate(), $itemSaved->getRebate() );
		$this->assertEquals( $item->getTaxRate(), $itemSaved->getTaxRate() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getCurrencyId(), $itemUpd->getCurrencyId() );
		$this->assertEquals( $itemExp->getQuantity(), $itemUpd->getQuantity() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );
		$this->assertEquals( $itemExp->getCosts(), $itemUpd->getCosts() );
		$this->assertEquals( $itemExp->getRebate(), $itemUpd->getRebate() );
		$this->assertEquals( $itemExp->getTaxRate(), $itemUpd->getTaxRate() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf('MW_Common_Criteria_SQL', $this->_object->createSearch());
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'price.id', null );
		$expr[] = $search->compare( '!=', 'price.siteid', null );
		$expr[] = $search->compare( '!=', 'price.typeid', null );
		$expr[] = $search->compare( '==', 'price.domain', 'product' );
		$expr[] = $search->compare( '>=', 'price.label', '' );
		$expr[] = $search->compare( '==', 'price.currencyid', 'EUR' );
		$expr[] = $search->compare( '==', 'price.quantity', 100 );
		$expr[] = $search->compare( '==', 'price.value', '580.00' );
		$expr[] = $search->compare( '==', 'price.shipping', '20.00' );
		$expr[] = $search->compare( '==', 'price.rebate', '0.00' );
		$expr[] = $search->compare( '==', 'price.taxrate', '19.00' );
		$expr[] = $search->compare( '==', 'price.status', 1 );
		$expr[] = $search->compare( '>=', 'price.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'price.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'price.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'price.type.id', null );
		$expr[] = $search->compare( '!=', 'price.type.siteid', null );
		$expr[] = $search->compare( '==', 'price.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'price.type.code', 'default' );
		$expr[] = $search->compare( '==', 'price.type.label', 'Default' );
		$expr[] = $search->compare( '==', 'price.type.status', 1 );
		$expr[] = $search->compare( '>=', 'price.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'price.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'price.type.editor', $this->_editor );

		$search->setConditions( $search->combine('&&', $expr) );
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );


		//search without base criteria
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'price.editor', $this->_editor ) );
		$search->setSlice(0, 10);
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 10, count( $results ) );
		$this->assertEquals( 23, $total );

		//search with base criteria
		$search = $this->_object->createSearch(true);
		$conditions = array(
			$search->compare( '==', 'price.editor', $this->_editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems( $search );
		$this->assertEquals( 21, count( $results ) );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type', 'Default') );
	}


	public function testGetLowestPrice()
	{
		$item = $this->_object->createItem();
		$item->setValue( '1.00' );

		$lowest = $this->_object->getLowestPrice( array( $item ), 1 );

		$this->assertEquals( $item, $lowest );
	}


	public function testGetLowestPriceQuantity()
	{
		$item = $this->_object->createItem();
		$item->setValue( '10.00' );

		$item2 = $this->_object->createItem();
		$item2->setValue( '5.00' );
		$item2->setQuantity( 5 );

		$lowest = $this->_object->getLowestPrice( array( $item, $item2 ), 10 );

		$this->assertEquals( $item2, $lowest );
	}


	public function testGetLowestPriceNoPrice()
	{
		$this->setExpectedException( 'MShop_Price_Exception' );
		$lowest = $this->_object->getLowestPrice( array(), 1 );
	}


	public function testGetLowestPriceNoPriceForQuantity()
	{
		$item = $this->_object->createItem();
		$item->setValue( '1.00' );
		$item->setQuantity( 5 );

		$this->setExpectedException( 'MShop_Price_Exception' );
		$lowest = $this->_object->getLowestPrice( array( $item ), 1 );
	}


	public function testGetLowestPriceWrongItem()
	{
		$this->setExpectedException( 'MShop_Price_Exception' );
		$lowest = $this->_object->getLowestPrice( array( new stdClass() ), 1 );
	}
}