<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Price\Manager;


/**
 * Test class for \Aimeos\MShop\Price\Manager\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = \Aimeos\MShop\Price\Manager\Factory::createManager( \TestHelperMShop::getContext() );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'price', $result );
		$this->assertContains( 'price/type', $result );
		$this->assertContains( 'price/lists', $result );
		$this->assertContains( 'price/lists/type', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $object ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $object );
		}
	}

	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Price\\Item\\Iface', $this->object->createItem() );
	}

	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'price.value', 12.00 ),
			$search->compare( '==', 'price.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No results available' );
		}

		$itemB = $this->object->getItem( $item->getId() );
		$this->assertEquals( 19.00, $itemB->getTaxRate() );
		$this->assertNotEquals( '', $itemB->getTypeName() );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Price\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'price.editor', $this->editor ) );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item->setId( null );
		$item->setLabel( 'price label' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


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

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
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

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\SQL', $this->object->createSearch() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'price.id', null );
		$expr[] = $search->compare( '!=', 'price.siteid', null );
		$expr[] = $search->compare( '!=', 'price.typeid', null );
		$expr[] = $search->compare( '==', 'price.domain', 'product' );
		$expr[] = $search->compare( '>=', 'price.label', '' );
		$expr[] = $search->compare( '==', 'price.currencyid', 'EUR' );
		$expr[] = $search->compare( '==', 'price.quantity', 100 );
		$expr[] = $search->compare( '==', 'price.value', '580.00' );
		$expr[] = $search->compare( '==', 'price.costs', '20.00' );
		$expr[] = $search->compare( '==', 'price.rebate', '0.00' );
		$expr[] = $search->compare( '==', 'price.taxrate', '19.00' );
		$expr[] = $search->compare( '==', 'price.status', 1 );
		$expr[] = $search->compare( '>=', 'price.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'price.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'price.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'price.type.id', null );
		$expr[] = $search->compare( '!=', 'price.type.siteid', null );
		$expr[] = $search->compare( '==', 'price.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'price.type.code', 'default' );
		$expr[] = $search->compare( '==', 'price.type.label', 'Standard' );
		$expr[] = $search->compare( '==', 'price.type.status', 1 );
		$expr[] = $search->compare( '>=', 'price.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'price.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'price.type.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );


		//search without base criteria
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'price.editor', $this->editor ) );
		$search->setSlice( 0, 10 );
		$results = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 10, count( $results ) );
		$this->assertEquals( 23, $total );

		//search with base criteria
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'price.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );
		$this->assertEquals( 21, count( $results ) );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type', 'Standard' ) );
	}


	public function testGetLowestPrice()
	{
		$item = $this->object->createItem();
		$item->setValue( '1.00' );

		$lowest = $this->object->getLowestPrice( array( $item ), 1 );

		$this->assertEquals( $item, $lowest );
	}


	public function testGetLowestPriceQuantity()
	{
		$item = $this->object->createItem();
		$item->setValue( '10.00' );

		$item2 = $this->object->createItem();
		$item2->setValue( '5.00' );
		$item2->setQuantity( 5 );

		$lowest = $this->object->getLowestPrice( array( $item, $item2 ), 10 );

		$this->assertEquals( $item2, $lowest );
	}


	public function testGetLowestPriceCurrency()
	{
		$item = $this->object->createItem();
		$item->setValue( '1.00' );

		$this->setExpectedException( '\\Aimeos\\MShop\\Price\\Exception' );
		$this->object->getLowestPrice( array( $item ), 1, 'USD' );
	}


	public function testGetLowestPriceNoPrice()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Price\\Exception' );
		$this->object->getLowestPrice( [], 1 );
	}


	public function testGetLowestPriceNoPriceForQuantity()
	{
		$item = $this->object->createItem();
		$item->setValue( '1.00' );
		$item->setQuantity( 5 );

		$this->setExpectedException( '\\Aimeos\\MShop\\Price\\Exception' );
		$this->object->getLowestPrice( array( $item ), 1 );
	}


	public function testGetLowestPriceWrongItem()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Price\\Exception' );
		$this->object->getLowestPrice( array( new \stdClass() ), 1 );
	}
}
