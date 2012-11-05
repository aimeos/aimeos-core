<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 1124 2012-08-14 15:47:22Z doleiynyk $
 */


class Controller_ExtJS_Price_List_Type_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Price_List_Type_DefaultTest' );
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
		$this->object = new Controller_ExtJS_Price_List_Type_Default( TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.list.type.code' => 'default' ) ) ) ),
			'sort' => 'price.list.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'default', $result['items'][0]->{'price.list.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'price.list.type.code' => 'test',
				'price.list.type.label' => 'testLabel',
				'price.list.type.domain' => 'price',
				'price.list.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.list.type.code' => 'test' ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'price.list.type.id'} );
		$this->object->deleteItems( $params );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'price.list.type.id'} );
		$this->assertEquals( $saved['items']->{'price.list.type.id'}, $searched['items'][0]->{'price.list.type.id'} );
		$this->assertEquals( $saved['items']->{'price.list.type.code'}, $searched['items'][0]->{'price.list.type.code'} );
		$this->assertEquals( $saved['items']->{'price.list.type.domain'}, $searched['items'][0]->{'price.list.type.domain'} );
		$this->assertEquals( $saved['items']->{'price.list.type.label'}, $searched['items'][0]->{'price.list.type.label'} );
		$this->assertEquals( $saved['items']->{'price.list.type.status'}, $searched['items'][0]->{'price.list.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
