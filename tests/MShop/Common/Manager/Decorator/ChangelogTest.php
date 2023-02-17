<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 */


namespace Aimeos\MShop\Common\Manager\Decorator;


class ChangelogTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $logger;
	private $mock;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->logger = $this->getMockBuilder( '\Aimeos\Base\Logger\Errorlog' )
			->setConstructorArgs( [\Aimeos\Base\Logger\Iface::NOTICE] )
			->onlyMethods( ['notice'] )
			->getMock();

		$this->mock = $this->getMockBuilder( '\Aimeos\MShop\Product\Manager\Standard' )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( ['delete', 'save'] )
			->getMock();

		$this->context->setLogger( $this->logger );

		$this->object = new \Aimeos\MShop\Common\Manager\Decorator\Changelog( $this->mock, $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->mock, $this->context );
	}


	public function testDelete()
	{
		$item = $this->mock->find( 'U:TESTP' );

		$this->mock->expects( $this->once() )->method( 'delete' );
		$this->logger->expects( $this->once() )->method( 'notice' );

		$this->assertEquals( $this->object, $this->object->delete( $item ) );
	}


	public function testSave()
	{
		$this->context->config()->set( 'mshop/common/manager/maxdepth', 0 );

		$item = $this->mock->find( 'U:TESTP' )->setId( null );

		$this->mock->expects( $this->once() )->method( 'save' )->will( $this->returnArgument( 0 ) );
		$this->logger->expects( $this->once() )->method( 'notice' );

		$this->assertEquals( $item, $this->object->save( $item ) );
	}
}
