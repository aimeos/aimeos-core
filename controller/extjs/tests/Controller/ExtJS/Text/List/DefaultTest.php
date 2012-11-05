<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14411 2011-12-17 14:02:37Z nsendetzky $
 */


class Controller_ExtJS_Text_List_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Text_List_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Text_List_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'text.list.type.code' => 'align-left' ) ) ) ),
			'sort' => 'text.list.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 4, $result['total'] );
		$this->assertEquals( 'media', $result['items'][0]->{'text.list.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Media']['items'] ) );
		$this->assertEquals( 'example image 1', $result['graph']['Media']['items'][0]->{'media.label'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1 );
		$textManager = new Controller_ExtJS_Text_Default( TestHelper::getContext() );
		$result = $textManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'text.list.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$textListTypeManager = Controller_ExtJS_Text_List_Type_Factory::createController( TestHelper::getContext() );
		$resultType = $textListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'text.list.parentid' => $result['items'][0]->{'text.id'},
				'text.list.typeid' => $resultType['items'][0]->{'text.list.type.id'},
				'text.list.domain' => 'text',
				'text.list.refid' => 123,
				'text.list.datestart' => '2000-01-01 00:00:00',
				'text.list.dateend' => '2001-01-01 00:00:00',
				'text.list.position' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'text.list.refid' => 123 ) ) ) )
		);


		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'text.list.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'text.list.id'} );
		$this->assertEquals( $saved['items']->{'text.list.id'}, $searched['items'][0]->{'text.list.id'});
		$this->assertEquals( $saved['items']->{'text.list.parentid'}, $searched['items'][0]->{'text.list.parentid'});
		$this->assertEquals( $saved['items']->{'text.list.typeid'}, $searched['items'][0]->{'text.list.typeid'});
		$this->assertEquals( $saved['items']->{'text.list.domain'}, $searched['items'][0]->{'text.list.domain'});
		$this->assertEquals( $saved['items']->{'text.list.refid'}, $searched['items'][0]->{'text.list.refid'});
		$this->assertEquals( $saved['items']->{'text.list.datestart'}, $searched['items'][0]->{'text.list.datestart'});
		$this->assertEquals( $saved['items']->{'text.list.dateend'}, $searched['items'][0]->{'text.list.dateend'});
		$this->assertEquals( $saved['items']->{'text.list.position'}, $searched['items'][0]->{'text.list.position'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
