<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */


namespace Aimeos\MShop\Common\Manager;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $item;
	private $mock;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->item = \Aimeos\MShop::create( $this->context, 'product' )->createItem();

		$this->mock = $this->getMockBuilder( '\Aimeos\MShop\Product\Manager\Standard' )
			->setConstructorArgs( [$this->context] )->setMethods( ['test'] )->getMock();
	}


	protected function tearDown() : void
	{
		unset( $this->item, $this->mock, $this->context );
	}


	public function testFilter()
	{
		$this->assertTrue( $this->access( 'filter' )->invokeArgs( $this->mock, [$this->item] ) );
	}


	public function testFilterTrue()
	{
		$this->mock->addFilter( \Aimeos\MShop\Common\Item\Iface::class, function( $item ) {
			return true;
		} );

		$this->assertTrue( $this->access( 'filter' )->invokeArgs( $this->mock, [$this->item] ) );
	}


	public function testFilterFalse()
	{
		$this->mock->addFilter( \Aimeos\MShop\Common\Item\Iface::class, function( $item ) {
			return false;
		} );

		$this->assertFalse( $this->access( 'filter' )->invokeArgs( $this->mock, [$this->item] ) );
	}


	public function testFilterMultipleSameTrue()
	{
		$this->mock->addFilter( \Aimeos\MShop\Common\Item\Iface::class, function( $item ) {
			return true;
		} );

		$this->mock->addFilter( \Aimeos\MShop\Common\Item\Iface::class, function( $item ) {
			return true;
		} );

		$this->assertTrue( $this->access( 'filter' )->invokeArgs( $this->mock, [$this->item] ) );
	}


	public function testFilterMultipleSameFalse()
	{
		$this->mock->addFilter( \Aimeos\MShop\Common\Item\Iface::class, function( $item ) {
			return true;
		} );

		$this->mock->addFilter( \Aimeos\MShop\Common\Item\Iface::class, function( $item ) {
			return false;
		} );

		$this->assertFalse( $this->access( 'filter' )->invokeArgs( $this->mock, [$this->item] ) );
	}


	public function testFilterMultipleIfaceTrue()
	{
		$this->mock->addFilter( \Aimeos\MShop\Common\Item\Iface::class, function( $item ) {
			return true;
		} );

		$this->mock->addFilter( \Aimeos\MShop\Common\Item\ListRef\Iface::class, function( $item ) {
			return true;
		} );

		$this->assertTrue( $this->access( 'filter' )->invokeArgs( $this->mock, [$this->item] ) );
	}


	public function testFilterMultipleIfaceFalse()
	{
		$this->mock->addFilter( \Aimeos\MShop\Common\Item\Iface::class, function( $item ) {
			return true;
		} );

		$this->mock->addFilter( \Aimeos\MShop\Common\Item\ListRef\Iface::class, function( $item ) {
			return false;
		} );

		$this->assertFalse( $this->access( 'filter' )->invokeArgs( $this->mock, [$this->item] ) );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Common\Manager\Base::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
