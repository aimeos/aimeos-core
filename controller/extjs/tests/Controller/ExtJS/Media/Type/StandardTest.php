<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Media_Type_StandardTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Controller_ExtJS_Media_Type_Standard( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'media.type.code' => 'prod_114x95' ) ) ) ),
			'sort' => 'media.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'prod_114x95', $result['items'][0]->{'media.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'media.type.code' => 'cntltest',
				'media.type.domain' => 'media',
				'media.type.label' => 'testLabel',
				'media.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => array( 'media.type.code' => 'cntltest' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'media.type.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'media.type.id'} );
		$this->assertEquals( $saved['items']->{'media.type.id'}, $searched['items'][0]->{'media.type.id'} );
		$this->assertEquals( $saved['items']->{'media.type.code'}, $searched['items'][0]->{'media.type.code'} );
		$this->assertEquals( $saved['items']->{'media.type.domain'}, $searched['items'][0]->{'media.type.domain'} );
		$this->assertEquals( $saved['items']->{'media.type.label'}, $searched['items'][0]->{'media.type.label'} );
		$this->assertEquals( $saved['items']->{'media.type.status'}, $searched['items'][0]->{'media.type.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
