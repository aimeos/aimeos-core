<?php

namespace Aimeos\Controller\ExtJS\Service\Lists\Type;


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
		$this->object = new \Aimeos\Controller\ExtJS\Service\Lists\Type\Standard( \TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.lists.type.code' => 'unittype2' ) ) ) ),
			'sort' => 'service.lists.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'unittype2', $result['items'][0]->{'service.lists.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'service.lists.type.code' => 'test',
				'service.lists.type.label' => 'testLabel',
				'service.lists.type.domain' => 'service',
				'service.lists.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'service.lists.type.code' => 'test' ) ) ) )
		);


		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'service.lists.type.id'} );
		$this->object->deleteItems( $params );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'service.lists.type.id'} );
		$this->assertEquals( $saved['items']->{'service.lists.type.id'}, $searched['items'][0]->{'service.lists.type.id'} );
		$this->assertEquals( $saved['items']->{'service.lists.type.code'}, $searched['items'][0]->{'service.lists.type.code'} );
		$this->assertEquals( $saved['items']->{'service.lists.type.domain'}, $searched['items'][0]->{'service.lists.type.domain'} );
		$this->assertEquals( $saved['items']->{'service.lists.type.label'}, $searched['items'][0]->{'service.lists.type.label'} );
		$this->assertEquals( $saved['items']->{'service.lists.type.status'}, $searched['items'][0]->{'service.lists.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
