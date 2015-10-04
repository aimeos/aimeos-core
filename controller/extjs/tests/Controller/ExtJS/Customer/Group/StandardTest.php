<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_ExtJS_Customer_Group_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new Controller_ExtJS_Customer_Group_Standard( TestHelper::getContext() );
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'customer.group.label' => 'Unitgroup' ) ) ) ),
			'sort' => 'customer.group.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'Unitgroup', $result['items'][0]->{'customer.group.label'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParam = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'customer.group.code' => 'extjs-unittest-group',
				'customer.group.label' => 'ExtJS unittest group',
			),
		);

		$searchParams = (object) array( 'site' => 'unittest', 'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'customer.group.label' => 'ExtJS unittest group' ) ) ) ) );

		$saved = $this->object->saveItems( $saveParam );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'customer.group.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'customer.group.id'} );
		$this->assertEquals( $saved['items']->{'customer.group.id'}, $searched['items'][0]->{'customer.group.id'} );
		$this->assertEquals( $saved['items']->{'customer.group.code'}, $searched['items'][0]->{'customer.group.code'} );
		$this->assertEquals( $saved['items']->{'customer.group.label'}, $searched['items'][0]->{'customer.group.label'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
