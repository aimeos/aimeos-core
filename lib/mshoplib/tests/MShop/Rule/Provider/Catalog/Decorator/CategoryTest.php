<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MShop\Rule\Provider\Catalog\Decorator;


class CategoryTest extends \PHPUnit\Framework\TestCase
{
	private $item;
	private $stub;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->item = \Aimeos\MShop\Rule\Manager\Factory::create( $this->context )->create();

		$this->stub = $this->getMockBuilder( \Aimeos\MShop\Rule\Provider\Catalog\Percent::class )
			->setConstructorArgs( [$this->context, $this->item] )
			->setMethods( ['apply'] )
			->getMock();
	}


	protected function tearDown() : void
	{
		unset( $this->stub, $this->item, $this->context );
	}


	public function testCheckConfigBE()
	{
		$object = new \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Category( $this->context, $this->item, $this->stub );
		$result = $object->checkConfigBE( ['category.code' => 'root'] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
		$this->assertEquals( null, $result['category.code'] );
	}


	public function testGetConfigBE()
	{
		$object = new \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Category( $this->context, $this->item, $this->stub );
		$list = $object->getConfigBE();

		$this->assertGreaterThanOrEqual( 1, count( $list ) );
		$this->assertArrayHasKey( 'category.code', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testApply()
	{
		$this->item->setConfig( ['category.code' => 'cafe'] );
		$object = new \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Category( $this->context, $this->item, $this->stub );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE', ['catalog'] );

		$this->stub->expects( $this->once() )->method( 'apply' )
			->will( $this->returnValue( false ) );

		$this->assertFalse( $object->apply( $product ) );
	}


	public function testApplyFail()
	{
		$object = new \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Category( $this->context, $this->item, $this->stub );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->assertFalse( $object->apply( $product ) );
	}


	public function testApplyLast()
	{
		$this->item->setConfig( ['category.code' => 'cafe', 'last-rule' => true] );
		$object = new \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Category( $this->context, $this->item, $this->stub );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE', ['catalog'] );

		$this->stub->expects( $this->once() )->method( 'apply' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $object->apply( $product ) );
	}
}
