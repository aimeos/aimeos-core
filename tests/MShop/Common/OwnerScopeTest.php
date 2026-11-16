<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\MShop\Common;


class OwnerScopeTest extends \PHPUnit\Framework\TestCase
{
	public function testKey()
	{
		$this->assertEquals( 'product.lists.domain', OwnerScope::key( 'product/lists' ) );
	}


	public function testLimit()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'price' );
		$filter = $manager->filter()->slice( 0, 0x7fffffff );

		$this->assertSame( $filter, OwnerScope::limit( $manager, $filter, fn() => ['product'] ) );

		$domains = $manager->search( $filter )->getDomain()->unique()->values()->all();
		$this->assertEquals( ['product'], $domains );
	}


	public function testLimitNone()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'price' );
		$filter = $manager->filter();

		$this->assertCount( 0, $manager->search( OwnerScope::limit( $manager, $filter, fn() => [] ) ) );
	}


	public function testLimitNotShared()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'product' );
		$fcn = fn() => throw new \LogicException( 'Domains must not be computed' );

		$this->assertEquals( $manager->filter(), OwnerScope::limit( $manager, $manager->filter(), $fcn ) );
	}


	public function testLimitSubDomain()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'price' );
		$filter = OwnerScope::limit( $manager, $manager->filter(), fn() => ['product'] );

		$sql = $filter->getConditions()->toSource( ['price.domain' => 'string'], ['price.domain' => 'domain'] );
		$this->assertStringContainsString( "LIKE 'product/%'", $sql );
	}


	public function testOwner()
	{
		$item = \Aimeos\MShop::create( \TestHelper::context(), 'price' )->create( ['price.domain' => 'product'] );
		$this->assertEquals( 'product', OwnerScope::owner( $item ) );
	}


	public function testOwnerNone()
	{
		$item = \Aimeos\MShop::create( \TestHelper::context(), 'product' )->create();
		$this->assertEquals( '', OwnerScope::owner( $item ) );
	}


	public function testOwnerSubDomain()
	{
		$item = \Aimeos\MShop::create( \TestHelper::context(), 'price' )->create( ['price.domain' => 'product/property'] );
		$this->assertEquals( 'product', OwnerScope::owner( $item ) );
	}


	public function testShared()
	{
		$this->assertTrue( OwnerScope::shared( \Aimeos\MShop::create( \TestHelper::context(), 'price' ) ) );
		$this->assertFalse( OwnerScope::shared( \Aimeos\MShop::create( \TestHelper::context(), 'product' ) ) );
	}
}
