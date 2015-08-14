<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Locale_Language_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new Controller_ExtJS_Locale_Language_Default( TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'locale.language.code' => 'de' ) ) ) ),
			'sort' => 'locale.language.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'de', $result['items'][0]->{'locale.language.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'items' => (object) array(
				'locale.language.code' => 'xx',
				'locale.language.label' => 'XX',
				'locale.language.status' => 1
			),
		);

		$searchParams = (object) array(
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => array( 'locale.language.code' => 'xx' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'items' => $saved['items']->{'locale.language.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'locale.language.id'} );
		$this->assertEquals( $saved['items']->{'locale.language.id'}, $searched['items'][0]->{'locale.language.id'} );
		$this->assertEquals( $saved['items']->{'locale.language.code'}, $searched['items'][0]->{'locale.language.code'} );
		$this->assertEquals( $saved['items']->{'locale.language.label'}, $searched['items'][0]->{'locale.language.label'} );
		$this->assertEquals( $saved['items']->{'locale.language.status'}, $searched['items'][0]->{'locale.language.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testGetServiceDescription()
	{
		$expected = array(
			'Locale_Language.deleteItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Language.saveItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Language.searchItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "condition", "optional" => true ),
					array( "type" => "integer", "name" => "start", "optional" => true ),
					array( "type" => "integer", "name" => "limit", "optional" => true ),
					array( "type" => "string", "name" => "sort", "optional" => true ),
					array( "type" => "string", "name" => "dir", "optional" => true ),
					array( "type" => "array", "name" => "options", "optional" => true ),
				),
				"returns" => "array",
			),
		);

		$actual = $this->_object->getServiceDescription();

		$this->assertEquals( $expected, $actual );
	}

}
