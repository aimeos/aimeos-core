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
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->item = \Aimeos\MShop\Rule\Manager\Factory::create( $this->context )->create();
		$provider = new \Aimeos\MShop\Rule\Provider\Catalog\Percent( $this->context, $this->item );

		$this->stub = $this->getMockBuilder( \Aimeos\MShop\Rule\Provider\Catalog\Percent::class )
			->setConstructorArgs( [$this->context, $this->item] )
			->setMethods( ['apply'] )
			->getMock();

		$this->object = new \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Category( $this->context, $this->item, $this->stub );
	}


	protected function tearDown() : void
	{
		unset( $this->object,  $this->stub,  $this->item, $this->context );
	}


	public function testApply()
	{
		$this->item->setConfig( ['category.code' => ['cafe']] );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE', ['catalog'] );

		$this->stub->expects( $this->once() )->method( 'apply' )
			->will( $this->returnValue( false ) );

		$this->assertFalse( $this->object->apply( $product ) );
	}


	public function testApplyFail()
	{
		$product = \Aimeos\MShop::create( $this->context, 'product' )->create();
		$this->assertFalse( $this->object->apply( $product ) );
	}


	public function testApplyLast()
	{
		$this->item->setConfig( ['category.code' => ['cafe'], 'last-rule' => true] );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE', ['catalog'] );

		$this->stub->expects( $this->once() )->method( 'apply' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->apply( $product ) );
	}
}
