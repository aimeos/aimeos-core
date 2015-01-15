<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Client_Html_Basket_Standard_DefaultTest extends MW_Unittest_Testcase
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
		Controller_Frontend_Basket_Factory::createController( $this->_context )->clear();
		unset( $this->_object );
	}


	public function testGetHeader()
	{
		$output = $this->_object->getHeader();
		$this->assertNotNull( $output );
	}


	public function testGetBody()
	{
		$output = $this->_object->getBody();
		$this->assertStringStartsWith( '<section class="aimeos basket-standard">', $output );
	}


	public function testGetBodyAddSingle()
	{
		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->_getProductItem( 'CNE' )->getId(),
			'b_quantity' => 1,
			'b_warehouse' => 'default',
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="price">18.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">18.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">1.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">19.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="tax">.*<td class="price">3.03 .+</td>.*.*</tfoot>#smU', $output );
	}


	public function testGetBodyAddMulti()
	{
		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prod' => array(
				array(
					'prodid' => $this->_getProductItem( 'CNC' )->getId(),
					'quantity' => 1,
					'warehouse' => 'default',
				),
				array(
					'prodid' => $this->_getProductItem( 'CNE' )->getId(),
					'quantity' => 1,
					'warehouse' => 'default',
				),
			),
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="price">18.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tbody>.*<td class="price">600.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">618.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">31.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">649.00 .+</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyAddVariantAttribute()
	{
		$attrManager = MShop_Attribute_Manager_Factory::createManager( $this->_context );

		$search = $attrManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->combine( '||', array(
				$search->combine( '&&', array(
					$search->compare( '==', 'attribute.code', '30' ),
					$search->compare( '==', 'attribute.type.code', 'length' ),
				) ),
				$search->combine( '&&', array(
					$search->compare( '==', 'attribute.code', '30' ),
					$search->compare( '==', 'attribute.type.code', 'width' ),
				) ),
			) ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$attributes = $attrManager->searchItems( $search, array() );

		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->_getProductItem( 'U:TEST' )->getId(),
			'b_quantity' => 1,
			'b_attrvarid' => array_keys( $attributes ),
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
		$output = $this->_object->getBody();

		$this->assertRegExp( '#<li class="attr-item">.*<span class="value">30</span>.*</li>.*<li class="attr-item">.*<span class="value">30</span>.*</li>#smU', $output );
	}


	public function testGetBodyAddConfigAttribute()
	{
		$attrManager = MShop_Attribute_Manager_Factory::createManager( $this->_context );

		$search = $attrManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'white' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'color' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attrManager->searchItems( $search, array() );

		if( ( $attribute = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute' );
		}

		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->_getProductItem( 'CNE' )->getId(),
			'b_quantity' => 1,
			'b_attrconfid' => $attribute->getId(),
			'b_warehouse' => 'default',
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
		$output = $this->_object->getBody();

		$this->assertRegExp( '#<li class="attr-item">.*<a class="change" href=[^>]*>.*<span class="value">weiß</span>.*</a>.*</li>#smU', $output );
	}


	public function testGetBodyAddHiddenAttribute()
	{
		$attrManager = MShop_Attribute_Manager_Factory::createManager( $this->_context );

		$search = $attrManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'm' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'size' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attrManager->searchItems( $search, array() );

		if( ( $attribute = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute' );
		}

		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $this->_getProductItem( 'CNE' )->getId(),
			'b_quantity' => 1,
			'b_attrhideid' => $attribute->getId(),
			'b_warehouse' => 'default',
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
		$output = $this->_object->getBody();

		$this->assertNotRegExp( '#<li class="attr-item">.*<span class="value">m</span>.*</li>#smU', $output );
	}


	public function testGetBodyEditSingle()
	{
		$this->_addProduct( 'CNE', 2, 'default' );

		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'edit',
			'b_position' => 0,
			'b_quantity' => 1,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="price">18.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">18.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">1.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">19.00 .+</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyEditMulti()
	{
		$this->_addProduct( 'CNE', 1, 'default' );
		$this->_addProduct( 'CNC', 2, 'default' );

		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'edit',
			'b_prod' => array(
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

		$this->_object->process();
		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="price">36.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tbody>.*<td class="price">600.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">636.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">32.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">668.00 .+</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyDeleteSingle()
	{
		$this->_addProduct( 'CNE', 2, 'default' );
		$this->_addProduct( 'CNC', 1, 'default' );

		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'delete',
			'b_position' => 1,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tbody>.*<td class="price">36.00 .+</td>.*</tbody>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">36.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">2.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">38.00 .+</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyDeleteMulti()
	{
		$this->_addProduct( 'CNE', 1, 'default' );
		$this->_addProduct( 'CNC', 1, 'default' );

		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'delete',
			'b_position' => array( 0, 1 ),
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
		$output = $this->_object->getBody();

		$this->assertRegExp( '#<tfoot>.*<tr class="subtotal">.*<td class="price">0.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="delivery">.*<td class="price">0.00 .+</td>.*</tfoot>#smU', $output );
		$this->assertRegExp( '#<tfoot>.*<tr class="total">.*<td class="price">0.00 .+</td>.*</tfoot>#smU', $output );
	}


	public function testGetBodyDeleteInvalid()
	{
		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'delete',
			'b_position' => -1,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();

		$this->assertEquals( 1, count( $view->get( 'standardErrorList', array() ) ) );
	}


	public function testGetSubClient()
	{
		$client = $this->_object->getSubClient( 'detail', 'Default' );
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


	/**
	 * @param string $code
	 * @param integer $quantity
	 * @param string $warehouse
	 */
	protected function _addProduct( $code, $quantity, $warehouse )
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		$view = $this->_object->getView();
		$param = array(
			'b_action' => 'add',
			'b_prodid' => $item->getId(),
			'b_quantity' => $quantity,
			'b_warehouse' => $warehouse,
		);

		$helper = new MW_View_Helper_Parameter_Default( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->_object->process();
	}


	/**
	 * @param string $code
	 */
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
