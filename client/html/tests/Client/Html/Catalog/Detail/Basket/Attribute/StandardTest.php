<?php

namespace Aimeos\Client\Html\Catalog\Detail\Basket\Attribute;


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
		$paths = \TestHelper::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Basket\Attribute\Standard( \TestHelper::getContext(), $paths );
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
		$view->detailProductItem = $this->getProductItem();

		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->detailProductItem = $this->getProductItem();
		$view->detailProductAttributeItems = $view->detailProductItem->getRefItems( 'attribute', null, 'config' );

		$configAttr = $view->detailProductItem->getRefItems( 'attribute', null, 'config' );
		$hiddenAttr = $view->detailProductItem->getRefItems( 'attribute', null, 'hidden' );

		$this->assertGreaterThan( 0, count( $configAttr ) );
		$this->assertGreaterThan( 0, count( $hiddenAttr ) );

		$output = $this->object->getBody();
		$this->assertStringStartsWith( '<div class="catalog-detail-basket-attribute', $output );

		foreach( $configAttr as $id => $item ) {
			$this->assertRegexp( '#<option class="select-option" value="' . $id . '">#', $output );
		}

		foreach( $hiddenAttr as $id => $item ) {
			$this->assertRegexp( '#<input type="hidden" .* value="' . $id . '" />#', $output );
		}
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	protected function getProductItem()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelper::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TESTP' ) );
		$items = $manager->searchItems( $search, array( 'attribute' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'No product item with code "U:TESTP" found' );
		}

		return $item;
	}
}
