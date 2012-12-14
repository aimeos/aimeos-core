<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 1352 2012-10-29 16:11:47Z nsendetzky $
 */

class Client_Html_Basket_Standard_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;
	protected $_context;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('Client_Html_Basket_Standard_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


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
		$this->_object = new Client_Html_Basket_Standard_Default( $this->_context, $paths );
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
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$output = $this->_object->getHeader();
		$this->assertEquals( '', $output );
	}


	public function testGetBody()
	{
		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<div class="arcavias basket-standard">', $output );
	}


	public function testGetBodyAddSingle()
	{
		$view = $this->_object->getView();
		$param = array(
			'b-action' => 'add',
			'b-prod-id' => $this->_getProductItem( 'CNE' )->getId(),
			'b-quantity' => 1,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="main-price">18.00€</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-subtotal">.*<td class="main-price">18.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-shipping">.*<td class="main-price">1.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-total">.*<td class="main-price">19.00€</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyAddMulti()
	{
		$view = $this->_object->getView();
		$param = array(
			'b-action' => 'add',
			'b-prod' => array(
				array(
					'prod-id' => $this->_getProductItem( 'CNC' )->getId(),
					'quantity' => 1,
				),
				array(
					'prod-id' => $this->_getProductItem( 'CNE' )->getId(),
					'quantity' => 1,
				),
			),
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="main-price">36.00€</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tbody>.*<td class="main-price">600.00€</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-subtotal">.*<td class="main-price">636.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-shipping">.*<td class="main-price">32.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-total">.*<td class="main-price">668.00€</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyEditSingle()
	{
		$view = $this->_object->getView();
		$param = array(
			'b-action' => 'edit',
			'b-position' => 0,
			'b-quantity' => 1,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="main-price">18.00€</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-subtotal">.*<td class="main-price">618.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-shipping">.*<td class="main-price">31.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-total">.*<td class="main-price">649.00€</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyEditMulti()
	{
		$view = $this->_object->getView();
		$param = array(
			'b-action' => 'edit',
			'b-prod' => array(
				array(
					'position' => 0,
					'quantity' => 2,
				),
				array(
					'position' => 1,
					'quantity' => 1,
				),
			),
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="main-price">36.00€</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tbody>.*<td class="main-price">600.00€</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-subtotal">.*<td class="main-price">636.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-shipping">.*<td class="main-price">32.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-total">.*<td class="main-price">668.00€</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyDeleteSingle()
	{
		$view = $this->_object->getView();
		$param = array(
			'b-action' => 'delete',
			'b-position' => 1,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="main-price">36.00€</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-subtotal">.*<td class="main-price">36.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-shipping">.*<td class="main-price">2.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-total">.*<td class="main-price">38.00€</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyDeleteMulti()
	{
		$view = $this->_object->getView();
		$param = array(
			'b-action' => 'delete',
			'b-position' => 0,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tfoot>.*<tr class="main-subtotal">.*<td class="main-price">0.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-shipping">.*<td class="main-price">0.00€</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="main-total">.*<td class="main-price">0.00€</td>.*</tfoot>#smU', $output );
	}


	public function testGetSubClient()
	{
		$client = $this->_object->getSubClient( 'main', 'Default' );
		$this->assertInstanceOf( 'Client_HTML_Interface', $client );
	}


	public function testGetSubClientInvalid()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->setExpectedException( 'Client_Html_Exception' );
		$this->_object->getSubClient( '$$$', '$$$' );
	}


	protected function _getProductItem( $code )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
