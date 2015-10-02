<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Locale_Currency_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Controller_ExtJS_Locale_Currency_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'locale.currency.code' => 'EUR' ) ) ) ),
			'sort' => 'locale.currency.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'EUR', $result['items'][0]->{'locale.currency.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'items' => (object) array(
				'locale.currency.code' => 'xxx',
				'locale.currency.label' => 'XXX',
				'locale.currency.status' => 1
			),
		);

		$searchParams = (object) array(
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => array( 'locale.currency.code' => 'xxx' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'items' => $saved['items']->{'locale.currency.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'locale.currency.id'} );
		$this->assertEquals( $saved['items']->{'locale.currency.id'}, $searched['items'][0]->{'locale.currency.id'} );
		$this->assertEquals( $saved['items']->{'locale.currency.code'}, $searched['items'][0]->{'locale.currency.code'} );
		$this->assertEquals( $saved['items']->{'locale.currency.label'}, $searched['items'][0]->{'locale.currency.label'} );
		$this->assertEquals( $saved['items']->{'locale.currency.status'}, $searched['items'][0]->{'locale.currency.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testGetServiceDescription()
	{
		$expected = array(
			'Locale_Currency.deleteItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Currency.saveItems' => array(
				"parameters" => array(
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Locale_Currency.searchItems' => array(
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

		$actual = $this->object->getServiceDescription();

		$this->assertEquals( $expected, $actual );
	}

}
