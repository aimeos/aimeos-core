<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14611 2011-12-28 13:09:10Z doleiynyk $
 */


class Controller_ExtJS_Price_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Price_DefaultTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new Controller_ExtJS_Price_Default( TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.quantity' => 1000 ) ) ) ),
			'sort' => 'price.quantity',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 1000, $result['items'][0]->{'price.quantity'} );
	}


	public function testSearchItemsWithReference()
	{
		$context = TestHelper::getContext();

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.quantity' => 1000 ) ) ) ),
			'sort' => 'price.quantity',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$price = $this->_object->searchItems( $params );

		// find refs
		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$productsList = $productManager->getSubManager('list');

		$search = $productsList->createSearch();

		$expr = array(
			$search->compare( '==', 'product.list.domain', 'price' ),
			$search->compare( '==', 'product.list.refid', $price['items'][0]->{'price.id'} ),

		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$sort = array( $search->sort( '+', 'product.list.id' ) );
		$search->setSortations( $sort );
		$search->setSlice(0, 1);

		$items = $productsList->searchItems( $search );

		if( ( $productItem = reset( $items ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$parentid = $productItem->getParentId();

		// search with refs
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.quantity' => 1000 ) ) ) ),
			'sort' => 'price.quantity',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
			'domain' => 'product',
			'parentid' => $parentid
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertTrue( $result['success'] );
		$this->assertEquals( $price['items'], $result['items'] );
	}


	public function testSaveDeleteItem()
	{
		$controller = Controller_ExtJS_Price_Type_Factory::createController( TestHelper::getContext() );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array(
				'&&' => array(
					(object) array( '==' => (object) array( 'price.type.domain' => 'product' ) ),
					(object) array( '==' => (object) array( 'price.type.code' => 'default' ) ),
				)
			)
		);

		$result = $controller->searchItems( $params );

		if( ( $priceItem = reset( $result['items'] ) ) === false ) {
			throw new Exception( 'No type item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'price.typeid' => $priceItem->{'price.type.id'},
				'price.domain' => 'product',
				'price.currencyid' => 'EUR',
				'price.quantity' => '10',
				'price.value' => '49.00',
				'price.shipping' => '5.00',
				'price.rebate' => '10.00',
				'price.taxrate' => '20.00',
				'price.status' => 0,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.value' => '49.00' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'price.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'price.id'} );
		$this->assertEquals( $saved['items']->{'price.id'}, $searched['items'][0]->{'price.id'} );
		$this->assertEquals( $saved['items']->{'price.typeid'}, $searched['items'][0]->{'price.typeid'} );
		$this->assertEquals( $saved['items']->{'price.domain'}, $searched['items'][0]->{'price.domain'} );
		$this->assertEquals( $saved['items']->{'price.currencyid'}, $searched['items'][0]->{'price.currencyid'} );
		$this->assertEquals( $saved['items']->{'price.quantity'}, $searched['items'][0]->{'price.quantity'} );
		$this->assertEquals( $saved['items']->{'price.value'}, $searched['items'][0]->{'price.value'} );
		$this->assertEquals( $saved['items']->{'price.shipping'}, $searched['items'][0]->{'price.shipping'} );
		$this->assertEquals( $saved['items']->{'price.rebate'}, $searched['items'][0]->{'price.rebate'} );
		$this->assertEquals( $saved['items']->{'price.taxrate'}, $searched['items'][0]->{'price.taxrate'} );
		$this->assertEquals( $saved['items']->{'price.status'}, $searched['items'][0]->{'price.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testGetServiceDescription()
	{
		$expected = array(
			'Price.deleteItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Price.saveItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Price.searchItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "condition", "optional" => true ),
					array( "type" => "integer", "name" => "start", "optional" => true ),
					array( "type" => "integer", "name" => "limit", "optional" => true ),
					array( "type" => "string", "name" => "sort", "optional" => true ),
					array( "type" => "string", "name" => "dir", "optional" => true ),
					array( "type" => "string", "name" => "domain", "optional" => true ),
					array( "type" => "string", "name" => "label", "optional" => true ),
					array( "type" => "integer", "name" => "parentid", "optional" => true ),
				),
				"returns" => "array",
			),
		);

		$actual = $this->_object->getServiceDescription();

		$this->assertEquals( $expected, $actual );
	}

}
