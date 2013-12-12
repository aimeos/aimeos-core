<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Product_List_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Product_List_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Product_List_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.list.type.code' => 'unittype2' ) ) ) ),
			'sort' => 'product.list.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'media', $result['items'][0]->{'product.list.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Media']['items'] ) );
		$this->assertEquals( 'cn_colombie_266x221', $result['graph']['Media']['items'][0]->{'media.label'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1 );
		$productManager = new Controller_ExtJS_Product_Default( TestHelper::getContext() );
		$result = $productManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.list.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$productListTypeManager = Controller_ExtJS_Product_List_Type_Factory::createController( TestHelper::getContext() );
		$resultType = $productListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'product.list.parentid' => $result['items'][0]->{'product.id'},
				'product.list.typeid' => $resultType['items'][0]->{'product.list.type.id'},
				'product.list.domain' => 'text',
				'product.list.refid' => -1,
				'product.list.datestart' => '2000-01-01 00:00:00',
				'product.list.dateend' => '2001-01-01 00:00:00',
				'product.list.position' => 1,
				'product.list.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.list.refid' => -1 ) ) ) )
		);


		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );
		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.list.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.list.id'} );
		$this->assertEquals( $saved['items']->{'product.list.id'}, $searched['items'][0]->{'product.list.id'});
		$this->assertEquals( $saved['items']->{'product.list.parentid'}, $searched['items'][0]->{'product.list.parentid'});
		$this->assertEquals( $saved['items']->{'product.list.typeid'}, $searched['items'][0]->{'product.list.typeid'});
		$this->assertEquals( $saved['items']->{'product.list.domain'}, $searched['items'][0]->{'product.list.domain'});
		$this->assertEquals( $saved['items']->{'product.list.refid'}, $searched['items'][0]->{'product.list.refid'});
		$this->assertEquals( $saved['items']->{'product.list.datestart'}, $searched['items'][0]->{'product.list.datestart'});
		$this->assertEquals( $saved['items']->{'product.list.dateend'}, $searched['items'][0]->{'product.list.dateend'});
		$this->assertEquals( $saved['items']->{'product.list.position'}, $searched['items'][0]->{'product.list.position'});
		$this->assertEquals( $saved['items']->{'product.list.status'}, $searched['items'][0]->{'product.list.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
