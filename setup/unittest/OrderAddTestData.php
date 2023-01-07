<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds order test data.
 */
class OrderAddTestData extends Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['Order', 'CustomerAddTestData', 'ProductAddTestData', 'PluginAddTestData', 'ServiceAddTestData', 'StockAddTestData'];
	}


	/**
	 * Adds order test data.
	 */
	public function up()
	{
		$this->info( 'Adding order test data', 'vv' );

		$context = $this->context();
		$context->setEditor( 'core' );
		$context->locale()->setCurrencyId( 'EUR' );

		$manager = $this->getOrderManager();
		$filter = $manager->filter()->add( ['order.sitecode' => 'unittest'] );
		$manager->delete( $manager->search( $filter ) );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'order.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for order domain', $path ) );
		}

		$this->import( $testdata, $this->getCustomer()->getId() );

		$context->locale()->setCurrencyId( null );
	}


	protected function import( array $data, string $customerId )
	{
		$orderManager = $this->getOrderManager();
		$orderStatusManager = $this->getOrderManager( 'order/status' );

		$attributes = $this->getAttributes();
		$products = $this->getProducts();
		$services = $this->getServices();

		foreach( $data as $entry )
		{
			$basket = $orderManager->create()->off()
				->fromArray( $entry, true )->setCustomerId( $customerId );

			$basket->setAddresses( $this->createAddresses( $entry['address'] ?? [] ) );
			$basket->setProducts( $this->createProducts( $entry['product'] ?? [], $products, $attributes ) );
			$basket->setServices( $this->createServices( $entry['service'] ?? [], $services ) );

			foreach( $entry['coupon'] ?? [] as $map )
			{
				$list = [];

				if( ( $pos = $map['ordprodpos'] ?? null ) !== null )
				{
					$list = [$basket->getProduct( $pos )];
					$basket->deleteProduct( $pos );
				}

				$basket->setCoupon( $map['code'], $list );
			}

			$orderManager->save( $basket );

			foreach( $entry['status'] ?? [] as $map ) {
				$orderStatusManager->save( $orderStatusManager->create()->fromArray( $map )->setParentId( $basket->getId() ) );
			}
		}
	}


	protected function createAddresses( array $data ) : array
	{
		$list = [];
		$manager = $this->getOrderManager( 'order/address' );

		foreach( $data as $entry )
		{
			$item = $manager->create()->fromArray( $entry, true );
			$list[$item->getType()][] = $item;
		}

		return $list;
	}


	protected function createProducts( array $data, \Aimeos\Map $products, \Aimeos\Map $attributes ) : array
	{
		$list = [];
		$priceManager = $this->getPriceManager();
		$manager = $this->getOrderManager( 'order/product' );
		$attrManager = $this->getOrderManager( 'order/product/attribute' );

		foreach( $data as $entry )
		{
			$attrs = [];
			foreach( $entry['attribute'] ?? [] as $attr )
			{
				$key = $attr['order.product.attribute.code'] . '/' . $attr['order.product.attribute.value'];
				$attrs[] = $attrManager->create()->fromArray( $attr, true )
					->setAttributeId( $attributes->get( $key ) );
			}

			$code = $entry['order.product.prodcode'] ?? null;
			$price = $priceManager->create()->fromArray( $entry, true );

			$list[] = $manager->create()->fromArray( $entry, true )
				->setProducts( $this->createProducts( $entry['product'] ?? [], $products, $attributes ) )
				->setAttributeItems( $attrs )->setPrice( $price )
				->setProductId( $products->get( $code ) );
		}

		return $list;
	}


	protected function createServices( array $data, \Aimeos\Map $services ) : array
	{
		$list = [];
		$priceManager = $this->getPriceManager();
		$manager = $this->getOrderManager( 'order/service' );
		$txManager = $this->getOrderManager( 'order/service/transaction' );
		$attrManager = $this->getOrderManager( 'order/service/attribute' );

		foreach( $data as $entry )
		{
			$attrs = [];
			foreach( $entry['attribute'] ?? [] as $attr ) {
				$attrs[] = $attrManager->create()->fromArray( $attr, true );
			}

			$trans = [];
			foreach( $entry['transaction'] ?? [] as $tx ) {
				$trans[] = $txManager->create()->fromArray( $tx, true );
			}

			$code = $entry['order.service.code'] ?? null;
			$price = $priceManager->create()->fromArray( $entry, true );

			$item = $manager->create()->fromArray( $entry, true )
				->setAttributeItems( $attrs )->setPrice( $price )
				->setServiceId( $services->get( $code ) ?: '' )
				->setTransactions( $trans );

			$list[$item->getType()][] = $item;
		}

		return $list;
	}


	protected function getAttributes() : \Aimeos\Map
	{
		$attributeManager = \Aimeos\MShop::create( $this->context(), 'attribute', 'Standard' );

		return $attributeManager->search( $attributeManager->filter() )
			->groupBy( 'attribute.type' )->map( function( $list ) {
				return map( $list )->col( 'attribute.id', 'attribute.code' );
			} );
	}


	protected function getCustomer() : \Aimeos\MShop\Customer\Item\Iface
	{
		$customerManager = \Aimeos\MShop::create( $this->context(), 'customer', 'Standard' );
		return $customerManager->find( 'test@example.com' );
	}


	protected function getProducts() : \Aimeos\Map
	{
		$productManager = \Aimeos\MShop::create( $this->context(), 'product', 'Standard' );
		return $productManager->search( $productManager->filter() )->col( 'product.id', 'product.code' );
	}


	protected function getServices() : \Aimeos\Map
	{
		$serviceManager = \Aimeos\MShop::create( $this->context(), 'service', 'Standard' );
		return $serviceManager->search( $serviceManager->filter() )->col( 'service.id', 'service.code' );
	}


	protected function getOrderManager( $path = 'order' ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return \Aimeos\MShop::create( $this->context(), $path, 'Standard' );
	}


	protected function getPriceManager() : \Aimeos\MShop\Common\Manager\Iface
	{
		return \Aimeos\MShop::create( $this->context(), 'price', 'Standard' );
	}
}
