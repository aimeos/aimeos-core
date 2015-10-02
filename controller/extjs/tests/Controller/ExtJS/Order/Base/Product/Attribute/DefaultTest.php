<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Order_Base_Product_Attribute_DefaultTest extends PHPUnit_Framework_TestCase
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
		$this->object = new Controller_ExtJS_Order_Base_Product_Attribute_Default( TestHelper::getContext() );
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
			'condition' => (object) array( '&&' => array(
				0 => (object) array( '==' => (object) array( 'order.base.product.attribute.code' => 'color' ) ),
				1 => (object) array( '==' => (object) array( 'order.base.product.attribute.value' => 'blue' ) ),
				2 => (object) array( '==' => (object) array( 'order.base.product.attribute.editor' => 'core:unittest' ) ),
			) ),
			'sort' => 'order.base.product.attribute.mtime',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 1, $result['total'] );
		$this->assertEquals( 'color', $result['items'][0]->{'order.base.product.attribute.code'} );
	}

	public function testSaveDeleteItem()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$baseManager = $manager->getSubManager( 'base' );
		$productManager = $baseManager->getSubManager( 'product' );
		$search = $productManager->createSearch();

		$search->setConditions( $search->compare( '==', 'order.base.product.prodcode', 'CNE' ) );
		$results = $productManager->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No product item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'order.base.product.attribute.productid' => $expected->getId(),
				'order.base.product.attribute.code' => 'color',
				'order.base.product.attribute.value' => 'purple',
				'order.base.product.attribute.name' => 'Lila'
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => array( '==' => (object) array( 'order.base.product.attribute.name' => 'Lila' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );

		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'order.base.product.attribute.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'order.base.product.attribute.id'} );
		$this->assertEquals( $saved['items']->{'order.base.product.attribute.id'}, $searched['items'][0]->{'order.base.product.attribute.id'} );
		$this->assertEquals( $saved['items']->{'order.base.product.attribute.code'}, $searched['items'][0]->{'order.base.product.attribute.code'} );
		$this->assertEquals( $saved['items']->{'order.base.product.attribute.value'}, $searched['items'][0]->{'order.base.product.attribute.value'} );
		$this->assertEquals( $saved['items']->{'order.base.product.attribute.name'}, $searched['items'][0]->{'order.base.product.attribute.name'} );
		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}
}

