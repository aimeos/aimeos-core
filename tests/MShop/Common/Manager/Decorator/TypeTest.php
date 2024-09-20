<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


class TypeTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $stub;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Product\\Manager\\Standard' )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['searchRefs', 'type'] )
			->getMock();

		$this->stub->method( 'type' )->willReturn( ['product'] );

		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Type( $this->stub, $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testSearchRefs()
	{
		$item = \Aimeos\MShop::create( $this->context, 'product' )->find( 'ABCD' );
		$entries = [$item->getId() => $item->toArray( true )];

		$this->stub->expects( $this->once() )->method( 'searchRefs' )->willReturn( $entries );

		$entries = $this->object->searchRefs( $entries, ['product/type'] );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, current( $entries )['.type'] );
	}
}
