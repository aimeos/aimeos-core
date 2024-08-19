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
	 * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and order address ID/item pairs as values
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
	 * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and order coupon ID/item pairs as values
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
	 * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and order product ID/item pairs as values
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
	 * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and service ID/item pairs as values
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
	 * Retrieves the order statuses from the storage.
	 *
	 * @param string[] $ids List of order IDs
	 * @param array $ref List of referenced domains that should be fetched too
	 * @return \Aimeos\Map Multi-dimensional associative list of order IDs as keys and order status ID/item pairs as values
	 */
	protected function getStatuses( array $ids, array $ref ) : \Aimeos\Map
	{
		$manager = $this->object()->getSubManager( 'status' );

		$filter = $manager->filter()
			->add( 'order.status.parentid', '==', $ids )
			->slice( 0, 0x7fffffff );

		return $manager->search( $filter, $ref )->groupBy( 'order.status.parentid' );
	}


	/**
	 * Saves the addresses of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order containing address items
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
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order containing coupon items
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	protected function saveCoupons( \Aimeos\MShop\Order\Item\Iface $item ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$list = [];
		$manager = $this->object()->getSubManager( 'coupon' );
		$filter = $manager->filter()->add( 'order.coupon.parentid', '==', $item->getId() )->slice( 0, 0x7fffffff );
		$items = $manager->search( $filter )->groupBy( 'order.coupon.code' );

		foreach( $item->getCoupons() as $code => $products )
		{
			if( empty( $products ) )
			{
				$list[] = current( $items[$code] ) ?: $manager->create()->setParentId( $item->getId() )->setCode( $code );
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

				$list[] = $manager->create()->setParentId( $item->getId() )->setCode( $code )->setProductId( $product->getId() );
			}
		}

		$manager->save( $list );
		return $this;
	}


	/**
	 * Saves the ordered products to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order containing ordered products or bundles
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	protected function saveProducts( \Aimeos\MShop\Order\Item\Iface $item ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$pos = 0;
		$products = $item->getProducts();

		foreach( $products as $product )
		{
			if( $product->getParentId() != $item->getId() ) {
				$product->setId( null ); // create new item if copied
			}

			$product->setParentId( $item->getId() )->setPosition( ++$pos );

			foreach( $product->getProducts() as $subProduct )
			{
				if( $subProduct->getParentId() != $item->getId() ) {
					$subProduct->setId( null ); // create new item if copied
				}

				$subProduct->setParentId( $item->getId() )->setPosition( ++$pos );
			}
		}

		$this->object()->getSubManager( 'product' )->save( $products );

		return $this;
	}


	/**
	 * Saves the services of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order containing service items
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


	/**
	 * Saves the statuses of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order containing status items
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	protected function saveStatuses( \Aimeos\MShop\Order\Item\Iface $item ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$statuses = $item->getStatuses();

		foreach( $statuses as $type => $list )
		{
			foreach( $list as $status )
			{
				if( $status->getParentId() != $item->getId() ) {
					$status->setId( null ); // create new item if copied
				}

				$status->setParentId( $item->getId() );
			}
		}

		$this->object()->getSubManager( 'status' )->save( $statuses->flat( 1 ) );

		return $this;
	}
}
