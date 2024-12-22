<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2024
 */

namespace Aimeos\MShop\Basket\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->editor = $this->context->editor();

		$this->object = new \Aimeos\MShop\Basket\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDelete()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testCreate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Basket\Item\Iface::class, $this->object->create() );
	}


	public function testCreateOrder()
	{
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE' );
		$orderProduct = \Aimeos\MShop::create( $this->context, 'order/product' )->create()->copyFrom( $product );
		$order = \Aimeos\MShop::create( $this->context, 'order' )->create()->addProduct( $orderProduct );
		$basket = $this->object->save( $this->object->create()->setItem( $order )->setId( 'unittest-100' ) );

		$basket2 = $this->object->get( $basket->getId() );
		$this->object->delete( $basket );

		$this->assertEquals( 1, count( $basket2->getItem()->getProducts() ) );
	}


	public function testGet()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$expected = $this->object->search( $search )->first( new \Exception( 'No order basket item found' ) );

		$this->assertEquals( $expected, $this->object->get( $expected->getId() ) );
	}


	public function testSaveUpdateDelete()
	{
		$search = $this->object->filter()->add( ['basket.customerid' => -1] )->slice( 0, 1 );
		$item = $this->object->search( $search )->first( new \Exception( 'No order basket item found' ) );

		$item->setId( 'unittest_1' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setName( 'unit test' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCustomerId(), $itemSaved->getCustomerId() );
		$this->assertEquals( $item->getItem(), $itemSaved->getItem() );
		$this->assertEquals( $item->getName(), $itemSaved->getName() );

		$this->assertEquals( $this->editor, $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCustomerId(), $itemUpd->getCustomerId() );
		$this->assertEquals( $itemExp->getItem(), $itemUpd->getItem() );
		$this->assertEquals( $itemExp->getName(), $itemUpd->getName() );

		$this->assertEquals( $this->editor, $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testFilter()
	{
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter() );
	}


	public function testSearch()
	{
		$total = 0;
		$search = $this->object->filter();
		$siteid = $this->context->locale()->getSiteId();

		$expr = [];
		$expr[] = $search->compare( '!=', 'basket.id', null );
		$expr[] = $search->compare( '==', 'basket.siteid', $siteid );
		$expr[] = $search->compare( '==', 'basket.customerid', '-1' );
		$expr[] = $search->compare( '>=', 'basket.name', '' );
		$expr[] = $search->compare( '>=', 'basket.content', '' );
		$expr[] = $search->compare( '>=', 'basket.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'basket.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'basket.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}
}
