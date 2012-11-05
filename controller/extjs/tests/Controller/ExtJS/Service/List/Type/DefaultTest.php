<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php
 */


class Controller_ExtJS_Service_List_Type_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Service_List_Type_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Service_List_Type_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.list.type.code' => 'unittype2' ) ) ) ),
			'sort' => 'service.list.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'unittype2', $result['items'][0]->{'service.list.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'service.list.type.code' => 'test',
				'service.list.type.label' => 'testLabel',
				'service.list.type.domain' => 'service',
				'service.list.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.list.type.code' => 'test' ) ) ) )
		);


		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'service.list.type.id'} );
		$this->_object->deleteItems( $params );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'service.list.type.id'} );
		$this->assertEquals( $saved['items']->{'service.list.type.id'}, $searched['items'][0]->{'service.list.type.id'} );
		$this->assertEquals( $saved['items']->{'service.list.type.code'}, $searched['items'][0]->{'service.list.type.code'} );
		$this->assertEquals( $saved['items']->{'service.list.type.domain'}, $searched['items'][0]->{'service.list.type.domain'} );
		$this->assertEquals( $saved['items']->{'service.list.type.label'}, $searched['items'][0]->{'service.list.type.label'} );
		$this->assertEquals( $saved['items']->{'service.list.type.status'}, $searched['items'][0]->{'service.list.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
