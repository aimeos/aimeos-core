<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Product_Tag_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new Controller_ExtJS_Product_Tag_Default( TestHelper::getContext() );
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
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.tag.languageid' => 'de' ) ) ) ),
			'sort' => 'product.tag.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->_object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 6, $result['total'] );
		$this->assertEquals( 'Cappuccino', $result['items'][0]->{'product.tag.label'} );
	}


	public function testSaveDeleteItem()
	{
		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.tag.type.code' => 'taste' ) ) ) )
		);

		$typeCtrl = new Controller_ExtJS_Product_Tag_Type_Default( TestHelper::getContext() );
		$types = $typeCtrl->searchItems( $searchParams );
		$this->assertEquals( 1, count( $types['items'] ) );


		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'product.tag.typeid' => $types['items'][0]->{'product.tag.type.id'},
				'product.tag.languageid' => 'de',
				'product.tag.label' => 'unittest',
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.tag.label' => 'unittest' ) ) ) )
		);

		$saved = $this->_object->saveItems( $saveParams );
		$searched = $this->_object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.tag.id'} );
		$this->_object->deleteItems( $deleteParams );
		$result = $this->_object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.tag.id'} );
		$this->assertEquals( $saved['items']->{'product.tag.id'}, $searched['items'][0]->{'product.tag.id'} );
		$this->assertEquals( $saved['items']->{'product.tag.typeid'}, $searched['items'][0]->{'product.tag.typeid'} );
		$this->assertEquals( $saved['items']->{'product.tag.languageid'}, $searched['items'][0]->{'product.tag.languageid'} );
		$this->assertEquals( $saved['items']->{'product.tag.label'}, $searched['items'][0]->{'product.tag.label'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
