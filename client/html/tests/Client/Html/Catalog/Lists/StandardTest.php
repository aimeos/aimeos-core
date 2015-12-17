<?php

namespace Aimeos\Client\Html\Catalog\Lists;


/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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
		$this->context = \TestHelperHtml::getContext();

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $this->context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );
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
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $this->getCatalogItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->object->getHeader( 1, $tags, $expire );

		$this->assertStringStartsWith( '	<title>Kaffee</title>', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 4, count( $tags ) );
	}


	public function testGetHeaderSearch()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_search' => '<b>Search result</b>' ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->object->getHeader( 1, $tags, $expire );

		$this->assertRegexp( '#<title>[^>]*Search result[^<]*</title>#', $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $this->getCatalogItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<section class="aimeos catalog-list home categories coffee">', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 4, count( $tags ) );
	}


	public function testGetBodyNoDefaultCat()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array() );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos catalog-list">', $output );
		$this->assertNotRegExp( '#.*U:TESTPSUB01.*#smu', $output );
		$this->assertNotRegExp( '#.*U:TESTSUB03.*#smu', $output );
		$this->assertNotRegExp( '#.*U:TESTSUB04.*#smu', $output );
		$this->assertNotRegExp( '#.*U:TESTSUB05.*#smu', $output );
	}


	public function testGetBodyDefaultCat()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'client/html/catalog/lists/catid-default', $this->getCatalogItem()->getId() );

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array() );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos catalog-list home categories coffee">', $output );
	}


	public function testGetBodyCategoryLevels()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'client/html/catalog/lists/levels', \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE );

		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Standard( $context, $paths );
		$this->object->setView( \TestHelperHtml::getView() );

		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $this->getCatalogItem( 'root' )->getId() ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertRegExp( '#.*Cafe Noire Cappuccino.*#smu', $output );
		$this->assertRegExp( '#.*Cafe Noire Expresso.*#smu', $output );
		$this->assertRegExp( '#.*Unittest: Bundle.*#smu', $output );
		$this->assertRegExp( '#.*Unittest: Test priced Selection.*#smu', $output );
	}


	public function testGetBodySearchText()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_search' => 'Kaffee' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos catalog-list">', $output );
	}


	public function testGetBodySearchAttribute()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_attrid' => array( -1, -2 ) ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos catalog-list">', $output );
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'items', 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\Client\\HTML\\Iface', $client );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	protected function getCatalogItem( $code = 'cafe' )
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', $code ) );
		$items = $catalogManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( sprintf( 'No catalog item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
