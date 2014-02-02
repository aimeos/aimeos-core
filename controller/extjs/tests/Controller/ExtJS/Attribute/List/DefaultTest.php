<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Attribute_List_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Attribute_List_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Attribute_List_Default( TestHelper::getContext() );
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
			'condition' => (object) array(
				'&&' => array(
					0 => (object) array( '==' => (object) array( 'attribute.list.domain' => 'media' ) ),
					1 => (object) array( '==' => (object) array( 'attribute.list.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'attribute.list.parentid',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 'media', $result['items'][0]->{'attribute.list.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Media']['items'] ) );
		$this->assertEquals( 'attribute', $result['graph']['Media']['items'][0]->{'media.domain'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1 );
		$productManager = new Controller_ExtJS_Attribute_Default( TestHelper::getContext() );
		$result = $productManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'attribute.list.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$attributeListTypeManager = Controller_ExtJS_Attribute_List_Type_Factory::createController( TestHelper::getContext() );
		$resultType = $attributeListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'attribute.list.parentid' => $result['items'][0]->{'attribute.id'},
				'attribute.list.typeid' => $resultType['items'][0]->{'attribute.list.type.id'},
				'attribute.list.domain' => 'text',
				'attribute.list.refid' => -1,
				'attribute.list.datestart' => '2000-01-01 00:00:00',
				'attribute.list.dateend' => '2000-01-01 00:00:00',
				'attribute.list.config' => array('test' => 'unit'),
				'attribute.list.position' => 1,
				'attribute.list.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'attribute.list.refid' => -1 ) ) ) )
		);


		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'attribute.list.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'attribute.list.id'} );
		$this->assertEquals( $saved['items']->{'attribute.list.id'}, $searched['items'][0]->{'attribute.list.id'});
		$this->assertEquals( $saved['items']->{'attribute.list.parentid'}, $searched['items'][0]->{'attribute.list.parentid'});
		$this->assertEquals( $saved['items']->{'attribute.list.typeid'}, $searched['items'][0]->{'attribute.list.typeid'});
		$this->assertEquals( $saved['items']->{'attribute.list.domain'}, $searched['items'][0]->{'attribute.list.domain'});
		$this->assertEquals( $saved['items']->{'attribute.list.refid'}, $searched['items'][0]->{'attribute.list.refid'});
		$this->assertEquals( $saved['items']->{'attribute.list.datestart'}, $searched['items'][0]->{'attribute.list.datestart'});
		$this->assertEquals( $saved['items']->{'attribute.list.dateend'}, $searched['items'][0]->{'attribute.list.dateend'});
		$this->assertEquals( $saved['items']->{'attribute.list.config'}, $searched['items'][0]->{'attribute.list.config'});
		$this->assertEquals( $saved['items']->{'attribute.list.position'}, $searched['items'][0]->{'attribute.list.position'});
		$this->assertEquals( $saved['items']->{'attribute.list.status'}, $searched['items'][0]->{'attribute.list.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
