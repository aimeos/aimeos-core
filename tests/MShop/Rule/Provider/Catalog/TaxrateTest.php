<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2023
 */


namespace Aimeos\MShop\Rule\Provider\Catalog;


class TaxrateTest extends \PHPUnit\Framework\TestCase
{
	private $item;
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->item = \Aimeos\MShop::create( $this->context, 'rule' )->create();

		$this->object = new \Aimeos\MShop\Rule\Provider\Catalog\Taxrate( $this->context, $this->item );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->item, $this->context );
	}


	public function testCheckConfigBE()
	{
		$result = $this->object->checkConfigBE( ['taxrate' => '10.00'] );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
		$this->assertEquals( null, $result['last-rule'] );
		$this->assertEquals( null, $result['taxrate'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertGreaterThanOrEqual( 2, count( $list ) );
		$this->assertArrayHasKey( 'last-rule', $list );
		$this->assertArrayHasKey( 'taxrate', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testApply()
	{
		$this->item->setConfig( ['taxrate' => '50'] );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC', ['price'] );

		$this->assertFalse( $this->object->apply( $product ) );
		$this->assertEquals( '50.00', $product->getRefItems( 'price' )->getTaxrate()->first() );
	}


	public function testApplyLast()
	{
		$this->item->setConfig( ['last-rule' => true] );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->assertTrue( $this->object->apply( $product ) );
	}
}
