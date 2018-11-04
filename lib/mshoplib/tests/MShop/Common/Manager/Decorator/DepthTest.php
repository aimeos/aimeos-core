<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$manager = new \Aimeos\MShop\Product\Manager\Standard( $this->context );

		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Depth( $manager, $this->context );
	}

	protected function tearDown()
	{
		unset( $this->object, $this->context );
	}

	public function testSearchItems()
	{
		$item = $this->object->findItem( 'U:TESTP', ['product'] );

		$this->assertEquals( 1, count( $item->getRefItems( 'product' ) ) );
	}

	public function testSearchLimitOne()
	{
		$this->context->getConfig()->set( 'mshop/common/manager/maxdepth', 0 );

		$item = $this->object->findItem( 'U:TESTP', ['product'] );

		$this->assertEquals( 0, count( $item->getRefItems( 'product' ) ) );
	}
}
