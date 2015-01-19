<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
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

		$this->assertContains( '<fieldset class="attr-size">', $output );
		$this->assertContains( '<fieldset class="attr-length">', $output );
		$this->assertContains( '<fieldset class="attr-width">', $output );
		$this->assertContains( '<fieldset class="attr-color">', $output );

		$this->assertEquals( 2, count( $tags ) );
		$this->assertEquals( null, $expire );
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
