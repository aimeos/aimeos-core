<?php

namespace Aimeos\Controller\ExtJS\Product\Property;


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
		$this->object = new \Aimeos\Controller\ExtJS\Product\Property\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.property.value' => '10.0' ) ) ) ),
			'sort' => 'product.property.value',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 2, $result['total'] );
		$this->assertEquals( '10.0', $result['items'][0]->{'product.property.value'} );
	}


	public function testSaveDeleteItem()
	{
		$searchParams = (object) array(
				'site' => 'unittest',
				'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.code' => 'CNC' ) ) ) )
		);
		
		$prodCtrl = new \Aimeos\Controller\ExtJS\Product\Standard( \TestHelperExtjs::getContext() );
		$products = $prodCtrl->searchItems( $searchParams );
		$this->assertEquals( 1, count( $products['items'] ) );
		
		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.property.type.code' => 'package-weight' ) ) ) )
		);

		$typeCtrl = new \Aimeos\Controller\ExtJS\Product\Property\Type\Standard( \TestHelperExtjs::getContext() );
		$types = $typeCtrl->searchItems( $searchParams );
		$this->assertEquals( 1, count( $types['items'] ) );


		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'product.property.parentid' => $products['items'][0]->{'product.id'},
				'product.property.typeid' => $types['items'][0]->{'product.property.type.id'},
				'product.property.languageid' => 'de',
				'product.property.value' => 'unittest',
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.property.value' => 'unittest' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.property.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.property.id'} );
		$this->assertEquals( $saved['items']->{'product.property.id'}, $searched['items'][0]->{'product.property.id'} );
		$this->assertEquals( $saved['items']->{'product.property.parentid'}, $searched['items'][0]->{'product.property.parentid'} );
		$this->assertEquals( $saved['items']->{'product.property.typeid'}, $searched['items'][0]->{'product.property.typeid'} );
		$this->assertEquals( $saved['items']->{'product.property.languageid'}, $searched['items'][0]->{'product.property.languageid'} );
		$this->assertEquals( $saved['items']->{'product.property.value'}, $searched['items'][0]->{'product.property.value'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
