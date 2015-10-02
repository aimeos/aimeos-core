<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_ExtJS_Supplier_List_Type_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new Controller_ExtJS_Supplier_List_Type_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.list.type.code' => 'default' ) ) ) ),
			'sort' => 'supplier.list.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'default', $result['items'][0]->{'supplier.list.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'supplier.list.type.code' => 'test',
				'supplier.list.type.label' => 'testLabel',
				'supplier.list.type.domain' => 'supplier',
				'supplier.list.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.list.type.code' => 'test' ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'supplier.list.type.id'} );
		$this->object->deleteItems( $params );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'supplier.list.type.id'} );
		$this->assertEquals( $saved['items']->{'supplier.list.type.id'}, $searched['items'][0]->{'supplier.list.type.id'} );
		$this->assertEquals( $saved['items']->{'supplier.list.type.code'}, $searched['items'][0]->{'supplier.list.type.code'} );
		$this->assertEquals( $saved['items']->{'supplier.list.type.domain'}, $searched['items'][0]->{'supplier.list.type.domain'} );
		$this->assertEquals( $saved['items']->{'supplier.list.type.label'}, $searched['items'][0]->{'supplier.list.type.label'} );
		$this->assertEquals( $saved['items']->{'supplier.list.type.status'}, $searched['items'][0]->{'supplier.list.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
