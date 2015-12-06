<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class MShop_Service_Provider_Decorator_DownloadTest extends \PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;
	private $_servItem;
	private $_mockProvider;


	protected function setUp()
	{
		$this->_context = \TestHelper::getContext();

		$servManager = MShop_Service_Manager_Factory::createManager( $this->_context );
		$this->_servItem = $servManager->createItem();

		$this->_mockProvider = $this->getMockBuilder( 'MShop_Service_Provider_Decorator_Example' )
			->disableOriginalConstructor()->getMock();

		$this->_object = new MShop_Service_Provider_Decorator_Download( $this->_context, $this->_servItem, $this->_mockProvider );
	}


	public function testGetConfigBE()
	{
		$result = $this->_object->getConfigBE();

		$this->assertArrayHasKey( 'download.all', $result );
	}


	public function testCheckConfigBEOK()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array( 'download.all' => '1' );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'null', $result['download.all'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array( 'download.all' => array() );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'string', $result['download.all'] );
	}


	public function testIsAvailableNoConfig()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_getOrderBaseItem() ) );
	}


	public function testIsAvailableOK()
	{
		$this->_servItem->setConfig( array( 'download.all' => '0' ) );

		$this->_mockProvider->expects( $this->once() )
			->method( 'isAvailable' )
			->will( $this->returnValue( true ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_getOrderBaseItem() ) );
	}


	public function testIsAvailableFailure()
	{
		$this->_servItem->setConfig( array( 'download.all' => '1' ) );

		$this->assertFalse( $this->_object->isAvailable( $this->_getOrderBaseItem() ) );
	}


	protected function _getOrderBaseItem()
	{
		$manager = MShop_Factory::createManager( $this->_context, 'order' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ) );
		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \Exception( 'No order item found' );
		}

		$baseManager = MShop_Factory::createManager( $this->_context, 'order/base' );
		return $baseManager->load( $item->getBaseId() );
	}
}