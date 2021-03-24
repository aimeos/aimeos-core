<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MShop\Rule\Provider\Catalog;


class PercentTest extends \PHPUnit\Framework\TestCase
{
	private $item;
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->item = \Aimeos\MShop\Rule\Manager\Factory::create( $this->context )->create();

		$this->object = new \Aimeos\MShop\Rule\Provider\Catalog\Percent( $this->context, $this->item );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->item, $this->context );
	}


	public function testCheckConfigBE()
	{
		$result = $this->object->checkConfigBE( ['percent' => '10'] );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
		$this->assertEquals( null, $result['last-rule'] );
		$this->assertEquals( null, $result['percent'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertGreaterThanOrEqual( 2, count( $list ) );
		$this->assertArrayHasKey( 'last-rule', $list );
		$this->assertArrayHasKey( 'percent', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testApply()
	{
		$this->item->setConfig( ['percent' => '10'] );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC', ['price'] );

		$this->assertFalse( $this->object->apply( $product ) );
		$this->assertEquals( '660.00', $product->getRefItems( 'price' )->getValue()->first() );
	}


	public function testApplyLast()
	{
		$this->item->setConfig( ['last-rule' => true] );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->assertTrue( $this->object->apply( $product ) );
	}
}
