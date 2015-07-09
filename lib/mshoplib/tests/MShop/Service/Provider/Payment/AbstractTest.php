<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class MShop_Service_Provider_Payment_AbstractTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;


	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$servManager = MShop_Service_Manager_Factory::createManager( $this->_context );
		$search = $servManager->createSearch();
		$search->setConditions($search->compare('==', 'service.provider', 'Default'));
		$result = $servManager->searchItems($search, array('price'));

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No order base item found' );
		}

		$this->_object = new MShop_Service_Provider_Payment_Test( $this->_context, $item );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCheckConfigBE()
	{
		$result = $this->_object->checkConfigBE( array( 'payment.url-success' => true ) );

		$this->assertInternalType( 'array', $result );
		$this->assertArrayHasKey( 'payment.url-success', $result );
	}


	public function testGetConfigBE()
	{
		$result = $this->_object->getConfigBE();

		$this->assertInternalType( 'array', $result );
		$this->assertArrayHasKey( 'payment.url-success', $result );
		$this->assertArrayHasKey( 'payment.url-failure', $result );
		$this->assertArrayHasKey( 'payment.url-cancel', $result );
		$this->assertArrayHasKey( 'payment.url-update', $result );
	}


	public function testCancel()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )->createItem();

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->_object->cancel( $item );
	}


	public function testCapture()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )->createItem();

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->_object->capture( $item );
	}

	public function testProcess()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )->createItem();

		$result = $this->_object->process( $item, array() );
		$this->assertInstanceOf( 'MShop_Common_Item_Helper_Form_Interface', $result );
	}


	public function testRefund()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )->createItem();

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->_object->refund( $item );
	}


	public function testSetConfigFE()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )
			->getSubManager( 'base' )->getSubManager( 'service' )->createItem();

		$this->_object->setConfigFE( $item, array() );
	}
}


class MShop_Service_Provider_Payment_Test extends MShop_Service_Provider_Payment_Abstract
{

}
