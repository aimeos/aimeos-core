<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Price_List_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Price_List_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Price_List_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.list.domain' => 'customer' ) ) ) ),
			'sort' => 'price.list.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );
		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 3, $result['total'] );
		$this->assertEquals( 'customer', $result['items'][0]->{'price.list.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Customer']['items'] ) );
		$this->assertEquals( 'UTC001', $result['graph']['Customer']['items'][0]->{'customer.code'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1, 'price.list.type.domain' => 'customer' );
		$priceManager = new Controller_ExtJS_Price_Default( TestHelper::getContext() );
		$result = $priceManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.list.type.domain' => 'customer' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$priceListTypeManager = Controller_ExtJS_Price_List_Type_Factory::createController( TestHelper::getContext() );
		$resultType = $priceListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'price.list.parentid' => $result['items'][0]->{'price.id'},
				'price.list.typeid' => $resultType['items'][0]->{'price.list.type.id'},
				'price.list.domain' => 'customer',
				'price.list.refid' => -1,
				'price.list.datestart' => '2000-01-01 00:00:00',
				'price.list.dateend' => '2001-01-01 00:00:00',
				'price.list.config' => array('test' => 'unit'),
				'price.list.position' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.list.refid' => -1 ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'price.list.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'price.list.id'} );
		$this->assertEquals( $saved['items']->{'price.list.id'}, $searched['items'][0]->{'price.list.id'});
		$this->assertEquals( $saved['items']->{'price.list.parentid'}, $searched['items'][0]->{'price.list.parentid'});
		$this->assertEquals( $saved['items']->{'price.list.typeid'}, $searched['items'][0]->{'price.list.typeid'});
		$this->assertEquals( $saved['items']->{'price.list.domain'}, $searched['items'][0]->{'price.list.domain'});
		$this->assertEquals( $saved['items']->{'price.list.refid'}, $searched['items'][0]->{'price.list.refid'});
		$this->assertEquals( $saved['items']->{'price.list.datestart'}, $searched['items'][0]->{'price.list.datestart'});
		$this->assertEquals( $saved['items']->{'price.list.dateend'}, $searched['items'][0]->{'price.list.dateend'});
		$this->assertEquals( $saved['items']->{'price.list.config'}, $searched['items'][0]->{'price.list.config'});
		$this->assertEquals( $saved['items']->{'price.list.position'}, $searched['items'][0]->{'price.list.position'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
