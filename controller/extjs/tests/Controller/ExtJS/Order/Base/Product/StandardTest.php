<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Order_Base_Product_StandardTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Controller_ExtJS_Order_Base_Product_Standard( TestHelper::getContext() );
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
			'condition' => (object) array(
				'&&' => array(
					0 => array( '~=' => (object) array( 'order.base.product.prodcode' => 'U:MD' ) ),
					1 => array( '==' => (object) array( 'order.base.product.editor' => 'core:unittest' ) )
				)
			),
			'sort' => 'order.base.product.mtime',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( -5.00, $result['items'][0]->{'order.base.product.price'} );
	}


	public function testSaveDeleteItem()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$baseManager = $manager->getSubManager( 'base' );
		$search = $baseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', '53.50' ) );
		$results = $baseManager->searchItems( $search );
		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No base item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'order.base.product.id' => null,
				'order.base.product.baseid' => $expected->getId(),
				'order.base.product.type' => 'default',
				'order.base.product.suppliercode' => 'unitsupplier',
				'order.base.product.prodcode' => 'EFGH22',
				'order.base.product.name' => 'FoooBar',
				'order.base.product.quantity' => 5,
				'order.base.product.flags' => 0,
				'order.base.product.status' => 1,
				'order.base.product.position' => 5,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array(
				'&&' => array(
					0 => array( '==' => (object) array( 'order.base.product.name' => 'FoooBar' ) ),
					1 => array( '==' => (object) array( 'order.base.product.prodcode' => 'EFGH22' ) )
				),
			),
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );


		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'order.base.product.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'order.base.product.id'} );
		$this->assertEquals( $saved['items']->{'order.base.product.id'}, $searched['items'][0]->{'order.base.product.id'} );
		$this->assertEquals( $saved['items']->{'order.base.product.baseid'}, $searched['items'][0]->{'order.base.product.baseid'} );
		$this->assertEquals( $saved['items']->{'order.base.product.suppliercode'}, $searched['items'][0]->{'order.base.product.suppliercode'} );
		$this->assertEquals( $saved['items']->{'order.base.product.prodcode'}, $searched['items'][0]->{'order.base.product.prodcode'} );
		$this->assertEquals( $saved['items']->{'order.base.product.name'}, $searched['items'][0]->{'order.base.product.name'} );
		$this->assertEquals( $saved['items']->{'order.base.product.quantity'}, $searched['items'][0]->{'order.base.product.quantity'} );
		$this->assertEquals( $saved['items']->{'order.base.product.flags'}, $searched['items'][0]->{'order.base.product.flags'} );
		$this->assertEquals( $saved['items']->{'order.base.product.status'}, $searched['items'][0]->{'order.base.product.status'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}
