<?php

namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
			throw new \Exception( 'No order base item found' );
		}

		$this->mock = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Payment\\PrePay' )
			->setConstructorArgs( array( $this->context, $item ) )
			->setMethods( array( 'calcPrice', 'checkConfigBE', 'checkConfigFE', 'getConfigBE',
				'getConfigFE', 'injectGlobalConfigBE', 'isAvailable', 'isImplemented', 'query',
				'setCommunication', 'setConfigFE', 'updateAsync', 'updateSync' ) )
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
		$this->mock->expects( $this->once() )->method( 'checkConfigBE' )->will( $this->returnValue( array() ) );

		$this->assertEquals( array(), $this->object->checkConfigBE( array() ) );
	}


	public function testCheckConfigFE()
	{
		$this->mock->expects( $this->once() )->method( 'checkConfigFE' )->will( $this->returnValue( array() ) );

		$this->assertEquals( array(), $this->object->checkConfigFE( array() ) );
	}


	public function testGetConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( array() ) );

		$this->assertEquals( array(), $this->object->getConfigBE() );
	}


	public function testGetConfigFE()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->getSubManager( 'base' )->createItem();

		$this->mock->expects( $this->once() )->method( 'getConfigFE' )->will( $this->returnValue( array() ) );

		$this->assertEquals( array(), $this->object->getConfigFE( $item ) );
	}


	public function testInjectGlobalConfigBE()
	{
		$this->mock->expects( $this->once() )->method( 'injectGlobalConfigBE' );

		$this->object->injectGlobalConfigBE( array() );
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


	public function testQuery()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->mock->expects( $this->once() )->method( 'query' );

		$this->object->query( $item );
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

		$this->object->setConfigFE( $item, array() );
	}


	public function testUpdateAsync()
	{
		$this->mock->expects( $this->once() )->method( 'updateAsync' );

		$this->object->updateAsync();
	}


	public function testUpdateSync()
	{
		$this->mock->expects( $this->once() )->method( 'updateSync' );

		$response = null; $header = array();
		$this->object->updateSync( array(), 'body', $response, $header );
	}


	public function testCallInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Service\\Exception' );
		$this->object->invalid();
	}
}


class TestBase extends \Aimeos\MShop\Service\Provider\Decorator\Base
{

}
