<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Test class for \Aimeos\MShop\Plugin\Provider\Order\PropertyAdd.
 */
class PropertyAddTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $products;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( \TestHelper::getContext() );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'PropertyAdd' );
		$this->plugin->setStatus( '1' );

		$this->plugin->setConfig( array( 'product.stock.parentid' => array(
			'product.stock.warehouseid',
			'product.stock.editor',
			'product.stock.stocklevel',
			'product.stock.dateback'
		) ) );

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );

		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelper::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );

		$products = $manager->searchItems( $search );

		if( count( $products ) !== 2 ) {
			throw new \Exception( 'Wrong number of products' );
		}

		$this->products = array();

		foreach( $products as $product )
		{
			$item = $orderBaseProductManager->createItem();
			$item->copyFrom( $product );

			$this->products[$product->getCode()] = $item;
		}

		$this->order = $orderBaseManager->createItem();

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyAdd( \TestHelper::getContext(), $this->plugin );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object, $this->order, $this->plugin, $this->products );
	}


	public function testRegister()
	{
		$this->object->register( $this->order );
	}


	public function testUpdateOk()
	{
		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );
		$this->assertEquals( 4, count( $this->products['CNC']->getAttributes() ) );

		$this->products['CNE']->setAttributes( array() );
		$this->plugin->setConfig( array(
			'product.lists.parentid' => array(
				'product.lists.domain',
			),
			'product.stock.parentid' => array(
				'product.stock.stocklevel'
			)
		) );

		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );

		$this->assertEquals( 2, count( $this->products['CNE']->getAttributes() ) );
	}


	public function testUpdateAttributeExists()
	{
		$attributeManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelper::getContext() )->getSubmanager( 'base' )->getSubmanager( 'product' )->getSubmanager( 'attribute' );

		$attribute = $attributeManager->createItem();

		$attribute->setCode( 'product.stock.stocklevel' );
		$attribute->setName( 'product.stock.stocklevel' );
		$attribute->setValue( '1200' );
		$attribute->setType( 'property' );

		$this->products['CNC']->setAttributes( array( $attribute ) );
		$this->assertEquals( 1, count( $this->products['CNC']->getAttributes() ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );
		$this->assertEquals( 4, count( $this->products['CNC']->getAttributes() ) );
	}


	public function testUpdateConfigError()
	{
		// Non-existent property:

		$this->plugin->setConfig( array( 'product.stock.parentid' => array(
			'product.stock.quatsch',
			'product.stock.editor',
			'product.stock.stocklevel',
			'product.stock.dateback'
		) ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );
		$this->assertEquals( 3, count( $this->products['CNC']->getAttributes() ) );


		// Incorrect key:

		$this->plugin->setConfig( array( 'stock.productid' => array(
			'stock.warehouseid',
		) ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] );
	}
}
