<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14411 2011-12-17 14:02:37Z nsendetzky $
 */


class Controller_ExtJS_Attribute_List_Type_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite('Controller_ExtJS_Attribute_List_Type_DefaultTest');
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
		$this->_object = new Controller_ExtJS_Attribute_List_Type_Default( TestHelper::getContext() );
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
			'condition' => (object) array(
				'&&' => array(
					0 => (object) array( '==' => (object) array( 'attribute.list.type.code' => 'default' ) ),
					1 => (object) array( '==' => (object) array( 'attribute.list.type.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'attribute.list.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 7, $result['total'] );
		$this->assertEquals( 'default', $result['items'][0]->{'attribute.list.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'attribute.list.type.code' => 'test',
				'attribute.list.type.label' => 'testLabel',
				'attribute.list.type.domain' => 'attribute',
				'attribute.list.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'attribute.list.type.code' => 'test' ) ) ) )
		);


		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'attribute.list.type.id'} );
		$this->_object->deleteItems( $params );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'attribute.list.type.id'} );
		$this->assertEquals( $saved['items']->{'attribute.list.type.id'}, $searched['items'][0]->{'attribute.list.type.id'} );
		$this->assertEquals( $saved['items']->{'attribute.list.type.code'}, $searched['items'][0]->{'attribute.list.type.code'} );
		$this->assertEquals( $saved['items']->{'attribute.list.type.domain'}, $searched['items'][0]->{'attribute.list.type.domain'} );
		$this->assertEquals( $saved['items']->{'attribute.list.type.label'}, $searched['items'][0]->{'attribute.list.type.label'} );
		$this->assertEquals( $saved['items']->{'attribute.list.type.status'}, $searched['items'][0]->{'attribute.list.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
