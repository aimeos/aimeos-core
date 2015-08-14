<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MW_View_Helper_Content_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		$this->_object = new MW_View_Helper_Content_Default( $view, 'base/url' );
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


	public function testTransformRelativeUrlFromConfig()
	{
		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		$helper = new MW_View_Helper_Config_Default( $view, TestHelper::getContext()->getConfig() );
		$view->addHelper( 'config', $helper );

		$this->_object = new MW_View_Helper_Content_Default( $view );


		$output = $this->_object->transform( 'path/to/resource' );
		$this->assertEquals( '/path/to/resource', $output );
	}


	public function testTransformRelativeUrl()
	{
		$output = $this->_object->transform( 'path/to/resource' );
		$this->assertEquals( 'base/url/path/to/resource', $output );
	}


	public function testTransformAbsoluteUrl()
	{
		$output = $this->_object->transform( 'https://host:443/path/to/resource' );
		$this->assertEquals( 'https://host:443/path/to/resource', $output );
	}


	public function testTransformDataUrl()
	{
		$output = $this->_object->transform( 'data:image/gif;base64,R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs=' );
		$this->assertEquals( 'data:image/gif;base64,R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs=', $output );
	}
}
