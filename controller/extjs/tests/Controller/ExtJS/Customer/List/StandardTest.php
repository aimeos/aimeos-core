<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_ExtJS_Customer_List_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new Controller_ExtJS_Customer_List_Standard( TestHelper::getContext() );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'customer.list.type.code' => 'watch' ) ) ) ),
			'sort' => 'customer.list.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'product', $result['items'][0]->{'customer.list.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Product']['items'] ) );
		$this->assertEquals( 'CNE', $result['graph']['Product']['items'][0]->{'product.code'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1 );
		$textManager = new Controller_ExtJS_Customer_Standard( TestHelper::getContext() );
		$result = $textManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'customer.list.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$listTypeManager = Controller_ExtJS_Customer_List_Type_Factory::createController( TestHelper::getContext() );
		$resultType = $listTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'customer.list.parentid' => $result['items'][0]->{'customer.id'},
				'customer.list.typeid' => $resultType['items'][0]->{'customer.list.type.id'},
				'customer.list.domain' => 'customer',
				'customer.list.refid' => -1,
				'customer.list.datestart' => '2000-01-01 00:00:00',
				'customer.list.dateend' => '2001-01-01 00:00:00',
				'customer.list.config' => array( 'test' => 'unit' ),
				'customer.list.position' => 1,
				'customer.list.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'customer.list.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'customer.list.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'customer.list.id'} );
		$this->assertEquals( $saved['items']->{'customer.list.id'}, $searched['items'][0]->{'customer.list.id'});
		$this->assertEquals( $saved['items']->{'customer.list.parentid'}, $searched['items'][0]->{'customer.list.parentid'});
		$this->assertEquals( $saved['items']->{'customer.list.typeid'}, $searched['items'][0]->{'customer.list.typeid'});
		$this->assertEquals( $saved['items']->{'customer.list.domain'}, $searched['items'][0]->{'customer.list.domain'});
		$this->assertEquals( $saved['items']->{'customer.list.refid'}, $searched['items'][0]->{'customer.list.refid'});
		$this->assertEquals( $saved['items']->{'customer.list.datestart'}, $searched['items'][0]->{'customer.list.datestart'});
		$this->assertEquals( $saved['items']->{'customer.list.dateend'}, $searched['items'][0]->{'customer.list.dateend'});
		$this->assertEquals( $saved['items']->{'customer.list.config'}, $searched['items'][0]->{'customer.list.config'});
		$this->assertEquals( $saved['items']->{'customer.list.position'}, $searched['items'][0]->{'customer.list.position'});
		$this->assertEquals( $saved['items']->{'customer.list.status'}, $searched['items'][0]->{'customer.list.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
