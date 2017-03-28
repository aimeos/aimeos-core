<?php

namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $mock;
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context );
		$search = $servManager->createSearch();
		$search->setConditions($search->compare('==', 'service.provider', 'Standard'));
		$result = $servManager->searchItems($search, array('price'));

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No order base item found' );
		}

		$this->mock = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Payment\\PrePay' )
			->setConstructorArgs( array( $this->context, $item ) )
			->setMethods( array( 'calcPrice', 'checkConfigBE', 'checkConfigFE', 'getConfigBE',
				'getConfigFE', 'injectGlobalConfigBE', 'isAvailable', 'isImplemented', 'query',
				'cancel', 'capture', 'process', 'refund', 'setCommunication', 'setConfigFE',
				'updateAsync', 'updateSync' ) )
			->getMock();

		$this->object = new TestBase( $this->mock, $this->context, $item );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCalcPrice()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->getSubManager( 'base' )->createItem();

		$this->mock->expects( $this->once() )->method( 'calcPrice' )->will( $this->returnValue( $item->getPrice() ) );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Price\\Item\\Iface', $this->object->calcPrice( $item ) );
	}


	public function testCheckConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'checkConfigBE' )->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->checkConfigBE( [] ) );
	}


	public function testCheckConfigFE()
	{
		$this->mock->expects( $this->once() )->method( 'checkConfigFE' )->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->checkConfigFE( [] ) );
	}


	public function testGetConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getConfigBE() );
	}


	public function testGetConfigFE()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->getSubManager( 'base' )->createItem();

		$this->mock->expects( $this->once() )->method( 'getConfigFE' )->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getConfigFE( $item ) );
	}


	public function testInjectGlobalConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'injectGlobalConfigBE' );

		$this->object->injectGlobalConfigBE( [] );
	}


	public function testIsAvailable()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->getSubManager( 'base' )->createItem();

		$this->mock->expects( $this->once() )->method( 'isAvailable' )->will( $this->returnValue( true ) );

		$this->assertEquals( true, $this->object->isAvailable( $item ) );

	}

	public function testIsImplemented()
	{
		$this->mock->expects( $this->once() )->method( 'isImplemented' )->will( $this->returnValue( true ) );

		$this->assertTrue( $this->object->isImplemented( \Aimeos\MShop\Service\Provider\Payment\Base::FEAT_QUERY ) );
	}


	public function testCancel()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->mock->expects( $this->once() )->method( 'cancel' );

		$this->object->cancel( $item );
	}


	public function testCapture()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->mock->expects( $this->once() )->method( 'capture' );

		$this->object->capture( $item );
	}


	public function testProcess()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->mock->expects( $this->once() )->method( 'process' );

		$this->object->process( $item, array( 'params' ) );
	}


	public function testQuery()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->mock->expects( $this->once() )->method( 'query' );

		$this->object->query( $item );
	}


	public function testRefund()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->mock->expects( $this->once() )->method( 'refund' );

		$this->object->refund( $item );
	}


	public function testSetCommunication()
	{
		$this->mock->expects( $this->once() )->method( 'setCommunication' );

		$this->object->setCommunication( new \Aimeos\MW\Communication\Curl() );
	}


	public function testSetConfigFE()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )
			->getSubManager( 'base' )->getSubManager( 'service' )->createItem();

		$this->mock->expects( $this->once() )->method( 'setConfigFE' );

		$this->object->setConfigFE( $item, [] );
	}


	public function testUpdateAsync()
	{
		$this->mock->expects( $this->once() )->method( 'updateAsync' );

		$this->object->updateAsync();
	}


	public function testUpdateSync()
	{
		$this->mock->expects( $this->once() )->method( 'updateSync' );

		$response = null; $header = [];
		$this->object->updateSync( [], 'body', $response, $header );
	}
}


class TestBase extends \Aimeos\MShop\Service\Provider\Decorator\Base
{

}
