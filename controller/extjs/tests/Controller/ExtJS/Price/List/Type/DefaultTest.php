<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Price_List_Type_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Controller_ExtJS_Price_List_Type_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'price.list.type.domain' => 'customer' ) ) ) ),
			'sort' => 'price.list.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

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


		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'price.list.type.id'} );
		$this->_object->deleteItems( $params );
		$result = $this->_object->searchItems( $searchParams );

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
