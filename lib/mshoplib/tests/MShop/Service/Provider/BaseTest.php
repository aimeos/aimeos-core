<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Service\Provider;


/**
 * Test class for \Aimeos\MShop\Service\Provider\Base.
 */
class BaseTest extends \PHPUnit_Framework_TestCase
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
		$this->context = \TestHelper::getContext();
		$serviceItem = \Aimeos\MShop\Service\Manager\Factory::createManager( $this->context )->createItem();

		$this->object = new TestBase( $this->context, $serviceItem );
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
		$item = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context )->createItem();

		$this->setExpectedException( '\\Aimeos\\MShop\\Service\\Exception' );
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


class TestBase extends \Aimeos\MShop\Service\Provider\Base
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

	public function setConfigFE( \Aimeos\MShop\Order\Item\Base\Service\Iface $orderServiceItem, array $attributes )
	{
	}
}
