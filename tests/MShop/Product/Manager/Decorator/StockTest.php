<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 */


namespace Aimeos\MShop\Product\Manager\Decorator;


class StockTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $stub;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Product\\Manager\\Standard' )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['saveRefs', 'searchRefs'] )
			->getMock();

		$this->object = new \Aimeos\MShop\Product\Manager\Decorator\Stock( $this->stub, $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testCreateStockItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $this->object->createStockItem() );
	}


	public function testSaveRefs()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->find( 'ABCD' );

		$this->stub->expects( $this->once() )->method( 'saveRefs' )->willReturn( $item );

		$this->object->saveRefs( $item );
	}


	public function testSearchRefs()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->find( 'ABCD' );
		$entries = [$item->getId() => $item->toArray( true )];

		$this->stub->expects( $this->once() )->method( 'searchRefs' )->willReturn( $entries );

		$entries = $this->object->searchRefs( $entries, ['stock'] );

		$this->assertCount( 1, current( $entries )['.stock'] );
		$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, current( current( $entries )['.stock'] ) );
	}
}
