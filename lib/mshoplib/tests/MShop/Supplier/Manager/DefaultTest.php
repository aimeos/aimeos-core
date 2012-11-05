<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14682 2012-01-04 11:30:14Z nsendetzky $
 */

class MShop_Supplier_Manager_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object = null;

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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Supplier_Manager_DefaultTest');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$this->_object = new MShop_Supplier_Manager_Default(TestHelper::getContext());
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset($this->_object);
	}

	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute )
		{
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}

	public function testCreateItem()
	{
		$item = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Supplier_Item_Interface', $item );
	}

	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'supplier.label', 'unitSupplier' ),
			$search->compare( '==', 'supplier.editor', $this->_editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No supplier item with label "unitSupplier" found' );
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId() ) );
	}

	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'supplier.label', 'unitSupplier001' ),
			$search->compare( '==', 'supplier.editor', $this->_editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No supplier item found' );
		}

		$item->setId( null );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'unitTest' );
		$itemExp->setStatus( 2 );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}

	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch() );
	}


	public function testSearchItem()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'supplier.id', null );
		$expr[] = $search->compare( '!=', 'supplier.siteid', null );
		$expr[] = $search->compare( '==', 'supplier.label', 'unitSupplier001' );
		$expr[] = $search->compare( '==', 'supplier.code', 'unitCode001' );
		$expr[] = $search->compare( '==', 'supplier.status', 1 );
		$expr[] = $search->compare( '>=', 'supplier.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'supplier.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'supplier.address.id', null );
		$expr[] = $search->compare( '!=', 'supplier.address.siteid', null );
		$expr[] = $search->compare( '!=', 'supplier.address.refid', '' );
		$expr[] = $search->compare( '==', 'supplier.address.company', 'Metaways' );
		$expr[] = $search->compare( '==', 'supplier.address.salutation', 'mrs' );
		$expr[] = $search->compare( '==', 'supplier.address.title', '' );
		$expr[] = $search->compare( '==', 'supplier.address.firstname', 'Our' );
		$expr[] = $search->compare( '==', 'supplier.address.lastname', 'Unittest' );
		$expr[] = $search->compare( '==', 'supplier.address.address1', 'Pickhuben' );
		$expr[] = $search->compare( '==', 'supplier.address.address2', '2' );
		$expr[] = $search->compare( '==', 'supplier.address.address3', '' );
		$expr[] = $search->compare( '==', 'supplier.address.postal', '20457' );
		$expr[] = $search->compare( '==', 'supplier.address.city', 'Hamburg' );
		$expr[] = $search->compare( '==', 'supplier.address.languageid', 'de' );
		$expr[] = $search->compare( '==', 'supplier.address.countryid', 'de' );
		$expr[] = $search->compare( '==', 'supplier.address.telephone', '055544332211' );
		$expr[] = $search->compare( '==', 'supplier.address.telefax', '055544332212' );
		$expr[] = $search->compare( '==', 'supplier.address.email', 'eshop@metaways.de' );
		$expr[] = $search->compare( '==', 'supplier.address.website', 'www.metaways.de' );
		$expr[] = $search->compare( '==', 'supplier.address.flag', 0 );
		$expr[] = $search->compare( '==', 'supplier.address.position', 0 );
		$expr[] = $search->compare( '>=', 'supplier.address.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'supplier.address.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.address.editor', $this->_editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $result ) );


		//search without base criteria
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'supplier.editor', $this->_editor ) );
		$search->setSlice(0, 2);
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 2, count( $results ) );
		$this->assertEquals( 3, $total );

		//search with base criteria
		$search = $this->_object->createSearch(true);
		$conditions = array(
			$search->compare( '==', 'supplier.editor', $this->_editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems( $search );
		$this->assertEquals( 2, count( $results ) );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('address') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('address', 'Default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('address', 'unknown');
	}
}