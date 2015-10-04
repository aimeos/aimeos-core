<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Client_Html_Catalog_Session_Pinned_StandardTest extends PHPUnit_Framework_TestCase
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

		$this->object = new Client_Html_Catalog_Session_Pinned_Standard( $this->context, $paths );
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
		$this->context->getSession()->set( 'aimeos/catalog/session/pinned/list', null );
		unset( $this->object );
	}


	public function testGetHeader()
	{
		$output = $this->object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$pinned = array( $this->getProductItem( 'CNC' )->getId() );
		$this->context->getSession()->set( 'aimeos/catalog/session/pinned/list', $pinned );

		$output = $this->object->getBody();

		$this->assertRegExp( '#.*Cafe Noire Cappuccino.*#smU', $output );
		$this->assertStringStartsWith( '<section class="catalog-session-pinned">', $output );
	}


	public function testGetSubClient()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcessAdd()
	{
		$prodId = $this->getProductItem( 'CNE' )->getId();

		$view = $this->object->getView();
		$param = array(
			'pin_action' => 'add',
			'pin_id' => $prodId,
		);

		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$pinned = $this->context->getSession()->get( 'aimeos/catalog/session/pinned/list' );
		$this->assertEquals( array( $prodId => $prodId ), $pinned );
	}


	public function testProcessDelete()
	{
		$prodId = $this->getProductItem( 'CNE' )->getId();
		$this->context->getSession()->set( 'aimeos/catalog/session/pinned/list', array( $prodId => $prodId ) );

		$view = $this->object->getView();
		$param = array(
			'pin_action' => 'delete',
			'pin_id' => $prodId,
		);

		$helper = new MW_View_Helper_Parameter_Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->process();

		$pinned = $this->context->getSession()->get( 'aimeos/catalog/session/pinned/list' );
		$this->assertEquals( array(), $pinned );
	}


	/**
	 * Returns the product for the given code.
	 *
	 * @param string $code Unique product code
	 * @throws Exception If no product is found
	 * @return MShop_Product_Item_Iface
	 */
	protected function getProductItem( $code )
	{
		$manager = MShop_Factory::createManager( $this->context, 'product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
