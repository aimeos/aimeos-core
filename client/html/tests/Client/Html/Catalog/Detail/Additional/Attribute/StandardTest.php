<?php

namespace Aimeos\Client\Html\Catalog\Detail\Additional\Attribute;


/**
 * @copyright Metaways Infosystems GmbH, 2014
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
		$paths = \TestHelperHtml::getHtmlTemplatePaths();
		$this->object = new \Aimeos\Client\Html\Catalog\Detail\Additional\Attribute\Standard( \TestHelperHtml::getContext(), $paths );
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
		$view->detailProductItem = $this->getProductItem();

		$tags = array();
		$expire = null;
		$output = $this->object->getHeader( 1, $tags, $expire );

		$this->assertNotNull( $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 2, count( $tags ) );
	}


	public function testGetBody()
	{
		$view = $this->object->getView();
		$view->detailProductItem = $this->getProductItem();

		$tags = array();
		$expire = null;
		$output = $this->object->getBody( 1, $tags, $expire );

		$this->assertContains( '<h2 class="header attributes">', $output );
		$this->assertContains( '<td class="name">size</td>', $output );
		$this->assertContains( '<span class="attr-name">XS</span>', $output );

		$this->assertEquals( null, $expire );
		$this->assertEquals( 2, count( $tags ) );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	protected function getProductItem()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperHtml::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNE' ) );
		$items = $manager->searchItems( $search, array( 'attribute', 'product' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'No product item with code "CNE" found' );
		}

		return $item;
	}
}
