<?php

namespace Aimeos\Client\Html\Catalog\Filter\Attribute;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Filter\Attribute\Standard( \TestHelper::getContext(), $paths );
		$this->object->setView( \TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$tags = array();
		$expire = null;
		$output = $this->object->getHeader( 1, $tags, $expire );

		$this->assertNotNull( $output );
		$this->assertEquals( 2, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testGetBody()
	{
		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$regex = '/<fieldset class="attr-color">.*<fieldset class="attr-length">.*<fieldset class="attr-size">.*<fieldset class="attr-width">/smu';
		$this->assertRegexp( $regex, $output );

		$this->assertEquals( 2, count( $tags ) );
		$this->assertEquals( null, $expire );
	}


	public function testGetBodyAttributeOrder()
	{
		$view = $this->object->getView();

		$conf = new \Aimeos\MW\Config\PHPArray();
		$conf->set( 'client/html/catalog/filter/attribute/types', array( 'color', 'width', 'length' ) );
		$helper = new \Aimeos\MW\View\Helper\Config\Standard( $view, $conf );
		$view->addHelper( 'config', $helper );

		$output = $this->object->getBody();
		$regex = '/<fieldset class="attr-color">.*<fieldset class="attr-width">.*<fieldset class="attr-length">/smu';

		$this->assertNotContains( '<fieldset class="attr-size">', $output );
		$this->assertRegexp( $regex, $output );
	}


	public function testGetBodyCategory()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, array( 'f_catid' => -1 ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetBodySearchText()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, array( 'f_search' => 'test' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetBodySearchAttribute()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Parameter\Standard( $view, array( 'f_attrid' => array( -1, -2 ) ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="catalog-filter-attribute">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}

}
