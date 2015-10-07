<?php

namespace Aimeos\Controller\ExtJS\Text\Lists;


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
		$this->object = new \Aimeos\Controller\ExtJS\Text\Lists\Standard( \TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'text.lists.type.code' => 'align-left' ) ) ) ),
			'sort' => 'text.lists.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 4, $result['total'] );
		$this->assertEquals( 'media', $result['items'][0]->{'text.lists.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Media']['items'] ) );
		$this->assertEquals( 'example image 1', $result['graph']['Media']['items'][0]->{'media.label'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1 );
		$textManager = new \Aimeos\Controller\ExtJS\Text\Standard( \TestHelper::getContext() );
		$result = $textManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'text.lists.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$textListTypeManager = \Aimeos\Controller\ExtJS\Text\Lists\Type\Factory::createController( \TestHelper::getContext() );
		$resultType = $textListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'text.lists.parentid' => $result['items'][0]->{'text.id'},
				'text.lists.typeid' => $resultType['items'][0]->{'text.lists.type.id'},
				'text.lists.domain' => 'text',
				'text.lists.refid' => -1,
				'text.lists.datestart' => '2000-01-01 00:00:00',
				'text.lists.dateend' => '2001-01-01 00:00:00',
				'text.lists.config' => array( 'test' => 'unit' ),
				'text.lists.position' => 1,
				'text.lists.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'text.lists.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'text.lists.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'text.lists.id'} );
		$this->assertEquals( $saved['items']->{'text.lists.id'}, $searched['items'][0]->{'text.lists.id'});
		$this->assertEquals( $saved['items']->{'text.lists.parentid'}, $searched['items'][0]->{'text.lists.parentid'});
		$this->assertEquals( $saved['items']->{'text.lists.typeid'}, $searched['items'][0]->{'text.lists.typeid'});
		$this->assertEquals( $saved['items']->{'text.lists.domain'}, $searched['items'][0]->{'text.lists.domain'});
		$this->assertEquals( $saved['items']->{'text.lists.refid'}, $searched['items'][0]->{'text.lists.refid'});
		$this->assertEquals( $saved['items']->{'text.lists.datestart'}, $searched['items'][0]->{'text.lists.datestart'});
		$this->assertEquals( $saved['items']->{'text.lists.dateend'}, $searched['items'][0]->{'text.lists.dateend'});
		$this->assertEquals( $saved['items']->{'text.lists.config'}, $searched['items'][0]->{'text.lists.config'});
		$this->assertEquals( $saved['items']->{'text.lists.position'}, $searched['items'][0]->{'text.lists.position'});
		$this->assertEquals( $saved['items']->{'text.lists.status'}, $searched['items'][0]->{'text.lists.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
