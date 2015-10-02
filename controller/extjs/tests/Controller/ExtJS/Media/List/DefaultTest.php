<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Media_List_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Controller_ExtJS_Media_List_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.list.domain' => 'text' ) ) ) ),
			'sort' => 'media.list.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'text', $result['items'][0]->{'media.list.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Text']['items'] ) );
		$this->assertEquals( 'Bildbeschreibung', $result['graph']['Text']['items'][0]->{'text.content'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1, 'media.list.type.domain' => 'text' );
		$mediaManager = new Controller_ExtJS_Media_Default( TestHelper::getContext() );
		$result = $mediaManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.list.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$mediaListTypeManager = Controller_ExtJS_Media_List_Type_Factory::createController( TestHelper::getContext() );
		$resultType = $mediaListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'media.list.parentid' => $result['items'][0]->{'media.id'},
				'media.list.typeid' => $resultType['items'][0]->{'media.list.type.id'},
				'media.list.domain' => 'text',
				'media.list.refid' => -1,
				'media.list.datestart' => '2000-01-01 00:00:00',
				'media.list.dateend' => '2000-01-01 00:00:00',
				'media.list.config' => array( 'test' => 'unit' ),
				'media.list.position' => 1,
				'media.list.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.list.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'media.list.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'media.list.id'} );
		$this->assertEquals( $saved['items']->{'media.list.id'}, $searched['items'][0]->{'media.list.id'});
		$this->assertEquals( $saved['items']->{'media.list.parentid'}, $searched['items'][0]->{'media.list.parentid'});
		$this->assertEquals( $saved['items']->{'media.list.typeid'}, $searched['items'][0]->{'media.list.typeid'});
		$this->assertEquals( $saved['items']->{'media.list.domain'}, $searched['items'][0]->{'media.list.domain'});
		$this->assertEquals( $saved['items']->{'media.list.refid'}, $searched['items'][0]->{'media.list.refid'});
		$this->assertEquals( $saved['items']->{'media.list.datestart'}, $searched['items'][0]->{'media.list.datestart'});
		$this->assertEquals( $saved['items']->{'media.list.dateend'}, $searched['items'][0]->{'media.list.dateend'});
		$this->assertEquals( $saved['items']->{'media.list.config'}, $searched['items'][0]->{'media.list.config'});
		$this->assertEquals( $saved['items']->{'media.list.position'}, $searched['items'][0]->{'media.list.position'});
		$this->assertEquals( $saved['items']->{'media.list.status'}, $searched['items'][0]->{'media.list.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
