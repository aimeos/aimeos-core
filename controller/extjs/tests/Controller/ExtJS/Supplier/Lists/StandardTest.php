<?php

namespace Aimeos\Controller\ExtJS\Supplier\Lists;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\Controller\ExtJS\Supplier\Lists\Standard( \TestHelper::getContext() );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.lists.type.code' => 'default' ) ) ) ),
			'sort' => 'supplier.lists.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 3, $result['total'] );
		$this->assertEquals( 'text', $result['items'][0]->{'supplier.lists.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Text']['items'] ) );
		$this->assertEquals( 'supplier/description', $result['graph']['Text']['items'][0]->{'text.label'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1 );
		$textManager = new \Aimeos\Controller\ExtJS\Supplier\Standard( \TestHelper::getContext() );
		$result = $textManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.lists.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$listTypeManager = \Aimeos\Controller\ExtJS\Supplier\Lists\Type\Factory::createController( \TestHelper::getContext() );
		$resultType = $listTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'supplier.lists.parentid' => $result['items'][0]->{'supplier.id'},
				'supplier.lists.typeid' => $resultType['items'][0]->{'supplier.lists.type.id'},
				'supplier.lists.domain' => 'supplier',
				'supplier.lists.refid' => -1,
				'supplier.lists.datestart' => '2000-01-01 00:00:00',
				'supplier.lists.dateend' => '2001-01-01 00:00:00',
				'supplier.lists.config' => array( 'test' => 'unit' ),
				'supplier.lists.position' => 1,
				'supplier.lists.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'supplier.lists.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'supplier.lists.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'supplier.lists.id'} );
		$this->assertEquals( $saved['items']->{'supplier.lists.id'}, $searched['items'][0]->{'supplier.lists.id'});
		$this->assertEquals( $saved['items']->{'supplier.lists.parentid'}, $searched['items'][0]->{'supplier.lists.parentid'});
		$this->assertEquals( $saved['items']->{'supplier.lists.typeid'}, $searched['items'][0]->{'supplier.lists.typeid'});
		$this->assertEquals( $saved['items']->{'supplier.lists.domain'}, $searched['items'][0]->{'supplier.lists.domain'});
		$this->assertEquals( $saved['items']->{'supplier.lists.refid'}, $searched['items'][0]->{'supplier.lists.refid'});
		$this->assertEquals( $saved['items']->{'supplier.lists.datestart'}, $searched['items'][0]->{'supplier.lists.datestart'});
		$this->assertEquals( $saved['items']->{'supplier.lists.dateend'}, $searched['items'][0]->{'supplier.lists.dateend'});
		$this->assertEquals( $saved['items']->{'supplier.lists.config'}, $searched['items'][0]->{'supplier.lists.config'});
		$this->assertEquals( $saved['items']->{'supplier.lists.position'}, $searched['items'][0]->{'supplier.lists.position'});
		$this->assertEquals( $saved['items']->{'supplier.lists.status'}, $searched['items'][0]->{'supplier.lists.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
