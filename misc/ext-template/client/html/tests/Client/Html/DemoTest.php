<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_DemoTest extends PHPUnit_Framework_TestCase
{
	private $_context;
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$paths = TestHelper::getHtmlTemplatePaths();

		// $this->_object = new Client_Html_..._Default( $this->_context, $paths );
		// $this->_object->setView( TestHelper::getView() );
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

		Controller_Frontend_Factory::clear();
		MShop_Factory::clear();
	}


	public function testDemo()
	{
		$this->markTestIncomplete( 'Just a demo' );
	}
}