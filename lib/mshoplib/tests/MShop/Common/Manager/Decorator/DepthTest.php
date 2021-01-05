<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


class DepthTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$manager = new \Aimeos\MShop\Product\Manager\Standard( $this->context );

		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Depth( $manager, $this->context );
	}

	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}

	public function testSearchItems()
	{
		$item = $this->object->find( 'U:TESTP', ['product'] );

		$this->assertEquals( 1, count( $item->getRefItems( 'product' ) ) );
	}

	public function testSearchLimitOne()
	{
		$this->context->getConfig()->set( 'mshop/common/manager/maxdepth', 0 );

		$item = $this->object->find( 'U:TESTP', ['product'] );

		$this->assertEquals( 0, count( $item->getRefItems( 'product' ) ) );
	}
}
