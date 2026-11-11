<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\MShop\Policy;


class GroupsTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\Base\View\Standard();
		$view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\Standard( $view, ['editor'] ) );

		$this->context = \TestHelper::context()->setView( $view );
		$this->context->config()->set( 'test/resource/groups', ['admin'] );
		$this->context->config()->set( 'test/resource/product/groups', ['editor'] );
		$this->context->config()->set( 'test/resource/service/groups', ['admin'] );

		$this->object = new \Aimeos\MShop\Policy\Groups( 'test/resource/' );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testAllowed()
	{
		$this->assertTrue( $this->object->allowed( $this->context, 'product', 'delete' ) );
		$this->assertFalse( $this->object->allowed( $this->context, 'service', 'get' ) );
		$this->assertNull( $this->object->allowed( $this->context, 'order', 'get' ) );
	}


	public function testAllowedAction()
	{
		$this->context->config()->set( 'test/resource/product/import', ['admin'] );

		$this->assertTrue( $this->object->allowed( $this->context, 'product', 'save' ) );
		$this->assertFalse( $this->object->allowed( $this->context, 'product', 'import' ) );
	}


	public function testAllowedActionEmpty()
	{
		// empty lists allow the action for super users only
		$this->context->config()->set( 'test/resource/product/import', [] );

		$this->assertFalse( $this->object->allowed( $this->context, 'product', 'import' ) );
	}


	public function testAllowedActionSubresource()
	{
		// sub-resources with the name of the action don't overwrite the resource groups
		$this->context->config()->set( 'test/resource/product/import/groups', ['admin'] );

		$this->assertTrue( $this->object->allowed( $this->context, 'product', 'import' ) );
	}


	public function testAllowedString()
	{
		// single group names as strings have been accepted by the interfaces before
		$this->context->config()->set( 'test/resource/order/groups', 'editor' );

		$this->assertTrue( $this->object->allowed( $this->context, 'order', 'get' ) );
	}


	public function testAllowedRoot()
	{
		$this->assertFalse( $this->object->allowed( $this->context, '', 'get' ) );
	}


	public function testAllowedNoView()
	{
		$context = ( new \Aimeos\MShop\Context() )->setConfig( $this->context->config() );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->allowed( $context, 'product', 'get' );
	}
}
