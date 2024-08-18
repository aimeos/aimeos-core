<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2024
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class BasketValuesTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;
	private $serviceItem;


	protected function setUp() : void
	{
		$orderProducts = [];
		$context = \TestHelper::context();
		$this->serviceItem = \Aimeos\MShop::create( $context, 'service' )->create();

		$provider = new \Aimeos\MShop\Service\Provider\Delivery\Standard( $context, $this->serviceItem );
		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\BasketValues( $provider, $context, $this->serviceItem );
		$this->object->setObject( $this->object );

		$orderProductManager = \Aimeos\MShop::create( $context, 'order/product' );
		$priceManager = \Aimeos\MShop::create( $context, 'price' );

		$productManager = \Aimeos\MShop::create( $context, 'product' );
		$search = $productManager->filter()->add( 'product.code', '==', 'CNC' );

		foreach( $productManager->search( $search ) as $product )
		{
			$orderProduct = $orderProductManager->create();
			$orderProduct->copyFrom( $product );
			$orderProducts[$product->getCode()] = $orderProduct;
		}

		$orderProducts['CNC']->setPrice( $priceManager->create()->setValue( 321 ) );

		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off();
		$this->order->addProduct( $orderProducts['CNC'] );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->order );
		unset( $this->serviceItem );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'basketvalues.total-value-min', $result );
		$this->assertArrayHasKey( 'basketvalues.total-value-max', $result );
	}


	public function testCheckConfigBE()
	{
		$attributes = [
			'basketvalues.total-value-min' => ['EUR' => '10.5'],
			'basketvalues.total-value-max' => ['EUR' => '100'],
		];
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['basketvalues.total-value-min'] );
		$this->assertNull( $result['basketvalues.total-value-max'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( ['basketvalues.total-value-min' => '10.5'] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['basketvalues.total-value-min'] );
		$this->assertNull( $result['basketvalues.total-value-max'] );
	}


	public function testIsAvailable()
	{
		$config = array(
			'basketvalues.total-value-min' => array( 'EUR' =>  320 ),
			'basketvalues.total-value-max' => array( 'EUR' => 1000 ),
		);

		$this->serviceItem->setConfig( $config );
		$result = $this->object->isAvailable( $this->order );

		$this->assertTrue( $result );
	}

	// min value higher than order price
	public function testIsAvailableTestMinValue()
	{
		$config = array(
			'basketvalues.total-value-min' => array( 'EUR' =>  700 ),
			'basketvalues.total-value-max' => array( 'EUR' => 1000 ),
		);

		$this->serviceItem->setConfig( $config );
		$result = $this->object->isAvailable( $this->order );

		$this->assertFalse( $result );
	}

	// order price higher than max price
	public function testIsAvailableTestMaxValue()
	{
		$config = array(
			'basketvalues.total-value-min' => array( 'EUR' =>  50 ),
			'basketvalues.total-value-max' => array( 'EUR' => 320 ),
		);

		$this->serviceItem->setConfig( $config );
		$result = $this->object->isAvailable( $this->order );

		$this->assertFalse( $result );
	}

}
