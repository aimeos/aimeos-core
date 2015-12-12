<?php

namespace Aimeos\Client\Html\Catalog\Stage;


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
		$this->context = \TestHelper::getContext();

		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Stage\Standard( $this->context, $paths );
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
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $this->getCatalogItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->object->getHeader( 1, $tags, $expire );

		$this->assertNotNull( $output );
		$this->assertEquals( '2019-01-01 00:00:00', $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testGetBody()
	{
		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<section class="aimeos catalog-stage">', $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 0, count( $tags ) );
	}


	public function testGetBodyCatId()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_catid' => $this->getCatalogItem()->getId() ) );
		$view->addHelper( 'param', $helper );

		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<section class="aimeos catalog-stage home categories coffee">', $output );
		$this->assertEquals( '2019-01-01 00:00:00', $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testModifyBody()
	{
		$this->assertEquals( 'test', $this->object->modifyBody( 'test', 1 ) );
	}


	public function testModifyHeader()
	{
		$this->assertEquals( 'test', $this->object->modifyHeader( 'test', 1 ) );
	}


	public function testGetSubClient()
	{
		$client = $this->object->getSubClient( 'image', 'Standard' );
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


	protected function getCatalogItem()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$items = $catalogManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'No catalog item with code "cafe" found' );
		}

		return $item;
	}
}
