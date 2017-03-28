<?php

namespace Aimeos\MShop\Service\Provider\Payment;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
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

		$this->object = new TestBase( $this->context, $item );
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


	public function testCheckConfigBE()
	{
		$result = $this->object->checkConfigBE( array( 'payment.url-success' => true ) );

		$this->assertInternalType( 'array', $result );
		$this->assertArrayHasKey( 'payment.url-success', $result );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertInternalType( 'array', $result );
		$this->assertArrayHasKey( 'payment.url-success', $result );
		$this->assertArrayHasKey( 'payment.url-failure', $result );
		$this->assertArrayHasKey( 'payment.url-cancel', $result );
		$this->assertArrayHasKey( 'payment.url-update', $result );
	}


	public function testCancel()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->setExpectedException( '\\Aimeos\\MShop\\Service\\Exception' );
		$this->object->cancel( $item );
	}


	public function testCapture()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->setExpectedException( '\\Aimeos\\MShop\\Service\\Exception' );
		$this->object->capture( $item );
	}

	public function testProcess()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$result = $this->object->process( $item, [] );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Item\\Helper\\Form\\Iface', $result );
	}


	public function testRefund()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->setExpectedException( '\\Aimeos\\MShop\\Service\\Exception' );
		$this->object->refund( $item );
	}


	public function testSetConfigFE()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )
			->getSubManager( 'base' )->getSubManager( 'service' )->createItem();

		$this->object->setConfigFE( $item, [] );
	}
}


class TestBase extends \Aimeos\MShop\Service\Provider\Payment\Base
{

}
