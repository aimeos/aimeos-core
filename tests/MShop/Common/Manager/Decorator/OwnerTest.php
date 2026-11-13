/<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


class OwnerTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $manager;
	private $object;
	private $ids = [];
	private $productId;


	protected function setUp() : void
	{
		$this->manager = new \Aimeos\MShop\Price\Manager\Standard( \TestHelper::context() );

		$this->context = \TestHelper::context();
		$this->context->setPolicy( new class implements \Aimeos\MShop\PolicyIface {
			public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool {
				return ['product' => true, 'service' => false][$resource] ?? null;
			}
		} );

		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Owner( new \Aimeos\MShop\Price\Manager\Standard( $this->context ), $this->context );
		$this->object->setObject( $this->object );
	}


	protected function tearDown() : void
	{
		if( $this->productId ) {
			\Aimeos\MShop::create( \TestHelper::context(), 'product' )->delete( $this->productId );
		}

		\Aimeos\MShop\Price\Manager\Standard::unmacro( 'ownerTestList' );

		$this->manager->delete( $this->ids );
		unset( $this->object, $this->manager, $this->context );
	}


	public function testDelete()
	{
		$item = $this->price( 'product' );

		$this->object->delete( $item->getId() );

		$this->assertEquals( 0, count( $this->manager->search( $this->filter( [$item->getId()] ) ) ) );
	}


	public function testDeleteDenied()
	{
		$item = $this->price( 'service' );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->expectExceptionMessageMatches( '/^Permission "save" for "service" is required to change "price" items$/' );
		$this->object->delete( [$item] );
	}


	public function testDeleteOwnerSaveAllowed()
	{
		// shared items can be removed while saving the owning item, so its "delete" permission isn't required
		$this->context->setPolicy( new class implements \Aimeos\MShop\PolicyIface {
			public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool {
				return $action === 'save';
			}
		} );

		$item = $this->price( 'service' );
		$this->object->delete( $item->getId() );

		$this->assertEquals( 0, count( $this->manager->search( $this->filter( [$item->getId()] ) ) ) );
	}


	public function testDeleteNotFound()
	{
		// items which aren't found (e.g. of sub-sites) are skipped like with missing IDs
		$this->assertSame( $this->object, $this->object->delete( ['-1'] ) );
	}


	public function testDeleteGenerator()
	{
		$item = $this->price( 'product' );
		$this->object->delete( ( fn() => yield $item->getId() )() );

		$this->assertEquals( 0, count( $this->manager->search( $this->filter( [$item->getId()] ) ) ) );
	}


	public function testSaveNotFound()
	{
		// items of sub-sites aren't found but would be overwritten without checking their stored domain
		$item = $this->manager->create()->setId( '-1' )->setDomain( 'product' )->setValue( '13.37' );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->expectExceptionCode( 404 );
		$this->expectExceptionMessage( 'Item with ID "-1" in "price.id" not found' );
		$this->object->save( $item );
	}


	public function testSaveGenerator()
	{
		$item = $this->manager->create()->setDomain( 'product' )->setValue( '10.00' )->setCurrencyId( 'EUR' );
		$result = $this->object->save( ( fn() => yield $item )() );
		$this->ids[] = $item->getId();

		$this->assertNotNull( $item->getId() );
		$this->assertCount( 1, $result );
	}


	public function testCallUnchecked()
	{
		$price = $this->price( 'service' )->setValue( '13.37' );
		\Aimeos\MShop\Price\Manager\Standard::macro( 'ownerTestList', fn( iterable $list ) => $list );

		// additional methods aren't checked, only the ones of domain decorators like the media decorator
		$this->assertSame( $price, $this->object->getLowestPrice( map( [$price] ), 1 ) );
		$this->assertEquals( [$price], $this->object->ownerTestList( [$price] ) );
	}


	public function testGet()
	{
		$item = $this->price( 'service' );

		$this->assertEquals( 'service', $this->object->get( $item->getId() )->getDomain() );
	}


	public function testKey()
	{
		$this->assertEquals( 'product.lists.domain', \Aimeos\MShop\Common\Manager\Decorator\Owner::key( 'product/lists' ) );
	}


	public function testListsSave()
	{
		$product = $this->product();
		$manager = \Aimeos\MShop::create( $this->context, 'product' );

		// unchanged prices of denied domains are saved together with the product
		$manager->save( $manager->get( $product->getId(), ['price'] )->setLabel( 'owner test 2' ) );

		$item = \Aimeos\MShop::create( \TestHelper::context(), 'product' )->get( $product->getId(), ['price'] );
		$this->assertEquals( 'owner test 2', $item->getLabel() );
		$this->assertEquals( ['product', 'service'], $item->getRefItems( 'price' )->getDomain()->values()->sort()->values()->all() );
	}


	public function testListItemDeleteDenied()
	{
		$product = $this->product( 'service' );
		$manager = \Aimeos\MShop::create( $this->context, 'product/lists' );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$manager->delete( $product->getListItems( 'service' )->getId()->all() );
	}


	public function testListItemSaveDenied()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product/lists' );
		$item = $manager->create()->setParentId( $this->product()->getId() )->setDomain( 'service' )->setRefId( '-1' );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$manager->save( $item );
	}


	public function testListItemSaveDeniedStored()
	{
		$product = $this->product( 'service' );
		$manager = \Aimeos\MShop::create( $this->context, 'product/lists' );

		// list items referencing denied domains must not be re-pointed to other domains
		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$manager->save( $product->getListItems( 'service' )->first()->setDomain( 'price' ) );
	}


	public function testListsSaveDenied()
	{
		$product = $this->product();
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$item = $manager->get( $product->getId(), ['price'] );

		foreach( $item->getRefItems( 'price' ) as $price ) {
			$price->setValue( '13.37' );
		}

		try
		{
			$manager->save( $item );
			$this->fail( 'Exception expected' );
		}
		catch( \Aimeos\MShop\PolicyException $e )
		{
			$this->assertEquals( 403, $e->getCode() );
		}

		$prices = \Aimeos\MShop::create( \TestHelper::context(), 'product' )->get( $product->getId(), ['price'] )->getRefItems( 'price' );
		$this->assertEquals( '10.00', $prices->filter( fn( $price ) => $price->getDomain() === 'service' )->first()->getValue() );
	}


	public function testSave()
	{
		$item = $this->object->save( $this->price( 'product' )->setValue( '13.37' ) );

		$this->assertEquals( '13.37', $this->manager->get( $item->getId() )->getValue() );
	}


	public function testSaveDenied()
	{
		$item = $this->manager->create()->setDomain( 'service' )->setValue( '13.37' );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->object->save( $item );
	}


	public function testSaveDeniedProperty()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'price' );
		$item = $manager->get( $this->price( 'service' )->getId(), ['price/property'] );
		$item->addPropertyItem( $manager->createPropertyItem()->setType( 'package-weight' )->setValue( '1' ) );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$this->expectExceptionCode( 403 );
		$this->object->save( $item );
	}


	public function testSaveDeniedStored()
	{
		$item = $this->price( 'service' );

		// Items of denied domains must not be taken over by changing the owning domain
		try
		{
			$this->object->save( $item->setDomain( 'product' )->setValue( '13.37' ) );
			$this->fail( 'Exception expected' );
		}
		catch( \Aimeos\MShop\PolicyException $e )
		{
			$this->assertEquals( 403, $e->getCode() );
		}

		$this->assertEquals( 'service', $this->manager->get( $item->getId() )->getDomain() );
	}


	public function testSaveDeniedUnchanged()
	{
		$item = $this->price( 'service' );

		$this->assertEquals( $item->getId(), $this->object->save( $this->manager->get( $item->getId() ) )->getId() );
	}


	public function testSaveUnrestricted()
	{
		$this->context->setPolicy( null );
		$item = $this->object->save( $this->price( 'service' )->setValue( '13.37' ) );

		$this->assertEquals( '13.37', $this->manager->get( $item->getId() )->getValue() );
	}


	public function testSaveUnconfigured()
	{
		// Domains without rules in the policy don't restrict access to their items
		$item = $this->object->save( $this->price( 'unconfigured' )->setValue( '13.37' ) );

		$this->assertEquals( '13.37', $this->manager->get( $item->getId() )->getValue() );
	}


	public function testSaveUnowned()
	{
		// the domain of attributes is the domain they belong to, not the one owning them
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'attribute' );
		$object = new \Aimeos\MShop\Common\Manager\Decorator\Owner( new \Aimeos\MShop\Attribute\Manager\Standard( $this->context ), $this->context );
		$item = $manager->create()->setDomain( 'service' )->setType( 'size' )->setCode( 'owner-test' )->setLabel( 'owner test' );

		try
		{
			$object->save( $item );
			$this->assertNotNull( $item->getId() );
		}
		finally
		{
			if( $item->getId() ) {
				$manager->delete( $item->getId() );
			}
		}
	}


	public function testSaveUnownedRemoved()
	{
		// entries are removed by setting their value to NULL because the configurations are merged by key
		$list = $this->context->config()->get( 'mshop/common/manager/unowned', [] );
		$this->context->config()->set( 'mshop/common/manager/unowned', ['attribute' => null] + $list );
		$object = new \Aimeos\MShop\Common\Manager\Decorator\Owner( new \Aimeos\MShop\Attribute\Manager\Standard( $this->context ), $this->context );
		$item = $object->create()->setDomain( 'service' )->setType( 'size' )->setCode( 'owner-test' )->setLabel( 'owner test' );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$object->save( $item );
	}


	public function testSaveUnownedType()
	{
		// the last part of the resource type matches the type managers of all domains
		$this->context->config()->set( 'mshop/common/manager/unowned', ['type' => 'type'] );
		$object = new \Aimeos\MShop\Common\Manager\Decorator\Owner( new \Aimeos\MShop\Price\Manager\Standard( $this->context ), $this->context );

		$this->expectException( \Aimeos\MShop\PolicyException::class );
		$object->save( $this->manager->create()->setDomain( 'service' )->setValue( '13.37' ) );
	}


	public function testSaveUnownedConfig()
	{
		$this->context->config()->set( 'mshop/common/manager/unowned', ['price' => 'price'] );
		$object = new \Aimeos\MShop\Common\Manager\Decorator\Owner( new \Aimeos\MShop\Price\Manager\Standard( $this->context ), $this->context );
		$item = $this->manager->create()->setDomain( 'service' )->setValue( '13.37' )->setCurrencyId( 'EUR' );

		$object->save( $item );
		$this->ids[] = $item->getId();

		$this->assertNotNull( $item->getId() );
	}


	protected function filter( array $ids ) : \Aimeos\Base\Criteria\Iface
	{
		return $this->manager->filter()->add( 'price.id', '==', $ids )->order( 'price.id' );
	}


	protected function product( ?string $domain = null ) : \Aimeos\MShop\Product\Item\Iface
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'product' );

		$item = $manager->create()->setCode( 'owner-test' )->setLabel( 'owner test' )
			->addListItem( 'price', $manager->createListItem()->setRefId( $this->price( 'product' )->getId() ) )
			->addListItem( 'price', $manager->createListItem()->setRefId( $this->price( 'service' )->getId() ) );

		if( $domain ) {
			$item->addListItem( $domain, $manager->createListItem()->setRefId( '-1' ) );
		}

		$item = $manager->save( $item );
		$this->productId = $item->getId();

		return $item;
	}


	protected function price( string $domain ) : \Aimeos\MShop\Price\Item\Iface
	{
		$item = $this->manager->save( $this->manager->create()->setDomain( $domain )->setValue( '10.00' )->setCurrencyId( 'EUR' ) );
		$this->ids[] = $item->getId();

		return $item;
	}
}
