<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager;


/**
 * Basic methods and constants for order items (shopping basket).
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base extends \Aimeos\MShop\Common\Manager\Base
{
	/**
	 * Returns the address item map for the given order IDs
	 *
	 * @param string[] $ids List of order IDs
	 * @param array $ref List of referenced domains that should be fetched too
	 * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and order address type/item pairs as values
	 */
	protected function getAddresses( array $ids, array $ref ) : \Aimeos\Map
	{
		$manager = $this->object()->getSubManager( 'address' );

		$filter = $manager->filter()
			->add( 'order.address.parentid', '==', $ids )
			->order( ['order.address.type', 'order.address.position', 'order.address.id'] )
			->slice( 0, 0x7fffffff );

		return $manager->search( $filter, $ref )->groupBy( 'order.address.parentid' );
	}


	/**
	 * Returns the coupon map for the given order IDs
	 *
	 * @param string[] $ids List of order IDs
	 * @param array $ref List of referenced domains that should be fetched too
	 * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and product items as values
	 */
	protected function getCoupons( array $ids, array $ref ) : \Aimeos\Map
	{
		$manager = $this->object()->getSubManager( 'coupon' );

		$filter = $manager->filter()
			->add( 'order.coupon.parentid', '==', $ids )
			->order( 'order.coupon.code' )
			->slice( 0, 0x7fffffff );

		return $manager->search( $filter, $ref )->groupBy( 'order.coupon.parentid' );
	}


	/**
	 * Retrieves the ordered products from the storage.
	 *
	 * @param string[] $ids List of order IDs
	 * @param array $ref List of referenced domains that should be fetched too
	 * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and order product IDs/items pairs as values
	 */
	protected function getProducts( array $ids, array $ref ) : \Aimeos\Map
	{
		$manager = $this->object()->getSubManager( 'product' );

		$filter = $manager->filter()
			->add( 'order.product.parentid', '==', $ids )
			->order( 'order.product.position' )
			->slice( 0, 0x7fffffff );
		$items = $manager->search( $filter, $ref );
		$map = $items->groupBy( 'order.product.orderproductid' );

		foreach( $map as $id => $list ) {
			$items[$id]?->setProducts( $list );
		}

		return map( $map->get( '' ) )->groupBy( 'order.product.parentid' );
	}


	/**
	 * Retrieves the order services from the storage.
	 *
	 * @param string[] $ids List of order IDs
	 * @param array $ref List of referenced domains that should be fetched too
	 * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and service type/items pairs as values
	 */
	protected function getServices( array $ids, array $ref ) : \Aimeos\Map
	{
		$manager = $this->object()->getSubManager( 'service' );

		$filter = $manager->filter()
			->add( 'order.service.parentid', '==', $ids )
			->order( ['order.service.type', 'order.service.position', 'order.service.id'] )
			->slice( 0, 0x7fffffff );

		return $manager->search( $filter, $ref )->groupBy( 'order.service.parentid' );
	}


	/**
	 * Saves the addresses of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Basket containing address items
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	protected function saveAddresses( \Aimeos\MShop\Order\Item\Iface $item ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$addresses = $item->getAddresses();

		foreach( $addresses as $type => $list )
		{
			$pos = 0;

			foreach( $list as $address )
			{
				if( $address->getParentId() != $item->getId() ) {
					$address->setId( null ); // create new item if copied
				}

				$address->setParentId( $item->getId() )->setPosition( ++$pos );
			}
		}

		$this->object()->getSubManager( 'address' )->save( $addresses->flat( 1 ) );

		return $this;
	}


	/**
	 * Saves the coupons of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket containing coupon items
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	protected function saveCoupons( \Aimeos\MShop\Order\Item\Iface $basket ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$manager = $this->object()->getSubManager( 'coupon' );
		$filter = $manager->filter()->add( 'order.coupon.parentid', '==', $basket->getId() )->slice( 0, 0x7fffffff );
		$items = $manager->search( $filter )->groupBy( 'order.coupon.code' );

		foreach( $basket->getCoupons() as $code => $products )
		{
			if( empty( $products ) )
			{
				$item = !empty( $items[$code] ) ? current( $items[$code] ) : $manager->create()->setParentId( $basket->getId() );
				$manager->save( $item->setCode( $code ) );
				continue;
			}

			foreach( $products as $product )
			{
				foreach( $items[$code] ?? [] as $prodItem )
				{
					if( $product->getId() === $prodItem->getId() ) {
						continue 2;
					}
				}

				$manager->save( $manager->create()->setParentId( $basket->getId() )->setCode( $code )->setProductId( $product->getId() ) );
			}
		}

		return $this;
	}


	/**
	 * Saves the ordered products to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket containing ordered products or bundles
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	protected function saveProducts( \Aimeos\MShop\Order\Item\Iface $basket ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$pos = 0;
		$products = $basket->getProducts();

		foreach( $products as $product )
		{
			if( $product->getParentId() != $basket->getId() ) {
				$product->setId( null ); // create new item if copied
			}

			$product->setParentId( $basket->getId() )->setPosition( ++$pos );

			foreach( $product->getProducts() as $subProduct )
			{
				if( $subProduct->getParentId() != $basket->getId() ) {
					$subProduct->setId( null ); // create new item if copied
				}

				$subProduct->setParentId( $basket->getId() )->setPosition( ++$pos );
			}
		}

		$this->object()->getSubManager( 'product' )->save( $products );

		return $this;
	}


	/**
	 * Saves the services of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Basket containing service items
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	protected function saveServices( \Aimeos\MShop\Order\Item\Iface $item ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$services = $item->getServices();

		foreach( $services as $type => $list )
		{
			$pos = 0;

			foreach( $list as $service )
			{
				if( $service->getParentId() != $item->getId() ) {
					$service->setId( null ); // create new item if copied
				}

				$service->setParentId( $item->getId() )->setPosition( ++$pos );
			}
		}

		$this->object()->getSubManager( 'service' )->save( $services->flat( 1 ) );

		return $this;
	}
}
