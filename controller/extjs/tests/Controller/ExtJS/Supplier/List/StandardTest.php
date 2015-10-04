<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_ExtJS_Supplier_List_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new Controller_ExtJS_Supplier_List_Standard( TestHelper::getContext() );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.list.type.code' => 'default' ) ) ) ),
			'sort' => 'supplier.list.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 3, $result['total'] );
		$this->assertEquals( 'text', $result['items'][0]->{'supplier.list.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Text']['items'] ) );
		$this->assertEquals( 'supplier/description', $result['graph']['Text']['items'][0]->{'text.label'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1 );
		$textManager = new Controller_ExtJS_Supplier_Standard( TestHelper::getContext() );
		$result = $textManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.list.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$listTypeManager = Controller_ExtJS_Supplier_List_Type_Factory::createController( TestHelper::getContext() );
		$resultType = $listTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'supplier.list.parentid' => $result['items'][0]->{'supplier.id'},
				'supplier.list.typeid' => $resultType['items'][0]->{'supplier.list.type.id'},
				'supplier.list.domain' => 'supplier',
				'supplier.list.refid' => -1,
				'supplier.list.datestart' => '2000-01-01 00:00:00',
				'supplier.list.dateend' => '2001-01-01 00:00:00',
				'supplier.list.config' => array( 'test' => 'unit' ),
				'supplier.list.position' => 1,
				'supplier.list.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.list.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'supplier.list.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'supplier.list.id'} );
		$this->assertEquals( $saved['items']->{'supplier.list.id'}, $searched['items'][0]->{'supplier.list.id'});
		$this->assertEquals( $saved['items']->{'supplier.list.parentid'}, $searched['items'][0]->{'supplier.list.parentid'});
		$this->assertEquals( $saved['items']->{'supplier.list.typeid'}, $searched['items'][0]->{'supplier.list.typeid'});
		$this->assertEquals( $saved['items']->{'supplier.list.domain'}, $searched['items'][0]->{'supplier.list.domain'});
		$this->assertEquals( $saved['items']->{'supplier.list.refid'}, $searched['items'][0]->{'supplier.list.refid'});
		$this->assertEquals( $saved['items']->{'supplier.list.datestart'}, $searched['items'][0]->{'supplier.list.datestart'});
		$this->assertEquals( $saved['items']->{'supplier.list.dateend'}, $searched['items'][0]->{'supplier.list.dateend'});
		$this->assertEquals( $saved['items']->{'supplier.list.config'}, $searched['items'][0]->{'supplier.list.config'});
		$this->assertEquals( $saved['items']->{'supplier.list.position'}, $searched['items'][0]->{'supplier.list.position'});
		$this->assertEquals( $saved['items']->{'supplier.list.status'}, $searched['items'][0]->{'supplier.list.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
