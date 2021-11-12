<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
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
		$this->info( 'Adding order test data', 'v' );

		$context = $this->context();
		$context->setEditor( 'core:lib/mshoplib' );
		$context->getLocale()->setCurrencyId( 'EUR' );

		$manager = $this->getOrderManager( 'base' );
		$filter = $manager->filter()->add( ['order.base.sitecode' => 'unittest'] );
		$manager->delete( $manager->search( $filter ) );

		$ds = DIRECTORY_SEPARATOR;
		$path = __DIR__ . $ds . 'data' . $ds . 'order.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \RuntimeException( sprintf( 'No file "%1$s" found for order domain', $path ) );
		}

		$this->import( $testdata, $this->getCustomer()->getId() );

		$context->getLocale()->setCurrencyId( null );
	}


	protected function import( array $data, string $customerId )
	{
		$orderManager = $this->getOrderManager();
		$orderBaseManager = $this->getOrderManager( 'base' );
		$orderStatusManager = $this->getOrderManager( 'status' );

		$attributes = $this->getAttributes();
		$products = $this->getProducts();
		$services = $this->getServices();

		foreach( $data as $entry )
		{
			if( !isset( $entry['base'] ) ) {
				throw new \RuntimeException( 'No base data found for ' . print_r( $entry, true ) );
			}

			$basket = $orderBaseManager->create()->off()
				->fromArray( $entry['base'], true )->setCustomerId( $customerId );

			$basket->setAddresses( $this->createAddresses( $entry['base']['address'] ?? [] ) );
			$basket->setProducts( $this->createProducts( $entry['base']['product'] ?? [], $products, $attributes ) );
			$basket->setServices( $this->createServices( $entry['base']['service'] ?? [], $services ) );

			foreach( $entry['base']['coupon'] ?? [] as $map )
			{
				$list = [];

				if( ( $pos = $map['ordprodpos'] ?? null ) !== null )
				{
					$list = [$basket->getProduct( $pos )];
					$basket->deleteProduct( $pos );
				}

				$basket->setCoupon( $map['code'], $list );
			}

			$orderBaseManager->store( $basket );

			$item = $orderManager->create()->fromArray( $entry, true );
			$orderManager->save( $item->setBaseId( $basket->getId() ) );

			foreach( $entry['status'] ?? [] as $map ) {
				$orderStatusManager->save( $orderStatusManager->create()->fromArray( $map )->setParentId( $item->getId() ) );
			}
		}
	}


	protected function createAddresses( array $data ) : array
	{
		$list = [];
		$manager = $this->getOrderManager( 'base/address' );

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
		$manager = $this->getOrderManager( 'base/product' );
		$attrManager = $this->getOrderManager( 'base/product/attribute' );

		foreach( $data as $entry )
		{
			$attrs = [];
			foreach( $entry['attribute'] ?? [] as $attr )
			{
				$key = $attr['order.base.product.attribute.code'] . '/' . $attr['order.base.product.attribute.value'];
				$attrs[] = $attrManager->create()->fromArray( $attr, true )
					->setAttributeId( $attributes->get( $key ) );
			}

			$code = $entry['order.base.product.prodcode'] ?? null;
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
		$manager = $this->getOrderManager( 'base/service' );
		$attrManager = $this->getOrderManager( 'base/service/attribute' );

		foreach( $data as $entry )
		{
			$attrs = [];
			foreach( $entry['attribute'] ?? [] as $attr ) {
				$attrs[] = $attrManager->create()->fromArray( $attr, true );
			}

			$code = $entry['order.base.service.code'] ?? null;
			$price = $priceManager->create()->fromArray( $entry, true );

			$item = $manager->create()->fromArray( $entry, true )
				->setAttributeItems( $attrs )->setPrice( $price )
				->setServiceId( $services->get( $code ) ?: '' );

			$list[$item->getType()][] = $item;
		}

		return $list;
	}


	protected function getAttributes() : \Aimeos\Map
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::create( $this->context(), 'Standard' );

		return $attributeManager->search( $attributeManager->filter() )
			->groupBy( 'attribute.type' )->map( function( $list ) {
				return map( $list )->col( 'attribute.id', 'attribute.code' );
			} );
	}


	protected function getCustomer() : \Aimeos\MShop\Customer\Item\Iface
	{
		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::create( $this->context(), 'Standard' );
		return $customerManager->find( 'test@example.com' );
	}


	protected function getProducts() : \Aimeos\Map
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context(), 'Standard' );
		return $productManager->search( $productManager->filter() )->col( 'product.id', 'product.code' );
	}


	protected function getServices() : \Aimeos\Map
	{
		$serviceManager = \Aimeos\MShop\Service\Manager\Factory::create( $this->context(), 'Standard' );
		return $serviceManager->search( $serviceManager->filter() )->col( 'service.id', 'service.code' );
	}


	protected function getOrderManager( $path = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context(), 'Standard' );

		if( $path )
		{
			foreach( explode( '/', $path ) as $part ) {
				$manager = $manager->getSubManager( $part );
			}
		}

		return $manager;
	}


	protected function getPriceManager() : \Aimeos\MShop\Common\Manager\Iface
	{
		return \Aimeos\MShop\Price\Manager\Factory::create( $this->context(), 'Standard' );
	}
}
