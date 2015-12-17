<?php

namespace Aimeos\Controller\ExtJS\Product\Property\Type;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
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
		$this->object = new \Aimeos\Controller\ExtJS\Product\Property\Type\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '=~' => (object) array( 'product.property.type.code' => 'package-' ) ) ) ),
			'sort' => 'product.property.type.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 4, $result['total'] );
		$this->assertStringStartsWith( 'package-', $result['items'][0]->{'product.property.type.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'product.property.type.code' => 'test',
				'product.property.type.label' => 'testLabel',
				'product.property.type.domain' => 'product/property',
				'product.property.type.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.property.type.code' => 'test' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$params = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.property.type.id'} );
		$this->object->deleteItems( $params );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.property.type.id'} );
		$this->assertEquals( $saved['items']->{'product.property.type.id'}, $searched['items'][0]->{'product.property.type.id'} );
		$this->assertEquals( $saved['items']->{'product.property.type.code'}, $searched['items'][0]->{'product.property.type.code'} );
		$this->assertEquals( $saved['items']->{'product.property.type.domain'}, $searched['items'][0]->{'product.property.type.domain'} );
		$this->assertEquals( $saved['items']->{'product.property.type.label'}, $searched['items'][0]->{'product.property.type.label'} );
		$this->assertEquals( $saved['items']->{'product.property.type.status'}, $searched['items'][0]->{'product.property.type.status'} );

		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
