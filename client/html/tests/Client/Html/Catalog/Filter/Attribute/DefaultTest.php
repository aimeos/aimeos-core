<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Catalog_Filter_Attribute_DefaultTest extends MW_Unittest_Testcase
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
		$paths = TestHelper::getHtmlTemplatePaths();
		$this->_object = new Client_Html_Catalog_Filter_Attribute_Default( TestHelper::getContext(), $paths );
		$this->_object->setView( TestHelper::getView() );
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


	public function testGetHeader()
	{
		$tags = array();
		$expire = null;
		$output = $this->_object->getHeader( 1, $tags, $expire );

		$this->assertNotNull( $output );
		$this->assertEquals( 2, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testGetBody()
	{
		$tags = array();
		$expire = null;
		$output = $this->_object->getBody( 1, $tags, $expire );

		$regex = '/<fieldset class="attr-color">.*<fieldset class="attr-length">.*<fieldset class="attr-size">.*<fieldset class="attr-width">/smu';
		$this->assertRegexp( $regex, $output );

		$this->assertEquals( 2, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testGetBodyAttributeOrder()
	{
		$view = $this->_object->getView();

		$conf = new MW_Config_Array();
		$conf->set( 'client/html/catalog/filter/attribute/types', array( 'color', 'width', 'length' ) );
		$helper = new MW_View_Helper_Config_Default( $view, $conf );
		$view->addHelper( 'config', $helper );

		$output = $this->_object->getBody();
		$regex = '/<fieldset class="attr-color">.*<fieldset class="attr-width">.*<fieldset class="attr-length">/smu';

		$this->assertNotContains( '<fieldset class="attr-size">', $output );
		$this->assertRegexp( $regex, $output );
	}


	public function testGetBodyCategory()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f_catid' => -1 ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetBodySearchText()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f_search' => 'test' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetBodySearchAttribute()
	{
		$view = $this->_object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 'f_attrid' => array( -1, -2 ) ) );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}

}
