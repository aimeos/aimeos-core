<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Product_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Product_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Product_Default( TestHelper::getContext() );
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
		MShop_Factory::clear();
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '~=' => (object) array( 'product.label' => 'Cafe' ) ) ) ),
			'sort' => 'product.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( 'unitSupplier', $result['items'][0]->{'product.suppliercode'} );
	}


	public function testSaveDeleteItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$typeManager = $productManager->getSubManager( 'type' );

		$search = $typeManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.type.code', 'default' ) );
		$result = $typeManager->searchItems( $search );

		if( ( $type = reset( $result ) ) === false ) {
			throw new Exception( 'No product type found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'product.code' => 'test',
				'product.label' => 'test product',
				'product.status' => 1,
				'product.datestart' => '2000-01-01 00:00:00',
				'product.dateend' => '2001-01-01 00:00:00',
				'product.suppliercode' => '',
				'product.typeid' => $type->getId(),
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'product.code' => 'test' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );


		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.id'} );
		$this->assertEquals( $saved['items']->{'product.id'}, $searched['items'][0]->{'product.id'} );
		$this->assertEquals( $saved['items']->{'product.code'}, $searched['items'][0]->{'product.code'} );
		$this->assertEquals( $saved['items']->{'product.label'}, $searched['items'][0]->{'product.label'} );
		$this->assertEquals( $saved['items']->{'product.status'}, $searched['items'][0]->{'product.status'} );
		$this->assertEquals( $saved['items']->{'product.datestart'}, $searched['items'][0]->{'product.datestart'} );
		$this->assertEquals( $saved['items']->{'product.dateend'}, $searched['items'][0]->{'product.dateend'} );
		$this->assertEquals( $saved['items']->{'product.suppliercode'}, $searched['items'][0]->{'product.suppliercode'} );
		$this->assertEquals( $saved['items']->{'product.typeid'}, $searched['items'][0]->{'product.typeid'} );

		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testFinish()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );
		$result = $productManager->searchItems( $search );

		$this->_object->finish( (object) array( 'site' => 'unittest', 'items' => array_keys( $result ) ) );
	}
}
