<?php

namespace Aimeos\Client\Html\Catalog\Lists\Items;


/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
		$context = \TestHelper::getContext();

		$config = $context->getConfig();
		$config->set( 'client/html/catalog/list/basket-add', true );

		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Items\Standard( $context, $paths );

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$catItems = $catalogManager->searchItems( $search );

		if( ( $catItem = reset( $catItems ) ) === false ) {
			throw new \Exception( 'No catalog item found' );
		}

		$domains = array( 'media', 'price', 'text', 'attribute', 'product' );
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'U:TEST', 'U:BUNDLE' ) ) );
		$total = 0;


		$view = \TestHelper::getView( 'unittest', $config );

		$view->listProductItems = $productManager->searchItems( $search, $domains, $total );
		$view->listProductTotal = $total;
		$view->listPageSize = 100;
		$view->listPageCurr = 1;
		$view->listParams = array();
		$view->listCatPath = array( $catalogManager->createItem(), $catItem );

		$this->object->setView( $view );
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
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<div class="catalog-list-items">', $output );

		$this->assertContains( '<div class="price-item', $output );
		$this->assertContains( '<span class="quantity">', $output );
		$this->assertContains( '<span class="value">', $output );
		$this->assertContains( '<span class="costs">', $output );
		$this->assertContains( '<span class="taxrate">', $output );
	}


	public function testGetBodyCsrf()
	{
		$output = $this->object->getBody( 1 );
		$output = str_replace( '_csrf_value', '_csrf_new', $output );

		$this->assertContains( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_new" />', $output );

		$output = $this->object->modifyBody( $output, 1 );

		$this->assertContains( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_value" />', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
