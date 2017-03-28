<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class PropertyAddTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $products;


	protected function setUp()
	{
		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'PropertyAdd' );
		$this->plugin->setStatus( '1' );

		$this->plugin->setConfig( array(
			'product.property.parentid' => array(
				'product.property.languageid',
				'product.property.value',
				'product.property.editor',
			),
		) );

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );

		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );

		$products = $manager->searchItems( $search );

		if( count( $products ) !== 2 ) {
			throw new \RuntimeException( 'Wrong number of products' );
		}

		$this->products = [];

		foreach( $products as $product )
		{
			$item = $orderBaseProductManager->createItem();
			$item->copyFrom( $product );

			$this->products[$product->getCode()] = $item;
		}

		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyAdd( \TestHelperMShop::getContext(), $this->plugin );
	}


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
		$this->assertEquals( 3, count( $this->products['CNC']->getAttributes() ) );

		$this->products['CNE']->setAttributes( [] );
		$this->plugin->setConfig( array(
			'product.lists.parentid' => array(
				'product.lists.domain',
				'product.lists.refid',
			),
		) );

		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );

		$this->assertEquals( 2, count( $this->products['CNE']->getAttributes() ) );
	}


	public function testUpdateAttributeExists()
	{
		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$attributeManager = $orderManager->getSubmanager( 'base' )->getSubmanager( 'product' )->getSubmanager( 'attribute' );

		$attribute = $attributeManager->createItem();

		$attribute->setCode( 'product.property.value' );
		$attribute->setName( 'product.property.value' );
		$attribute->setValue( '1000' );
		$attribute->setType( 'property' );

		$this->products['CNC']->setAttributes( array( $attribute ) );
		$this->assertEquals( 1, count( $this->products['CNC']->getAttributes() ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );
		$this->assertEquals( 3, count( $this->products['CNC']->getAttributes() ) );
	}


	public function testUpdateConfigError()
	{
		// Non-existent property:

		$this->plugin->setConfig( array(
			'product.property.parentid' => array(
				'product.property.quatsch',
				'product.property.editor',
			),
		) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );
		$this->assertEquals( 1, count( $this->products['CNC']->getAttributes() ) );


		// Incorrect key:

		$this->plugin->setConfig( array( 'product.myid' => array(
			'product.property.typeid',
		) ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] );
	}
}
