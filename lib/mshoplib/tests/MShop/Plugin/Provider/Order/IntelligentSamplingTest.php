<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class MShop_Plugin_Provider_Order_IntelligentSamplingTest extends MW_Unittest_Testcase
{
	private $_order;
	private $_plugin;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$pluginManager = MShop_Plugin_Manager_Factory::createManager( $context );
		$this->_plugin = $pluginManager->createItem();
		$this->_plugin->setProvider( 'IntelligentSampling' );
		$this->_plugin->setStatus( 1 );

		$this->_orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $this->_orderManager->getSubManager('base');
		$orderBaseProductManager = $orderBaseManager->getSubManager('product');

		$manager = MShop_Product_Manager_Factory::createManager( $context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'EFGH', 'IJKL', 'MNOP' ) ) );

		$pResults = $manager->searchItems( $search );

		if ( count($pResults) !== 3 ) {
			throw new Exception('Wrong number of products');
		}

		$this->products = array();
		foreach( $pResults as $prod ) {
			$this->products[ $prod->getCode() ] = $prod;
		}

		$product1 = $orderBaseProductManager->createItem();
		$product1->copyFrom( $this->products['EFGH'] );

		$product2 = $orderBaseProductManager->createItem();
		$product2->copyFrom( $this->products['IJKL'] );

		$product3 = $orderBaseProductManager->createItem();
		$product3->copyFrom( $this->products['MNOP'] );

		$this->_order = $orderBaseManager->createItem();

		$this->_order->addProduct( $product1 );
		$this->_order->addProduct( $product2 );
		$this->_order->addProduct( $product3 );

		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', 672.00 ) );
		$search->setSlice(0, 1);
		$items = $orderBaseManager->searchItems( $search );
		if( ( $baseItem = reset($items) ) === false ) {
			throw new Exception( 'No order base item found.' );
		}

		$this->_order->setCustomerId( $baseItem->getCustomerId() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_orderManager );
		unset($this->_plugin);
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_IntelligentSampling(TestHelper::getContext(), $this->_plugin);
		$object->register( $this->_order );
	}


	public function testUpdate1()
	{
		// Test for first time orders
		$this->_plugin->setConfig( array(
			'samples' => array(
				'CNE' => array(
					'conditions' => array(
						'&&' => array(
							array( '!=' => array( 'exists("CNC"]' => true ) ),
							array( '!=' => array( 'exists("CNE"]' => true ) ),
						)
					)
				)
			),
			'firsttime' => true
		) );

		$object = new MShop_Plugin_Provider_Order_IntelligentSampling(TestHelper::getContext(), $this->_plugin);
		$this->assertFalse($object->update($this->_order, 'setOrder.before'));
	}


	public function testUpdate2()
	{
		// Test for first time orders with the first product in the sample list already in the basket
		$this->_plugin->setConfig( array(
			"samples" => array(
				'CNE' => array(
					'conditions' => array(
						'!=' => array( 'exists("' . $this->products['EFGH']->getCode() . '")' => true ),
					)
				)
			),
			"firsttime" => 0
		) );

		$object = new MShop_Plugin_Provider_Order_IntelligentSampling(TestHelper::getContext(), $this->_plugin);
		$this->assertFalse($object->update($this->_order, 'setOrder.before'));
	}


	public function testUpdate3()
	{
		// Test for orders when the first sample product was already added
		$this->_plugin->setConfig( array(
			"samples" => array(
				'CNE' => array(
					'conditions' => array(
						'!=' => array( 'exists("CNE")' => true ),
					)
				)
			),
			"firsttime" => 0
		) );

		$object = new MShop_Plugin_Provider_Order_IntelligentSampling(TestHelper::getContext(), $this->_plugin);
		$this->assertFalse($object->update($this->_order, 'setOrder.before'));
	}


	public function testUpdate4()
	{
		// Test for orders where product A and not product B was already added
		$this->_plugin->setConfig( array(
			"samples" => array(
				'CNC' => array(
					'conditions' => array(
						'&&' => array(
							array( '!=' => array( 'exists("CNC")' => true ) ),
							array( '==' => array( 'exists("' . $this->products['EFGH']->getCode() . '")' => true ) ),
						)
					)
				)
			),
			"firsttime" => 0
		) );

		$object = new MShop_Plugin_Provider_Order_IntelligentSampling(TestHelper::getContext(), $this->_plugin);
		$this->assertFalse($object->update($this->_order, 'setOrder.before'));
	}


	public function testUpdate5()
	{
		// Test for orders where a product wasn't added before
		$this->_plugin->setConfig( array(
			"samples" => array(
				'CNC' => array(
					'conditions' => array(
						'!=' => array( 'exists("' . $this->products['MNOP']->getCode() . '")' => true ),
					)
				)
			),
			"firsttime" => 0
		) );
		$this->_order->deleteProduct( 2 );

		$object = new MShop_Plugin_Provider_Order_IntelligentSampling(TestHelper::getContext(), $this->_plugin);
		$result = $object->update($this->_order, 'setOrder.before');

		$products = $this->_order->getProducts();

		$this->assertTrue( $result );
		$this->assertEquals( 3, count( $products ) );

		$codes = array( $this->products['EFGH']->getCode(), $this->products['IJKL']->getCode(), 'CNC');
		foreach( $products as $product ) {
			$this->assertTrue( in_array( $product->getProductCode(), $codes ) );
		}
	}


	public function testUpdate6()
	{
		// Test for orders where products were already added before and an alternative sample is given
		$this->_plugin->setConfig( array(
			"samples" => array(
				'CNC' => array(
					'conditions' => array(
						'!=' => array( 'exists("' . $this->products['EFGH']->getCode() . '")' => true ),
					)
				)
			),
			"alternative" => 'CNE',
			"firsttime" => 0
		) );

		$object = new MShop_Plugin_Provider_Order_IntelligentSampling(TestHelper::getContext(), $this->_plugin);
		$result = $object->update($this->_order, 'setOrder.before');

		$products = $this->_order->getProducts();

		$this->assertTrue( $result );
		$this->assertEquals( 4, count( $products ) );
		$this->assertEquals( 'CNE', $products[3]->getProductCode() );
	}


	public function testUpdate7()
	{
		// Test for orders where a product is not existing
		$this->_plugin->setConfig( array(
			"samples" => array(
				'xyz' => array(
					'conditions' => array(
						'!=' => array( 'exists("CNE")' => true ),
					)
				)
			),
			"firsttime" => 0
		) );

		$object = new MShop_Plugin_Provider_Order_IntelligentSampling(TestHelper::getContext(), $this->_plugin);
		$this->assertFalse($object->update($this->_order, 'setOrder.before'));
	}
}