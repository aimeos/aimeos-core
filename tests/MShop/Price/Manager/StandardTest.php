<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Price\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\MShop\Price\Manager\Standard( \TestHelper::context() );
		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Lists( $this->object, $this->context );
		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Property( $this->object, $this->context );
		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Type( $this->object, $this->context );
		$this->object->setObject( $this->object );
	}

	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDelete()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
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
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $object );
		}
	}


	public function testCreate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Price\Item\Iface::class, $this->object->create() );
	}


	public function testCreateType()
	{
		$item = $this->object->create( ['price.type' => 'default'] );
		$this->assertEquals( 'default', $item->getType() );
	}


	public function testGet()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->add( $search->and( array(
			$search->compare( '==', 'price.value', 12.00 ),
			$search->compare( '==', 'price.editor', $this->context->editor() )
		) ) );

		$item = $this->object->search( $search, ['price/type'] )->first( new \RuntimeException( 'No results available' ) );

		$itemB = $this->object->get( $item->getId() );
		$this->assertEquals( 19.00, $itemB->getTaxRate() );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $item->getTypeItem() );
	}


	public function testGetSavePropertyItems()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'price.label', 'product/default/600.00/30.00' ) );
		$item = $this->object->search( $search, ['price/property', 'price/property/type'] )->first();

		$item->setId( null )->setLabel( 'core:property-test' );
		$this->object->save( $item );

		$search->setConditions( $search->compare( '==', 'price.label', 'core:property-test' ) );
		$item2 = $this->object->search( $search, ['price/property', 'price/property/type'] )->first();

		$this->object->delete( $item->getId() );

		$this->assertEquals( 1, count( $item->getPropertyItems() ) );
		$this->assertEquals( 1, count( $item2->getPropertyItems() ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $item->getPropertyItems()->first()?->getTypeItem() );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $item2->getPropertyItems()->first()?->getTypeItem() );
	}


	public function testSaveUpdateDelete()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'price.editor', $this->context->editor() ) );
		$items = $this->object->search( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item->setId( null );
		$item->setLabel( 'price label' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


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

		$this->assertEquals( $this->context->editor(), $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

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

		$this->assertEquals( $this->context->editor(), $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testFilter()
	{
		$this->assertInstanceOf( \Aimeos\Base\Criteria\SQL::class, $this->object->filter() );
	}


	public function testSearch()
	{
		$search = $this->object->filter()->add( ['price.value' => '2.95', 'price.costs' => '1.00'] );

		$item = $this->object->search( $search, ['customer'] )->first( new \RuntimeException( 'No item found' ) );
		$listItem = $item->getListItems( 'customer', 'test' )->first( new \RuntimeException( 'No list item found' ) );

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'price.id', null );
		$expr[] = $search->compare( '!=', 'price.siteid', null );
		$expr[] = $search->compare( '==', 'price.type', 'default' );
		$expr[] = $search->compare( '==', 'price.domain', 'service' );
		$expr[] = $search->compare( '>=', 'price.label', '' );
		$expr[] = $search->compare( '==', 'price.currencyid', 'EUR' );
		$expr[] = $search->compare( '==', 'price.quantity', 2 );
		$expr[] = $search->compare( '==', 'price.value', '2.95' );
		$expr[] = $search->compare( '==', 'price.costs', '1.00' );
		$expr[] = $search->compare( '==', 'price.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'price.taxrate', '{' );
		$expr[] = $search->compare( '==', 'price.status', 1 );
		$expr[] = $search->compare( '>=', 'price.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'price.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'price.editor', '' );

		$param = ['customer', 'test', $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->make( 'price:has', $param ), null );

		$param = ['customer', 'test'];
		$expr[] = $search->compare( '!=', $search->make( 'price:has', $param ), null );

		$param = ['customer'];
		$expr[] = $search->compare( '!=', $search->make( 'price:has', $param ), null );

		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchProperty()
	{
		$total = 0;
		$search = $this->object->filter();

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
		$expr[] = $search->compare( '>=', 'price.editor', '' );

		$param = ['zone', null, 'NY'];
		$expr[] = $search->compare( '!=', $search->make( 'price:prop', $param ), null );

		$param = ['zone', null];
		$expr[] = $search->compare( '!=', $search->make( 'price:prop', $param ), null );

		$param = ['zone'];
		$expr[] = $search->compare( '!=', $search->make( 'price:prop', $param ), null );

		$search->setConditions( $search->and( $expr ) );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
	}


	public function testSearchTotal()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 10 );
		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 10, count( $results ) );
		$this->assertGreaterThanOrEqual( 27, $total );
	}


	public function testSearchBase()
	{
		$search = $this->object->filter( true );
		$results = $this->object->search( $search )->toArray();
		$this->assertGreaterThanOrEqual( 25, count( $results ) );

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
		$item = $this->object->create();
		$item->setValue( '1.00' );

		$lowest = $this->object->getLowestPrice( map( [$item] ), 1 );

		$this->assertEquals( $item, $lowest );
	}


	public function testGetLowestPriceQuantity()
	{
		$item = $this->object->create();
		$item->setValue( '10.00' );

		$item2 = $this->object->create();
		$item2->setValue( '5.00' );
		$item2->setQuantity( 5 );

		$lowest = $this->object->getLowestPrice( map( [$item, $item2] ), 10 );

		$this->assertEquals( $item2, $lowest );
	}


	public function testGetLowestPriceCurrency()
	{
		$item = $this->object->create();
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
		$item = $this->object->create();
		$item->setValue( '1.00' );
		$item->setQuantity( 5 );

		$this->expectException( \Aimeos\MShop\Price\Exception::class );
		$this->object->getLowestPrice( map( [$item] ), 1 );
	}
}
