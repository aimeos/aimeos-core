<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Price\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = \Aimeos\MShop\Price\Manager\Factory::create( \TestHelperMShop::getContext() );
	}

	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->deleteItems( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'price', $result );
		$this->assertContains( 'price/lists', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $object ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $object );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $this->object->createItem() );
	}


	public function testCreateItemType()
	{
		$item = $this->object->createItem( ['price.type' => 'default'] );
		$this->assertEquals( 'default', $item->getType() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'price.value', 12.00 ),
			$search->compare( '==', 'price.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No results available' );
		}

		$itemB = $this->object->getItem( $item->getId() );
		$this->assertEquals( 19.00, $itemB->getTaxRate() );
	}


	public function testGetSavePropertyItems()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'price.label', 'product/default/600.00/30.00' ) );
		$items = $this->object->searchItems( $search, ['price/property'] )->toArray();
		$item = reset( $items );

		$item->setId( null )->setLabel( 'core:property-test' );
		$this->object->saveItem( $item );

		$search->setConditions( $search->compare( '==', 'price.label', 'core:property-test' ) );
		$items = $this->object->searchItems( $search, ['price/property'] )->toArray();
		$item2 = reset( $items );

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 1, count( $item->getPropertyItems() ) );
		$this->assertEquals( 1, count( $item2->getPropertyItems() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'price.editor', $this->editor ) );
		$items = $this->object->searchItems( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item->setId( null );
		$item->setLabel( 'price label' );
		$resultSaved = $this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest' );
		$resultUpd = $this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getCurrencyId(), $itemSaved->getCurrencyId() );
		$this->assertEquals( $item->getQuantity(), $itemSaved->getQuantity() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );
		$this->assertEquals( $item->getCosts(), $itemSaved->getCosts() );
		$this->assertEquals( $item->getRebate(), $itemSaved->getRebate() );
		$this->assertEquals( $item->getTaxRate(), $itemSaved->getTaxRate() );
		$this->assertEquals( $item->getTaxRates(), $itemSaved->getTaxRates() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getCurrencyId(), $itemUpd->getCurrencyId() );
		$this->assertEquals( $itemExp->getQuantity(), $itemUpd->getQuantity() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );
		$this->assertEquals( $itemExp->getCosts(), $itemUpd->getCosts() );
		$this->assertEquals( $itemExp->getRebate(), $itemUpd->getRebate() );
		$this->assertEquals( $itemExp->getTaxRate(), $itemUpd->getTaxRate() );
		$this->assertEquals( $itemExp->getTaxRates(), $itemUpd->getTaxRates() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\SQL::class, $this->object->createSearch() );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'price.value', '99.99' ) );
		$item = $this->object->searchItems( $search, ['customer'] )->first();

		if( $item && ( $listItem = $item->getListItems( 'customer', 'test' )->first() ) === null ) {
			throw new \RuntimeException( 'No list item found' );
		}

		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'price.id', null );
		$expr[] = $search->compare( '!=', 'price.siteid', null );
		$expr[] = $search->compare( '==', 'price.type', 'default' );
		$expr[] = $search->compare( '==', 'price.domain', 'attribute' );
		$expr[] = $search->compare( '>=', 'price.label', '' );
		$expr[] = $search->compare( '==', 'price.currencyid', 'EUR' );
		$expr[] = $search->compare( '==', 'price.quantity', 1 );
		$expr[] = $search->compare( '==', 'price.value', '99.99' );
		$expr[] = $search->compare( '==', 'price.costs', '9.99' );
		$expr[] = $search->compare( '==', 'price.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'price.taxrate', '{' );
		$expr[] = $search->compare( '==', 'price.status', 1 );
		$expr[] = $search->compare( '>=', 'price.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'price.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'price.editor', $this->editor );

		$param = ['customer', 'test', $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->createFunction( 'price:has', $param ), null );

		$param = ['customer', 'test'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'price:has', $param ), null );

		$param = ['customer'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'price:has', $param ), null );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchItemsProperty()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'price.id', null );
		$expr[] = $search->compare( '!=', 'price.siteid', null );
		$expr[] = $search->compare( '==', 'price.type', 'default' );
		$expr[] = $search->compare( '==', 'price.domain', 'product' );
		$expr[] = $search->compare( '>=', 'price.label', '' );
		$expr[] = $search->compare( '==', 'price.currencyid', 'EUR' );
		$expr[] = $search->compare( '==', 'price.quantity', 1 );
		$expr[] = $search->compare( '==', 'price.value', '600.00' );
		$expr[] = $search->compare( '==', 'price.costs', '30.00' );
		$expr[] = $search->compare( '==', 'price.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'price.taxrate', '{' );
		$expr[] = $search->compare( '==', 'price.status', 1 );
		$expr[] = $search->compare( '>=', 'price.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'price.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'price.editor', $this->editor );

		$param = ['zone', null, 'NY'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'price:prop', $param ), null );

		$param = ['zone', null];
		$expr[] = $search->compare( '!=', $search->createFunction( 'price:prop', $param ), null );

		$param = ['zone'];
		$expr[] = $search->compare( '!=', $search->createFunction( 'price:prop', $param ), null );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchItemsTotal()
	{
		$total = 0;
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'price.editor', $this->editor ) );
		$search->setSlice( 0, 10 );
		$results = $this->object->searchItems( $search, [], $total )->toArray();
		$this->assertEquals( 10, count( $results ) );
		$this->assertEquals( 29, $total );
	}


	public function testSearchItemsBase()
	{
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'price.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search )->toArray();
		$this->assertEquals( 27, count( $results ) );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'type', 'Standard' ) );
	}


	public function testGetLowestPrice()
	{
		$item = $this->object->createItem();
		$item->setValue( '1.00' );

		$lowest = $this->object->getLowestPrice( map( [$item] ), 1 );

		$this->assertEquals( $item, $lowest );
	}


	public function testGetLowestPriceQuantity()
	{
		$item = $this->object->createItem();
		$item->setValue( '10.00' );

		$item2 = $this->object->createItem();
		$item2->setValue( '5.00' );
		$item2->setQuantity( 5 );

		$lowest = $this->object->getLowestPrice( map( [$item, $item2] ), 10 );

		$this->assertEquals( $item2, $lowest );
	}


	public function testGetLowestPriceCurrency()
	{
		$item = $this->object->createItem();
		$item->setValue( '1.00' );

		$this->expectException( \Aimeos\MShop\Price\Exception::class );
		$this->object->getLowestPrice( map( [$item] ), 1, 'USD' );
	}


	public function testGetLowestPriceNoPrice()
	{
		$this->expectException( \Aimeos\MShop\Price\Exception::class );
		$this->object->getLowestPrice( map(), 1 );
	}


	public function testGetLowestPriceNoPriceForQuantity()
	{
		$item = $this->object->createItem();
		$item->setValue( '1.00' );
		$item->setQuantity( 5 );

		$this->expectException( \Aimeos\MShop\Price\Exception::class );
		$this->object->getLowestPrice( map( [$item] ), 1 );
	}
}
