<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class MShop_Service_Provider_Payment_BaseTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	protected function setUp()
	{
		$this->context = TestHelper::getContext();

		$servManager = MShop_Service_Manager_Factory::createManager( $this->context );
		$search = $servManager->createSearch();
		$search->setConditions($search->compare('==', 'service.provider', 'Standard'));
		$result = $servManager->searchItems($search, array('price'));

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order base item found' );
		}

		$this->object = new MShop_Service_Provider_Payment_Test( $this->context, $item );
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
		$item = MShop_Order_Manager_Factory::createManager( $this->context )->createItem();

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->object->cancel( $item );
	}


	public function testCapture()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->context )->createItem();

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->object->capture( $item );
	}

	public function testProcess()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->context )->createItem();

		$result = $this->object->process( $item, array() );
		$this->assertInstanceOf( 'MShop_Common_Item_Helper_Form_Iface', $result );
	}


	public function testRefund()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->context )->createItem();

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->object->refund( $item );
	}


	public function testSetConfigFE()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->context )
			->getSubManager( 'base' )->getSubManager( 'service' )->createItem();

		$this->object->setConfigFE( $item, array() );
	}
}


class MShop_Service_Provider_Payment_Test extends MShop_Service_Provider_Payment_Base
{

}
