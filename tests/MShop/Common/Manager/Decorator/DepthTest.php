<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


class DepthTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$manager = new \Aimeos\MShop\Product\Manager\Standard( $this->context );

		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Depth( $manager, $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testClasses()
	{
		$expected = ['Aimeos\MShop\Product\Manager\Standard', 'Aimeos\MShop\Common\Manager\Decorator\Depth'];
		$this->assertEquals( $expected, $this->object->classes() );
	}


	public function testSearch()
	{
		$filter = $this->object->filter()->add( ['product.code' => 'U:TESTP'] );
		$item = $this->object->search( $filter, ['product'] );

		$this->assertEquals( 1, count( $item->getRefItems( 'product' ) ) );
	}


	public function testSearchLimit()
	{
		$this->context->config()->set( 'mshop/common/manager/maxdepth', 0 );

		$filter = $this->object->filter()->add( ['product.code' => 'U:TESTP'] );
		$item = $this->object->search( $filter, ['product'] );

		$this->assertEquals( 0, count( $item->getRefItems( 'product' ) ) );
	}
}
