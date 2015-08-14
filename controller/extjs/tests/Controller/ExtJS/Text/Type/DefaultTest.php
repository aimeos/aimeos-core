<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Text_Type_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->_object = Controller_ExtJS_Text_Type_Factory::createController( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'text.type.domain' => 'attribute' ) ) ) ),
			'sort' => 'text.type.code',
			'dir' => 'DESC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 5, $result['total'] );
		$this->assertEquals( 'url', $result['items'][0]->{'text.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'text.type.code' => 'cntltest',
				'text.type.label' => 'testLabel',
				'text.type.domain' => 'text',
				'text.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => array( 'text.type.code' => 'cntltest' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'text.type.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'text.type.id'} );
		$this->assertEquals( $saved['items']->{'text.type.id'}, $searched['items'][0]->{'text.type.id'} );
		$this->assertEquals( $saved['items']->{'text.type.code'}, $searched['items'][0]->{'text.type.code'} );
		$this->assertEquals( $saved['items']->{'text.type.domain'}, $searched['items'][0]->{'text.type.domain'} );
		$this->assertEquals( $saved['items']->{'text.type.label'}, $searched['items'][0]->{'text.type.label'} );
		$this->assertEquals( $saved['items']->{'text.type.status'}, $searched['items'][0]->{'text.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
