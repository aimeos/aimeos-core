<?php

namespace Aimeos\Controller\ExtJS\Product\Stock\Warehouse;


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
		$this->object = new \Aimeos\Controller\ExtJS\Product\Stock\Warehouse\Standard( \TestHelperExtjs::getContext() );
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
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'product.stock.warehouse.code' => 'unit' ) ) ) ),
			'sort' => 'product.stock.warehouse.code',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 5, $result['total'] );
		$this->assertEquals( 'unit_warehouse1', $result['items'][0]->{'product.stock.warehouse.code'} );
	}


	public function testSaveDeleteItem()
	{
		$saveParams = (object) array(
			'site' => 'unittest',
			'items' =>  (object) array(
				'product.stock.warehouse.code' => 'test',
				'product.stock.warehouse.label' => 'label',
				'product.stock.warehouse.status' => 1,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'product.stock.warehouse.code' => 'test' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'product.stock.warehouse.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'product.stock.warehouse.id'} );
		$this->assertEquals( $saved['items']->{'product.stock.warehouse.id'}, $searched['items'][0]->{'product.stock.warehouse.id'} );
		$this->assertEquals( $saved['items']->{'product.stock.warehouse.code'}, $searched['items'][0]->{'product.stock.warehouse.code'} );
		$this->assertEquals( $saved['items']->{'product.stock.warehouse.label'}, $searched['items'][0]->{'product.stock.warehouse.label'} );
		$this->assertEquals( $saved['items']->{'product.stock.warehouse.status'}, $searched['items'][0]->{'product.stock.warehouse.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
