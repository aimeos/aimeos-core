<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Text_List_Type_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new Controller_ExtJS_Text_List_Type_Default( TestHelper::getContext() );
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
			'sort' => 'text.list.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'align-left', $result['items'][0]->{'text.list.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'text.list.type.code' => 'test',
				'text.list.type.label' => 'testLabel',
				'text.list.type.domain' => 'text',
				'text.list.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'text.list.type.code' => 'test' ) ) ) )
		);


		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'text.list.type.id'} );
		$this->_object->deleteItems( $params );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'text.list.type.id'} );
		$this->assertEquals( $saved['items']->{'text.list.type.id'}, $searched['items'][0]->{'text.list.type.id'} );
		$this->assertEquals( $saved['items']->{'text.list.type.code'}, $searched['items'][0]->{'text.list.type.code'} );
		$this->assertEquals( $saved['items']->{'text.list.type.domain'}, $searched['items'][0]->{'text.list.type.domain'} );
		$this->assertEquals( $saved['items']->{'text.list.type.label'}, $searched['items'][0]->{'text.list.type.label'} );
		$this->assertEquals( $saved['items']->{'text.list.type.status'}, $searched['items'][0]->{'text.list.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
