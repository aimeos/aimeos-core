<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


class PropertyTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $stub;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Customer\\Manager\\Standard' )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['saveRefs', 'searchRefs', 'type'] )
			->getMock();

		$this->stub->method( 'type' )->willReturn( ['customer'] );

		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Property( $this->stub, $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testSaveRefs()
	{
		$custItem = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' );

		$this->stub->expects( $this->once() )->method( 'saveRefs' )->willReturn( $custItem );

		$this->object->saveRefs( $custItem );
	}


	public function testSearchRefs()
	{
		$custItem = \Aimeos\MShop::create( $this->context, 'customer' )->find( 'test@example.com' );
		$entries = [$custItem->getId() => $custItem->toArray( true )];

		$this->stub->expects( $this->once() )->method( 'searchRefs' )->willReturn( $entries );

		$entries = $this->object->searchRefs( $entries, ['customer/property'] );

		$this->assertEquals( 1, count( current( $entries )['.propitems'] ) );
	}
}
