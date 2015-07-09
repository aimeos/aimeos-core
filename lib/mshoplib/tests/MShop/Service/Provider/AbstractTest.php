<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Service_Provider_Abstract.
 */
class MShop_Service_Provider_AbstractTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$serviceItem = MShop_Service_Manager_Factory::createManager( $this->_context )->createItem();

		$this->_object = new Test_MShop_Service_Provider_Abstract( $this->_context, $serviceItem );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testCalcDateLimit()
	{
		$this->assertEquals( '2013-10-15', $this->_object->calcDateLimit( 1382100000, 3 ) );
	}


	public function testCalcDateLimitWeekdays()
	{
		$this->assertEquals( '2013-10-18', $this->_object->calcDateLimit( 1382186400, 0, true ) );
		$this->assertEquals( '2013-10-18', $this->_object->calcDateLimit( 1382272800, 0, true ) );
	}


	public function testCalcDateLimitHolidays()
	{
		$this->assertEquals( '2013-10-16', $this->_object->calcDateLimit( 1382100000, 0, false, '2013-10-17, 2013-10-18' ) );
	}


	public function testCheckConfigBE()
	{
		$this->assertEquals( array(), $this->_object->checkConfigBE( array() ) );
	}


	public function testGetConfigValue()
	{
		$this->_object->injectGlobalConfigBE( array( 'payment.url-success' => 'https://url.to/ok' ) );
		$this->assertEquals( 'https://url.to/ok', $this->_object->getConfigValue( array( 'payment.url-success' ) ) );
	}


	public function testQuery()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->_context )->createItem();

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->_object->query( $item );
	}


	public function testUpdateAsync()
	{
		$this->assertFalse( $this->_object->updateAsync() );
	}


	public function testUpdateSync()
	{
		$response = null; $header = array();
		$result = $this->_object->updateSync( array(), 'body', $response, $header );

		$this->assertEquals( null, $result );
	}
}


class Test_MShop_Service_Provider_Abstract extends MShop_Service_Provider_Abstract
{
	/**
	 * @param integer $ts
	 */
	public function calcDateLimit( $ts, $days = 0, $bd = false, $hd = '' )
	{
		return $this->_calcDateLimit( $ts, $days, $bd, $hd );
	}

	public function getConfigValue( array $keys )
	{
		return $this->_getConfigValue( $keys );
	}

	public function setConfigFE( MShop_Order_Item_Base_Service_Interface $orderServiceItem, array $attributes )
	{
	}
}
