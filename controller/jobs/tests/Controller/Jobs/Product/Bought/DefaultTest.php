<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_Jobs_Product_Bought_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;
	private $_aimeos;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$this->_context = TestHelper::getContext();
		$this->_aimeos = TestHelper::getAimeos();

		$this->_object = new Controller_Jobs_Product_Bought_Default( $this->_context, $this->_aimeos );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		MShop_Factory::setCache( false );
		MShop_Factory::clear();

		$this->_object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Products bought together', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Creates bought together product suggestions';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$stub = $this->getMockBuilder( 'MShop_Product_Manager_List_Default' )
			->setConstructorArgs( array( $this->_context ) )
			->setMethods( array( 'deleteItems', 'saveItem' ) )
			->getMock();

		MShop_Factory::injectManager( $this->_context, 'product/list', $stub );

		$stub->expects( $this->atLeastOnce() )->method( 'deleteItems' );
		$stub->expects( $this->atLeastOnce() )->method( 'saveItem' );

		$this->_object->run();
	}
}