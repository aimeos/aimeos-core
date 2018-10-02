<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Service\Provider\Payment;


class BaseTest extends \PHPUnit\Framework\TestCase
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


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCheckConfigBE()
	{
		$result = $this->object->checkConfigBE( array( 'payment.url-success' => true ) );

		$this->assertEquals( 0, count( $result ) );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertEquals( 0, count( $result ) );
		$this->assertInternalType( 'array', $result );
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


	public function testRepay()
	{
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->setExpectedException( '\\Aimeos\\MShop\\Service\\Exception' );
		$this->object->repay( $item );
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
