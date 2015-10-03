<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Service_Provider_Base.
 */
class MShop_Service_Provider_BaseTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = TestHelper::getContext();
		$serviceItem = MShop_Service_Manager_Factory::createManager( $this->context )->createItem();

		$this->object = new Test_MShop_Service_Provider_Base( $this->context, $serviceItem );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCalcDateLimit()
	{
		$this->assertEquals( '2013-10-15', $this->object->calcDateLimitPublic( 1382100000, 3 ) );
	}


	public function testCalcDateLimitWeekdays()
	{
		$this->assertEquals( '2013-10-18', $this->object->calcDateLimitPublic( 1382186400, 0, true ) );
		$this->assertEquals( '2013-10-18', $this->object->calcDateLimitPublic( 1382272800, 0, true ) );
	}


	public function testCalcDateLimitHolidays()
	{
		$this->assertEquals( '2013-10-16', $this->object->calcDateLimitPublic( 1382100000, 0, false, '2013-10-17, 2013-10-18' ) );
	}


	public function testCheckConfigBE()
	{
		$this->assertEquals( array(), $this->object->checkConfigBE( array() ) );
	}


	public function testGetConfigValue()
	{
		$this->object->injectGlobalConfigBE( array( 'payment.url-success' => 'https://url.to/ok' ) );
		$this->assertEquals( 'https://url.to/ok', $this->object->getConfigValuePublic( array( 'payment.url-success' ) ) );
	}


	public function testQuery()
	{
		$item = MShop_Order_Manager_Factory::createManager( $this->context )->createItem();

		$this->setExpectedException( 'MShop_Service_Exception' );
		$this->object->query( $item );
	}


	public function testUpdateAsync()
	{
		$this->assertFalse( $this->object->updateAsync() );
	}


	public function testUpdateSync()
	{
		$response = null; $header = array();
		$result = $this->object->updateSync( array(), 'body', $response, $header );

		$this->assertEquals( null, $result );
	}
}


class Test_MShop_Service_Provider_Base extends MShop_Service_Provider_Base
{
	/**
	 * @param integer $ts
	 */
	public function calcDateLimitPublic( $ts, $days = 0, $bd = false, $hd = '' )
	{
		return $this->calcDateLimit( $ts, $days, $bd, $hd );
	}

	public function getConfigValuePublic( array $keys )
	{
		return $this->getConfigValue( $keys );
	}

	public function setConfigFE( MShop_Order_Item_Base_Service_Interface $orderServiceItem, array $attributes )
	{
	}
}
