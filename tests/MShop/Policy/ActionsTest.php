<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\MShop\Policy;


class ActionsTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\Base\View\Standard();
		$view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\Standard( $view, ['editor'] ) );

		$this->context = \TestHelper::context()->setView( $view );
		// runtime values can't be read by the keys of their sub-arrays
		$this->context->config()->set( 'test/resource/product', ['get' => ['editor'], 'delete' => ['admin']] );
		$this->context->config()->set( 'test/resource/product/get', ['editor'] );
		$this->context->config()->set( 'test/resource/product/delete', ['admin'] );

		$this->object = new \Aimeos\MShop\Policy\Actions( 'test/resource' );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testAllowed()
	{
		$this->assertTrue( $this->object->allowed( $this->context, 'product', 'get' ) );
		$this->assertFalse( $this->object->allowed( $this->context, 'product', 'delete' ) );
	}


	public function testAllowedNoGroups()
	{
		// actions of configured resources without user groups are allowed for super users only
		$this->assertFalse( $this->object->allowed( $this->context, 'product', 'save' ) );
	}


	public function testAllowedUnconfigured()
	{
		$this->assertNull( $this->object->allowed( $this->context, 'order', 'get' ) );
	}


	public function testAllowedString()
	{
		// single group names as strings restrict the actions like lists of user groups
		$this->context->config()->set( 'test/resource/service', ['get' => 'editor', 'save' => 'admin'] );
		$this->context->config()->set( 'test/resource/service/get', 'editor' );
		$this->context->config()->set( 'test/resource/service/save', 'admin' );

		$this->assertTrue( $this->object->allowed( $this->context, 'service', 'get' ) );
		$this->assertFalse( $this->object->allowed( $this->context, 'service', 'save' ) );
	}


	public function testAllowedSubResources()
	{
		// resources configured only by their sub-resources have no rules on their own
		$this->context->config()->set( 'test/resource/order/address/get', ['editor'] );

		$this->assertNull( $this->object->allowed( $this->context, 'order', 'get' ) );
		$this->assertTrue( $this->object->allowed( $this->context, 'order/address', 'get' ) );
	}


	public function testAllowedEmpty()
	{
		// empty lists are actions allowed for super users only
		$this->context->config()->set( 'test/resource/order/get', [] );

		$this->assertFalse( $this->object->allowed( $this->context, 'order', 'get' ) );
	}


	public function testAllowedChanged()
	{
		$this->context->config()->set( 'test/resource/product/get', ['admin'] );

		$this->assertFalse( $this->object->allowed( $this->context, 'product', 'get' ) );
	}
}
