<?php

namespace Aimeos\Controller\ExtJS\Customer\Lists;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\Controller\ExtJS\Customer\Lists\Standard( \TestHelper::getContext() );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'customer.lists.type.code' => 'watch' ) ) ) ),
			'sort' => 'customer.lists.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'product', $result['items'][0]->{'customer.lists.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Product']['items'] ) );
		$this->assertEquals( 'CNE', $result['graph']['Product']['items'][0]->{'product.code'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1 );
		$textManager = new \Aimeos\Controller\ExtJS\Customer\Standard( \TestHelper::getContext() );
		$result = $textManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'customer.lists.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$listTypeManager = \Aimeos\Controller\ExtJS\Customer\Lists\Type\Factory::createController( \TestHelper::getContext() );
		$resultType = $listTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'customer.lists.parentid' => $result['items'][0]->{'customer.id'},
				'customer.lists.typeid' => $resultType['items'][0]->{'customer.lists.type.id'},
				'customer.lists.domain' => 'customer',
				'customer.lists.refid' => -1,
				'customer.lists.datestart' => '2000-01-01 00:00:00',
				'customer.lists.dateend' => '2001-01-01 00:00:00',
				'customer.lists.config' => array( 'test' => 'unit' ),
				'customer.lists.position' => 1,
				'customer.lists.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'customer.lists.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'customer.lists.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'customer.lists.id'} );
		$this->assertEquals( $saved['items']->{'customer.lists.id'}, $searched['items'][0]->{'customer.lists.id'});
		$this->assertEquals( $saved['items']->{'customer.lists.parentid'}, $searched['items'][0]->{'customer.lists.parentid'});
		$this->assertEquals( $saved['items']->{'customer.lists.typeid'}, $searched['items'][0]->{'customer.lists.typeid'});
		$this->assertEquals( $saved['items']->{'customer.lists.domain'}, $searched['items'][0]->{'customer.lists.domain'});
		$this->assertEquals( $saved['items']->{'customer.lists.refid'}, $searched['items'][0]->{'customer.lists.refid'});
		$this->assertEquals( $saved['items']->{'customer.lists.datestart'}, $searched['items'][0]->{'customer.lists.datestart'});
		$this->assertEquals( $saved['items']->{'customer.lists.dateend'}, $searched['items'][0]->{'customer.lists.dateend'});
		$this->assertEquals( $saved['items']->{'customer.lists.config'}, $searched['items'][0]->{'customer.lists.config'});
		$this->assertEquals( $saved['items']->{'customer.lists.position'}, $searched['items'][0]->{'customer.lists.position'});
		$this->assertEquals( $saved['items']->{'customer.lists.status'}, $searched['items'][0]->{'customer.lists.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
