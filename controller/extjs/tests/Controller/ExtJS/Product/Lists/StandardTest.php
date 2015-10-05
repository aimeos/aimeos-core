<?php

namespace Aimeos\Controller\ExtJS\Product\Lists;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
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
		$this->object = new \Aimeos\Controller\ExtJS\Product\Lists\Standard( \TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.lists.type.code' => 'unittype2' ) ) ) ),
			'sort' => 'product.lists.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'media', $result['items'][0]->{'product.lists.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Media']['items'] ) );
		$this->assertEquals( 'cn_colombie_266x221', $result['graph']['Media']['items'][0]->{'media.label'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1 );
		$productManager = new \Aimeos\Controller\ExtJS\Product\Standard( \TestHelper::getContext() );
		$result = $productManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.lists.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$productListTypeManager = \Aimeos\Controller\ExtJS\Product\Lists\Type\Factory::createController( \TestHelper::getContext() );
		$resultType = $productListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'product.lists.parentid' => $result['items'][0]->{'product.id'},
				'product.lists.typeid' => $resultType['items'][0]->{'product.lists.type.id'},
				'product.lists.domain' => 'text',
				'product.lists.refid' => -1,
				'product.lists.datestart' => '2000-01-01 00:00:00',
				'product.lists.dateend' => '2001-01-01 00:00:00',
				'product.lists.config' => array( 'test' => 'unit' ),
				'product.lists.position' => 1,
				'product.lists.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.lists.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );
		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.lists.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.lists.id'} );
		$this->assertEquals( $saved['items']->{'product.lists.id'}, $searched['items'][0]->{'product.lists.id'});
		$this->assertEquals( $saved['items']->{'product.lists.parentid'}, $searched['items'][0]->{'product.lists.parentid'});
		$this->assertEquals( $saved['items']->{'product.lists.typeid'}, $searched['items'][0]->{'product.lists.typeid'});
		$this->assertEquals( $saved['items']->{'product.lists.domain'}, $searched['items'][0]->{'product.lists.domain'});
		$this->assertEquals( $saved['items']->{'product.lists.refid'}, $searched['items'][0]->{'product.lists.refid'});
		$this->assertEquals( $saved['items']->{'product.lists.datestart'}, $searched['items'][0]->{'product.lists.datestart'});
		$this->assertEquals( $saved['items']->{'product.lists.dateend'}, $searched['items'][0]->{'product.lists.dateend'});
		$this->assertEquals( $saved['items']->{'product.lists.config'}, $searched['items'][0]->{'product.lists.config'});
		$this->assertEquals( $saved['items']->{'product.lists.position'}, $searched['items'][0]->{'product.lists.position'});
		$this->assertEquals( $saved['items']->{'product.lists.status'}, $searched['items'][0]->{'product.lists.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
