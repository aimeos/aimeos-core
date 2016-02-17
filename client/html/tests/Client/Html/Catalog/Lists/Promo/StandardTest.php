<?php

namespace Aimeos\Client\Html\Catalog\Lists\Promo;


/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $catItem;
	private $object;
	private $view;


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
		$this->object = new \Aimeos\Client\Html\Catalog\Lists\Promo\Standard( $this->context, $paths );

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context );
		$search = $catalogManager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );
		$catItems = $catalogManager->searchItems( $search, array( 'product' ) );

		if( ( $this->catItem = reset( $catItems ) ) === false ) {
			throw new \Exception( 'No catalog item found' );
		}

		$this->view = \TestHelperHtml::getView();
		$this->view->listParams = array();
		$this->view->listCurrentCatItem = $this->catItem;
		$this->object->setView( $this->view );
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

		$this->assertStringStartsWith( '<script type="text/javascript"', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testGetBody()
	{
		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<section class="catalog-list-promo">', $output );
		$this->assertRegExp( '/.*Expresso.*Cappuccino.*/smu', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testGetBodyDefaultCatid()
	{
		unset( $this->view->listCurrentCatItem );
		$this->object->setView( $this->view );
		$this->context->getConfig()->set( 'client/html/catalog/lists/catid-default', $this->catItem->getId() );

		$output = $this->object->getBody();

		$this->assertStringStartsWith( '<section class="catalog-list-promo">', $output );
		$this->assertRegExp( '/.*Expresso.*Cappuccino.*/smu', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcess()
	{
		$this->object->process();
	}
}
