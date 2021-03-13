<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MShop\Rule\Provider\Catalog\Decorator;


class CgroupTest extends \PHPUnit\Framework\TestCase
{
	private $item;
	private $stub;
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
	}


	protected function tearDown() : void
	{
		unset( $this->stub,  $this->item, $this->context );
	}


	public function testApply()
	{
		$this->context->setGroupIds( ['456', '789'] );
		$this->item->setConfig( ['cgroup.id' => ['123' => 'a', '456' => 'b']] );

		$object = new \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Cgroup( $this->context, $this->item, $this->stub );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->stub->expects( $this->once() )->method( 'apply' )
			->will( $this->returnValue( false ) );

		$this->assertFalse( $object->apply( $product ) );
	}


	public function testApplyFail()
	{
		$object = new \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Cgroup( $this->context, $this->item, $this->stub );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->assertFalse( $object->apply( $product ) );
	}


	public function testApplyLast()
	{
		$this->context->setGroupIds( ['456', '789'] );
		$this->item->setConfig( ['cgroup.id' => ['123' => 'a', '456' => 'b'], 'last-rule' => true] );

		$object = new \Aimeos\MShop\Rule\Provider\Catalog\Decorator\Cgroup( $this->context, $this->item, $this->stub );
		$product = \Aimeos\MShop::create( $this->context, 'product' )->create();

		$this->stub->expects( $this->once() )->method( 'apply' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $object->apply( $product ) );
	}
}
