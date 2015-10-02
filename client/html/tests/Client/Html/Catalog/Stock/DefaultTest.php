<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Client_Html_Catalog_Stock_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->context = TestHelper::getContext();

		$paths = TestHelper::getHtmlTemplatePaths();
		$this->object = new Client_Html_Catalog_Stock_Default( $this->context, $paths );
		$this->object->setView( TestHelper::getView() );
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
		$productId = $this->getProductItem()->getId();

		$view = $this->object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 's_prodid' => $productId ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertRegExp( '/"' . $productId . '".*stock-high/', $output );
	}


	public function testGetBodyStockUnlimited()
	{
		$view = $this->object->getView();
		$helper = new MW_View_Helper_Parameter_Default( $view, array( 's_prodid' => -1 ) );
		$view->addHelper( 'param', $helper );

		$output = $this->object->getBody();
		$this->assertRegExp( '/"-1".*stock-unlimited/', $output );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	protected function getProductItem()
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No product item with code "CNC" found' );
		}

		return $item;
	}
}
