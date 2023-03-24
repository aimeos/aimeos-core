<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
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
	 * Unlock basket.
	 * Disable the lock for the serialized basket in the session so
	 * modifications of the basket content are allowed again. Note that the
	 * locks are advisory locks that can't be enforced if code doesn't care
	 * about the lock.
	 */
	const LOCK_DISABLE = 0;

	/**
	 * Lock basket.
	 * Enable the lock for the serialized basket in the session so
	 * modifications of the basket content are not allowed any more. Note that
	 * the locks are advisory locks that can't be enforced if code doesn't care
	 * about the lock.
	 */
	const LOCK_ENABLE = 1;


	/**
	 * Returns a new and empty order item (shopping basket).
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $products List of ordered product items
	 * @param \Aimeos\MShop\Order\Item\Address\Iface[] $addresses List of order address items
	 * @param \Aimeos\MShop\Order\Item\Service\Iface[] $services List of order serviceitems
	 * @param \Aimeos\MShop\Order\Item\Product\Iface[] $coupons Associative list of coupon codes as keys and items as values
	 * @param \Aimeos\MShop\Customer\Item\Iface|null $custItem Customer item object if requested
	 * @return \Aimeos\MShop\Order\Item\Iface Order object
	 */
	abstract protected function createItemBase( \Aimeos\MShop\Price\Item\Iface $price, \Aimeos\MShop\Locale\Item\Iface $locale,
		array $values = [], array $products = [], array $addresses = [], array $services = [], array $coupons = [],
		?\Aimeos\MShop\Customer\Item\Iface $custItem = null ) : \Aimeos\MShop\Order\Item\Iface;


	/**
	 * Returns the current basket of the customer.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return \Aimeos\MShop\Order\Item\Iface Shopping basket
	 */
	public function getSession( string $type = 'default' ) : \Aimeos\MShop\Order\Item\Iface
	{
		$context = $this->context();
		$token = $context->token();
		$locale = $context->locale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSiteItem()->getCode();

		$key = $token . '-' . $sitecode . '-' . $language . '-' . $currency . '-' . $type;

		try
		{
			if( ( $order = \Aimeos\MShop::create( $context, 'order/basket' )->get( $key )->getItem() ) === null ) {
				return $this->object()->create();
			}

			\Aimeos\MShop::create( $context, 'plugin' )->register( $order, 'order' );
		}
		catch( \Exception $e )
		{
			return $this->object()->create();
		}

		return $order;
	}


	/**
	 * Returns the current lock status of the basket.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return int Lock status (@see \Aimeos\MShop\Order\Manager\Base)
	 */
	public function getSessionLock( string $type = 'default' ) : int
	{
		$context = $this->context();
		$session = $context->session();
		$locale = $context->locale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSiteItem()->getCode();
		$key = 'aimeos/basket/lock-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		if( ( $value = $session->get( $key ) ) !== null ) {
			return (int) $value;
		}

		return \Aimeos\MShop\Order\Manager\Base::LOCK_DISABLE;
	}


	/**
	 * Saves the current shopping basket of the customer.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Shopping basket
	 * @param string $type Order type if a customer can have more than one order at once
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	public function setSession( \Aimeos\MShop\Order\Item\Iface $order, string $type = 'default' ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$context = $this->context();
		$token = $context->token();
		$locale = $context->locale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSiteItem()->getCode();

		$key = $token . '-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		$session = $context->session();

		$list = $session->get( 'aimeos/basket/list', [] );
		$list[$key] = $key;

		$session->set( 'aimeos/basket/list', $list );

		$manager = \Aimeos\MShop::create( $context, 'order/basket' );
		$manager->save( $manager->create()->setId( $key )->setCustomerId( $context->user() )->setItem( clone $order ) );

		return $this;
	}


	/**
	 * Locks or unlocks the session by setting the lock value.
	 * The lock is a cooperative lock and you have to check the lock value before you proceed.
	 *
	 * @param int $lock Lock value (@see \Aimeos\MShop\Order\Manager\Base)
	 * @param string $type Order type if a customer can have more than one order at once
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	public function setSessionLock( int $lock, string $type = 'default' ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$this->checkLock( $lock );

		$context = $this->context();
		$session = $context->session();
		$locale = $context->locale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSiteItem()->getCode();
		$key = 'aimeos/basket/lock-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		$session->set( $key, strval( $lock ) );

		return $this;
	}


	/**
	 * Checks if the lock value is a valid constant.
	 *
	 * @param int $value Lock constant
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If given value is invalid
	 */
	protected function checkLock( int $value ) : \Aimeos\MShop\Order\Manager\Iface
	{
		switch( $value )
		{
			case \Aimeos\MShop\Order\Manager\Base::LOCK_DISABLE:
			case \Aimeos\MShop\Order\Manager\Base::LOCK_ENABLE:
				return $this;
		}

		$msg = $this->context()->translate( 'mshop', 'Lock flag "%1$d" not within allowed range' );
		throw new \Aimeos\MShop\Order\Exception( sprintf( $msg, $value ) );
	}


	/**
	 * Returns the address item map for the given order IDs
	 *
	 * @param string[] $ids List of order IDs
	 * @return array Multi-dimensional associative list of order IDs as keys and order address type/item pairs as values
	 */
	protected function getAddresses( array $ids ) : array
	{
		$items = [];
		$manager = $this->object()->getSubManager( 'address' );

		$criteria = $manager->filter()->slice( 0, 0x7fffffff );
		$criteria->setConditions( $criteria->compare( '==', 'order.address.parentid', $ids ) );

		foreach( $manager->search( $criteria ) as $item ) {
			$items[$item->getParentId()][] = $item;
		}

		return $items;
	}


	/**
	 * Returns the coupon map for the given order IDs
	 *
	 * @param string[] $ids List of order IDs
	 * @param array $products Associative list of IDs and order product ID/item pairs as values
	 * @return array Multi-dimensional associative list of order IDs as keys and coupons with product items as values
	 */
	protected function getCoupons( array $ids, array $products = [] ) : array
	{
		$map = $productMap = [];
		$manager = $this->object()->getSubManager( 'coupon' );

		foreach( $products as $id => $list )
		{
			if( !isset( $productMap[$id] ) ) {
				$productMap[$id] = [];
			}

			foreach( $list as $key => $product ) {
				$productMap[$id][$product->getId()] = $product;
			}
		}

		$criteria = $manager->filter()->slice( 0, 0x7fffffff );
		$criteria->setConditions( $criteria->compare( '==', 'order.coupon.parentid', $ids ) );

		foreach( $manager->search( $criteria ) as $item )
		{
			if( !isset( $map[$item->getParentId()][$item->getCode()] ) ) {
				$map[$item->getParentId()][$item->getCode()] = [];
			}

			if( $item->getProductId() !== null && isset( $productMap[$item->getParentId()][$item->getProductId()] ) ) {
				$map[$item->getParentId()][$item->getCode()][] = $productMap[$item->getParentId()][$item->getProductId()];
			}
		}

		return $map;
	}


	/**
	 * Retrieves the ordered products from the storage.
	 *
	 * @param string[] $ids List of order IDs
	 * @return array Multi-dimensional associative list of order IDs as keys and order product
	 *	IDs/items pairs in reversed order as values
	 */
	protected function getProducts( array $ids ) : array
	{
		$map = $attributes = $subProducts = [];
		$manager = $this->object()->getSubManager( 'product' );
		$attrManager = $manager->getSubManager( 'attribute' );

		$criteria = $manager->filter()->slice( 0, 0x7fffffff );
		$criteria->setConditions( $criteria->compare( '==', 'order.product.parentid', $ids ) );
		$items = $manager->search( $criteria )->reverse();

		$search = $attrManager->filter()->slice( 0, 0x7fffffff );
		$search->setConditions( $search->compare( '==', 'order.product.attribute.parentid', $items->keys()->toArray() ) );

		foreach( $attrManager->search( $search ) as $id => $attribute ) {
			$attributes[$attribute->getParentId()][$id] = $attribute;
		}

		foreach( $items as $id => $item )
		{
			if( isset( $attributes[$id] ) ) {
				$item->setAttributeItems( $attributes[$id] );
			}

			if( $item->getOrderProductId() === null )
			{
				ksort( $subProducts ); // bring the array into the right order because it's reversed
				$item->setProducts( $subProducts );
				$map[$item->getParentId()][$item->getPosition()] = $item;

				$subProducts = [];
			}
			else
			{	// in case it's a sub-product
				$subProducts[$item->getPosition()] = $item;
			}
		}

		foreach( $map as $key => $list ) {
			ksort( $map[$key] );
		}

		return $map;
	}


	/**
	 * Retrieves the order services from the storage.
	 *
	 * @param string[] $ids List of order IDs
	 * @return array Multi-dimensional associative list of order IDs as keys and service type/items pairs as values
	 */
	protected function getServices( array $ids ) : array
	{
		$map = [];
		$manager = $this->object()->getSubManager( 'service' );

		$criteria = $manager->filter()->slice( 0, 0x7fffffff );
		$criteria->setConditions( $criteria->compare( '==', 'order.service.parentid', $ids ) );

		foreach( $manager->search( $criteria ) as $item ) {
			$map[$item->getParentId()][] = $item;
		}

		return $map;
	}


	/**
	 * Saves the addresses of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket containing address items
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	protected function saveAddresses( \Aimeos\MShop\Order\Item\Iface $basket ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$addresses = $basket->getAddresses()->flat( 1 );

		foreach( $addresses as $address )
		{
			if( $address->getParentId() != $basket->getId() ) {
				$address->setId( null ); // create new item if copied
			}

			$address->setParentId( $basket->getId() );
		}

		$this->object()->getSubManager( 'address' )->save( $addresses );

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
		$products = $basket->getProducts();
		$pos = (int) $products->merge( $products->getProducts()->flat( 1 ) )->max( 'order.product.position' );

		foreach( $products as $product )
		{
			if( $product->getParentId() != $basket->getId() ) {
				$product->setId( null ); // create new item if copied
			}

			if( !$product->getPosition() ) {
				$product->setPosition( ++$pos );
			}

			$product->setParentId( $basket->getId() );

			foreach( $product->getProducts() as $subProduct )
			{
				if( $subProduct->getParentId() != $basket->getId() ) {
					$subProduct->setId( null ); // create new item if copied
				}

				if( !$subProduct->getPosition() ) {
					$subProduct->setPosition( ++$pos );
				}

				$subProduct->setParentId( $basket->getId() );
			}
		}

		$this->object()->getSubManager( 'product' )->save( $products );

		return $this;
	}


	/**
	 * Saves the services of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $basket Basket containing service items
	 * @return \Aimeos\MShop\Order\Manager\Iface Manager object for chaining method calls
	 */
	protected function saveServices( \Aimeos\MShop\Order\Item\Iface $basket ) : \Aimeos\MShop\Order\Manager\Iface
	{
		$services = $basket->getServices()->flat( 1 );

		foreach( $services as $service )
		{
			if( $service->getParentId() != $basket->getId() ) {
				$service->setId( null ); // create new item if copied
			}

			$service->setParentId( $basket->getId() );
		}

		$this->object()->getSubManager( 'service' )->save( $services );

		return $this;
	}
}
