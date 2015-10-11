<?php

namespace Aimeos\Controller\ExtJS\Attribute\Lists;


/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
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
		$this->object = new \Aimeos\Controller\ExtJS\Attribute\Lists\Standard( \TestHelper::getContext() );
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
			'condition' => (object) array(
				'&&' => array(
					0 => (object) array( '==' => (object) array( 'attribute.lists.domain' => 'media' ) ),
					1 => (object) array( '==' => (object) array( 'attribute.lists.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'attribute.lists.parentid',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 'media', $result['items'][0]->{'attribute.lists.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Media']['items'] ) );
		$this->assertEquals( 'attribute', $result['graph']['Media']['items'][0]->{'media.domain'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1 );
		$productManager = new \Aimeos\Controller\ExtJS\Attribute\Standard( \TestHelper::getContext() );
		$result = $productManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'attribute.lists.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$attributeListTypeManager = \Aimeos\Controller\ExtJS\Attribute\Lists\Type\Factory::createController( \TestHelper::getContext() );
		$resultType = $attributeListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'attribute.lists.parentid' => $result['items'][0]->{'attribute.id'},
				'attribute.lists.typeid' => $resultType['items'][0]->{'attribute.lists.type.id'},
				'attribute.lists.domain' => 'text',
				'attribute.lists.refid' => -1,
				'attribute.lists.datestart' => '2000-01-01 00:00:00',
				'attribute.lists.dateend' => '2000-01-01 00:00:00',
				'attribute.lists.config' => array( 'test' => 'unit' ),
				'attribute.lists.position' => 1,
				'attribute.lists.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'attribute.lists.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'attribute.lists.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'attribute.lists.id'} );
		$this->assertEquals( $saved['items']->{'attribute.lists.id'}, $searched['items'][0]->{'attribute.lists.id'});
		$this->assertEquals( $saved['items']->{'attribute.lists.parentid'}, $searched['items'][0]->{'attribute.lists.parentid'});
		$this->assertEquals( $saved['items']->{'attribute.lists.typeid'}, $searched['items'][0]->{'attribute.lists.typeid'});
		$this->assertEquals( $saved['items']->{'attribute.lists.domain'}, $searched['items'][0]->{'attribute.lists.domain'});
		$this->assertEquals( $saved['items']->{'attribute.lists.refid'}, $searched['items'][0]->{'attribute.lists.refid'});
		$this->assertEquals( $saved['items']->{'attribute.lists.datestart'}, $searched['items'][0]->{'attribute.lists.datestart'});
		$this->assertEquals( $saved['items']->{'attribute.lists.dateend'}, $searched['items'][0]->{'attribute.lists.dateend'});
		$this->assertEquals( $saved['items']->{'attribute.lists.config'}, $searched['items'][0]->{'attribute.lists.config'});
		$this->assertEquals( $saved['items']->{'attribute.lists.position'}, $searched['items'][0]->{'attribute.lists.position'});
		$this->assertEquals( $saved['items']->{'attribute.lists.status'}, $searched['items'][0]->{'attribute.lists.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
