<?php

namespace Aimeos\Client\Html\Catalog\Lists\Head;


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
		$context = \TestHelperHtml::getContext();
		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Head\Standard( $context, $paths );

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$catItems = $catalogManager->searchItems( $search, array( 'text' ) );

		if( ( $catItem = reset( $catItems ) ) === false ) {
			throw new \Exception( 'No catalog item found' );
		}

		$view = \TestHelperHtml::getView();
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

		$this->assertStringStartsWith( '<div class="catalog-list-head">', $output );
		$this->assertRegExp( '#<h1>Kaffee</h1>#', $output );
	}


	public function testGetBodySearch()
	{
		$view = $this->object->getView();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, array( 'f_search' => '<b>Search result</b>' ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertContains( '&lt;b&gt;Search result&lt;/b&gt;', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}
}
