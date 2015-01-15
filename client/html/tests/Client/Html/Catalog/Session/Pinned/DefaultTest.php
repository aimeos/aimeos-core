<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Catalog_Session_Pinned_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$paths = TestHelper::getHtmlTemplatePaths();

		$this->_object = new Client_Html_Catalog_Session_Pinned_Default( $this->_context, $paths );
		$this->_object->setView( TestHelper::getView() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_context->getSession()->set( 'arcavias/catalog/session/pinned/list', null );
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$pinned = array( $this->_getProductItem( 'CNC' )->getId() );
		$this->_context->getSession()->set( 'arcavias/catalog/session/pinned/list', $pinned );

		$output = $this->_object->getBody();

		$this->assertRegExp( '#.*Cafe Noire Cappuccino.*#smU', $output );
		$this->assertStringStartsWith( '<section class="catalog-session-pinned">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcessAdd()
	{
		$prodId = $this->_getProductItem( 'CNE' )->getId();

		$view = $this->_object->getView();
		$param = array(
			'pin_action' => 'add',
			'pin_id' => $prodId,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();

		$pinned = $this->_context->getSession()->get( 'arcavias/catalog/session/pinned/list' );
		$this->assertEquals( array( $prodId => $prodId ), $pinned );
	}


	public function testProcessDelete()
	{
		$prodId = $this->_getProductItem( 'CNE' )->getId();
		$this->_context->getSession()->set( 'arcavias/catalog/session/pinned/list', array( $prodId => $prodId ) );

		$view = $this->_object->getView();
		$param = array(
			'pin_action' => 'delete',
			'pin_id' => $prodId,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();

		$pinned = $this->_context->getSession()->get( 'arcavias/catalog/session/pinned/list' );
		$this->assertEquals( array(), $pinned );
	}


	/**
	 * Returns the product for the given code.
	 *
	 * @param string $code Unique product code
	 * @throws Exception If no product is found
	 * @return MShop_Product_Item_Interface
	 */
	protected function _getProductItem( $code )
	{
		$manager = MShop_Factory::createManager( $this->_context, 'product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
