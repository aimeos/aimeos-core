<?php

namespace Aimeos\Client\Html\Catalog\Detail\Bought;


/**
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
		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Bought\Standard( \TestHelper::getContext(), $paths );
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

		$tags = array();
		$expire = null;
		$output = $this->object->getHeader( 1, $tags, $expire );

		$this->assertNotNull( $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->detailProductItem = $this->getProductItem();

		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertStringStartsWith( '<section class="catalog-detail-bought">', $output );
		$this->assertRegExp( '/.*Cappuccino.*/', $output );
		$this->assertEquals( '2022-01-01 00:00:00', $expire );
		$this->assertEquals( 1, count( $tags ) );
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
		$search->setConditions( $search->compare( '==', 'product.code', 'CNE' ) );
		$items = $manager->searchItems( $search, array( 'product' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'No product item with code "CNE" found' );
		}

		return $item;
	}
}
