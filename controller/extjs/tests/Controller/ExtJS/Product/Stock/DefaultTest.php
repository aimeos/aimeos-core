<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Product_Stock_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Product_Stock_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Product_Stock_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.stock.stocklevel' => 1000 ) ) ) ),
			'sort' => 'product.stock.stocklevel',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 1000, $result['items'][0]->{'product.stock.stocklevel'} );
	}


	public function testSaveDeleteItem()
	{
		$ctx = TestHelper::getContext();

		$productManager = $manager = MShop_Product_Manager_Factory::createManager( $ctx );
		$warehouseManager = $productManager->getSubManager('stock')->getSubManager('warehouse');

		$search = $warehouseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.warehouse.code', 'default') );
		$search->setSlice( 0, 1 );
		$items = $warehouseManager->searchItems( $search );

		if( ( $warehouseItem = reset( $items ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '~=', 'product.label', 'Cheapest') );
		$items = $productManager->searchItems( $search );

		if( ( $productItem = reset( $items ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'product.stock.productid' => $productItem->getId(),
				'product.stock.warehouseid' => $warehouseItem->getId(),
				'product.stock.stocklevel' => 999,
				'product.stock.dateback' => '2000-01-01 00:00:01',
			),
		);
		$saved = $this->_object->saveItems( $saveParams );

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.stock.dateback' => '2000-01-01 00:00:01' ) ) ) )
		);
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.stock.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.stock.id'} );
		$this->assertEquals( $saved['items']->{'product.stock.id'}, $searched['items'][0]->{'product.stock.id'} );
		$this->assertEquals( $saved['items']->{'product.stock.productid'}, $searched['items'][0]->{'product.stock.productid'} );
		$this->assertEquals( $saved['items']->{'product.stock.warehouseid'}, $searched['items'][0]->{'product.stock.warehouseid'} );
		$this->assertEquals( $saved['items']->{'product.stock.stocklevel'}, $searched['items'][0]->{'product.stock.stocklevel'} );
		$this->assertEquals( $saved['items']->{'product.stock.dateback'}, $searched['items'][0]->{'product.stock.dateback'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
