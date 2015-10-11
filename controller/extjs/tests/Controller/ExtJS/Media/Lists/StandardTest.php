<?php

namespace Aimeos\Controller\ExtJS\Media\Lists;


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
		$this->object = new \Aimeos\Controller\ExtJS\Media\Lists\Standard( \TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.lists.domain' => 'text' ) ) ) ),
			'sort' => 'media.lists.position',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'text', $result['items'][0]->{'media.lists.domain'} );
		$this->assertEquals( 1, count( $result['graph']['Text']['items'] ) );
		$this->assertEquals( 'Bildbeschreibung', $result['graph']['Text']['items'][0]->{'text.content'} );
	}


	public function testSaveDeleteItem()
	{
		$params = (object) array( 'site' => 'unittest', 'limit' => 1, 'media.lists.type.domain' => 'text' );
		$mediaManager = new \Aimeos\Controller\ExtJS\Media\Standard( \TestHelper::getContext() );
		$result = $mediaManager->searchItems( $params );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.lists.type.domain' => 'text' ) ) ) ),
			'start' => 0,
			'limit' => 1,
		);
		$mediaListTypeManager = \Aimeos\Controller\ExtJS\Media\Lists\Type\Factory::createController( \TestHelper::getContext() );
		$resultType = $mediaListTypeManager->searchItems( $params );

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'media.lists.parentid' => $result['items'][0]->{'media.id'},
				'media.lists.typeid' => $resultType['items'][0]->{'media.lists.type.id'},
				'media.lists.domain' => 'text',
				'media.lists.refid' => -1,
				'media.lists.datestart' => '2000-01-01 00:00:00',
				'media.lists.dateend' => '2000-01-01 00:00:00',
				'media.lists.config' => array( 'test' => 'unit' ),
				'media.lists.position' => 1,
				'media.lists.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.lists.refid' => -1 ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'media.lists.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'media.lists.id'} );
		$this->assertEquals( $saved['items']->{'media.lists.id'}, $searched['items'][0]->{'media.lists.id'});
		$this->assertEquals( $saved['items']->{'media.lists.parentid'}, $searched['items'][0]->{'media.lists.parentid'});
		$this->assertEquals( $saved['items']->{'media.lists.typeid'}, $searched['items'][0]->{'media.lists.typeid'});
		$this->assertEquals( $saved['items']->{'media.lists.domain'}, $searched['items'][0]->{'media.lists.domain'});
		$this->assertEquals( $saved['items']->{'media.lists.refid'}, $searched['items'][0]->{'media.lists.refid'});
		$this->assertEquals( $saved['items']->{'media.lists.datestart'}, $searched['items'][0]->{'media.lists.datestart'});
		$this->assertEquals( $saved['items']->{'media.lists.dateend'}, $searched['items'][0]->{'media.lists.dateend'});
		$this->assertEquals( $saved['items']->{'media.lists.config'}, $searched['items'][0]->{'media.lists.config'});
		$this->assertEquals( $saved['items']->{'media.lists.position'}, $searched['items'][0]->{'media.lists.position'});
		$this->assertEquals( $saved['items']->{'media.lists.status'}, $searched['items'][0]->{'media.lists.status'});
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
