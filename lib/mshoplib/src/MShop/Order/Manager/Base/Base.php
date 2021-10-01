<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Base;


/**
 * Basic methods and constants for order base items (shopping basket).
 *
 * @package MShop
 * @subpackage Order
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
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
	 * Returns a new and empty order base item (shopping basket).
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $products List of ordered product items
	 * @param \Aimeos\MShop\Order\Item\Base\Address\Iface[] $addresses List of order address items
	 * @param \Aimeos\MShop\Order\Item\Base\Service\Iface[] $services List of order serviceitems
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface[] $coupons Associative list of coupon codes as keys and items as values
	 * @param \Aimeos\MShop\Customer\Item\Iface|null $custItem Customer item object if requested
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base object
	 */
	abstract protected function createItemBase( \Aimeos\MShop\Price\Item\Iface $price, \Aimeos\MShop\Locale\Item\Iface $locale,
		array $values = [], array $products = [], array $addresses = [], array $services = [], array $coupons = [],
		?\Aimeos\MShop\Customer\Item\Iface $custItem = null ) : \Aimeos\MShop\Order\Item\Base\Iface;


	/**
	 * Returns the current basket of the customer.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Shopping basket
	 */
	public function getSession( string $type = 'default' ) : \Aimeos\MShop\Order\Item\Base\Iface
	{
		$context = $this->getContext();
		$session = $context->getSession();
		$locale = $context->getLocale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSiteItem()->getCode();
		$key = 'aimeos/basket/content-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		if( ( $serorder = $session->get( $key ) ) === null ) {
			return $this->getObject()->create();
		}

		$iface = \Aimeos\MShop\Order\Item\Base\Iface::class;

		if( ( $order = unserialize( $serorder ) ) === false || !( $order instanceof $iface ) )
		{
			$msg = sprintf( 'Invalid serialized basket. "%1$s" returns "%2$s".', __METHOD__, $serorder );
			$context->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN, 'core/order' );

			return $this->getObject()->create();
		}

		\Aimeos\MShop::create( $context, 'plugin' )->register( $order, 'order' );

		return $order;
	}


	/**
	 * Returns the current lock status of the basket.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return int Lock status (@see \Aimeos\MShop\Order\Manager\Base\Base)
	 */
	public function getSessionLock( string $type = 'default' ) : int
	{
		$context = $this->getContext();
		$session = $context->getSession();
		$locale = $context->getLocale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSiteItem()->getCode();
		$key = 'aimeos/basket/lock-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		if( ( $value = $session->get( $key ) ) !== null ) {
			return (int) $value;
		}

		return \Aimeos\MShop\Order\Manager\Base\Base::LOCK_DISABLE;
	}


	/**
	 * Saves the current shopping basket of the customer.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $order Shopping basket
	 * @param string $type Order type if a customer can have more than one order at once
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 */
	public function setSession( \Aimeos\MShop\Order\Item\Base\Iface $order, string $type = 'default' ) : \Aimeos\MShop\Order\Manager\Base\Iface
	{
		$context = $this->getContext();
		$session = $context->getSession();
		$locale = $context->getLocale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSiteItem()->getCode();
		$key = 'aimeos/basket/content-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		$list = $session->get( 'aimeos/basket/list', [] );
		$list[$key] = $key;

		$session->set( 'aimeos/basket/list', $list );
		$session->set( $key, serialize( clone $order ) );

		return $this;
	}


	/**
	 * Locks or unlocks the session by setting the lock value.
	 * The lock is a cooperative lock and you have to check the lock value before you proceed.
	 *
	 * @param int $lock Lock value (@see \Aimeos\MShop\Order\Manager\Base\Base)
	 * @param string $type Order type if a customer can have more than one order at once
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 */
	public function setSessionLock( int $lock, string $type = 'default' ) : \Aimeos\MShop\Order\Manager\Base\Iface
	{
		$this->checkLock( $lock );

		$context = $this->getContext();
		$session = $context->getSession();
		$locale = $context->getLocale();
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
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 * @throws \Aimeos\MShop\Order\Exception If given value is invalid
	 */
	protected function checkLock( int $value ) : \Aimeos\MShop\Order\Manager\Base\Iface
	{
		switch( $value )
		{
			case \Aimeos\MShop\Order\Manager\Base\Base::LOCK_DISABLE:
			case \Aimeos\MShop\Order\Manager\Base\Base::LOCK_ENABLE:
				return $this;
		}

		$msg = $this->getContext()->translate( 'mshop', 'Lock flag "%1$d" not within allowed range' );
		throw new \Aimeos\MShop\Order\Exception( sprintf( $msg, $value ) );
	}


	/**
	 * Returns the address item map for the given order base IDs
	 *
	 * @param string[] $baseIds List of order base IDs
	 * @param bool $fresh Create new items by copying the existing ones and remove their IDs
	 * @return array Multi-dimensional associative list of order base IDs as keys and order address type/item pairs as values
	 */
	protected function getAddresses( array $baseIds, bool $fresh = false ) : array
	{
		$items = [];
		$manager = $this->getObject()->getSubManager( 'address' );

		$criteria = $manager->filter()->slice( 0, 0x7fffffff );
		$criteria->setConditions( $criteria->compare( '==', 'order.base.address.baseid', $baseIds ) );

		foreach( $manager->search( $criteria ) as $item )
		{
			if( $fresh === true )
			{
				$item->setBaseId( null );
				$item->setId( null );
			}

			$items[$item->getBaseId()][] = $item;
		}

		return $items;
	}


	/**
	 * Returns the coupon map for the given order base IDs
	 *
	 * @param string[] $baseIds List of order base IDs
	 * @param bool $fresh Create new items by copying the existing ones and remove their IDs
	 * @param array $products Associative list of base IDs and order product ID/item pairs as values
	 * @return array Multi-dimensional associative list of order base IDs as keys and coupons with product items as values
	 */
	protected function getCoupons( array $baseIds, bool $fresh = false, array $products = [] ) : array
	{
		$map = $productMap = [];
		$manager = $this->getObject()->getSubManager( 'coupon' );

		foreach( $products as $baseId => $list )
		{
			if( !isset( $productMap[$baseId] ) ) {
				$productMap[$baseId] = [];
			}

			foreach( $list as $key => $product )
			{
				$productMap[$baseId][$product->getId()] = $product;

				if( $fresh === true )
				{
					$product->setPosition( null );
					$product->setBaseId( null );
					$product->setId( null );
				}
			}
		}

		$criteria = $manager->filter()->slice( 0, 0x7fffffff );
		$criteria->setConditions( $criteria->compare( '==', 'order.base.coupon.baseid', $baseIds ) );

		foreach( $manager->search( $criteria ) as $item )
		{
			if( !isset( $map[$item->getBaseId()][$item->getCode()] ) ) {
				$map[$item->getBaseId()][$item->getCode()] = [];
			}

			if( $item->getProductId() !== null && isset( $productMap[$item->getBaseId()][$item->getProductId()] ) ) {
				$map[$item->getBaseId()][$item->getCode()][] = $productMap[$item->getBaseId()][$item->getProductId()];
			}
		}

		return $map;
	}


	/**
	 * Retrieves the ordered products from the storage.
	 *
	 * @param string[] $baseIds List of order base IDs
	 * @param bool $fresh Create new items by copying the existing ones and remove their IDs
	 * @return array Multi-dimensional associative list of order base IDs as keys and order product
	 *	IDs/items pairs in reversed order as values
	 */
	protected function getProducts( array $baseIds, bool $fresh = false ) : array
	{
		$map = $attributes = $subProducts = [];
		$manager = $this->getObject()->getSubManager( 'product' );
		$attrManager = $manager->getSubManager( 'attribute' );

		$criteria = $manager->filter()->slice( 0, 0x7fffffff );
		$criteria->setConditions( $criteria->compare( '==', 'order.base.product.baseid', $baseIds ) );
		$items = $manager->search( $criteria )->reverse();

		$search = $attrManager->filter()->slice( 0, 0x7fffffff );
		$search->setConditions( $search->compare( '==', 'order.base.product.attribute.parentid', $items->keys()->toArray() ) );

		foreach( $attrManager->search( $search ) as $id => $attribute )
		{
			if( $fresh === true )
			{
				$attributes[$attribute->getParentId()][] = $attribute;
				$attribute->setParentId( null );
				$attribute->setId( null );
			}
			else
			{
				$attributes[$attribute->getParentId()][$id] = $attribute;
			}
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
				$map[$item->getBaseId()][$item->getPosition()] = $item;

				$subProducts = [];
			}
			else
			{	// in case it's a sub-product
				$subProducts[$item->getPosition()] = $item;
			}

			if( $fresh === true )
			{
				$item->setPosition( null );
				$item->setBaseId( null );
				$item->setId( null );
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
	 * @param string[] $baseIds List of order base IDs
	 * @param bool $fresh Create new items by copying the existing ones and remove their IDs
	 * @return array Multi-dimensional associative list of order base IDs as keys and service type/items pairs as values
	 */
	protected function getServices( array $baseIds, bool $fresh = false ) : array
	{
		$map = [];
		$manager = $this->getObject()->getSubManager( 'service' );

		$criteria = $manager->filter()->slice( 0, 0x7fffffff );
		$criteria->setConditions( $criteria->compare( '==', 'order.base.service.baseid', $baseIds ) );

		foreach( $manager->search( $criteria ) as $item )
		{
			if( $fresh === true )
			{
				foreach( $item->getAttributeItems() as $attribute )
				{
						$attribute->setId( null );
						$attribute->setParentId( null );
				}

				$item->setBaseId( null );
				$item->setId( null );
			}

			$map[$item->getBaseId()][] = $item;
		}

		return $map;
	}


	/**
	 * Load the basket item for the given ID.
	 *
	 * @param string $id Unique order base ID
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price object with total order value
	 * @param \Aimeos\MShop\Locale\Item\Iface $localeItem Locale object of the order
	 * @param array $row Array of values with all relevant order information
	 * @param int $parts Bitmap of the basket parts that should be loaded
	 * @return \Aimeos\MShop\Order\Item\Base\Iface The loaded order item for the given ID
	 */
	protected function loadItems( string $id, \Aimeos\MShop\Price\Item\Iface $price,
		\Aimeos\MShop\Locale\Item\Iface $localeItem, array $row, int $parts )
	{
		$products = $coupons = $addresses = $services = [];

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT
			|| $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_COUPON
		) {
			$products = $this->loadProducts( $id, false );
		}

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_COUPON ) {
			$coupons = $this->loadCoupons( $id, false, $products );
		}

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) {
			$addresses = $this->loadAddresses( $id, false );
		}

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) {
			$services = $this->loadServices( $id, false );
		}

		$basket = $this->createItemBase( $price, $localeItem, $row, $products, $addresses, $services, $coupons );

		return $basket;
	}


	/**
	 * Create a new basket item as a clone from an existing order ID.
	 *
	 * @param string $id Unique order base ID
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price object with total order value
	 * @param \Aimeos\MShop\Locale\Item\Iface $localeItem Locale object of the order
	 * @param array $row Array of values with all relevant order information
	 * @param int $parts Bitmap of the basket parts that should be loaded
	 * @return \Aimeos\MShop\Order\Item\Base\Standard The loaded order item for the given ID
	 */
	protected function loadFresh( string $id, \Aimeos\MShop\Price\Item\Iface $price,
		\Aimeos\MShop\Locale\Item\Iface $localeItem, array $row, int $parts )
	{
		$products = $coupons = $addresses = $services = [];

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) {
			$products = $this->loadProducts( $id, true );
		}

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_COUPON ) {
			// load coupons with product array containing product ids for coupon/product matching
			// not very efficient, a better solution might be considered for 2020.01 release
			// see https://github.com/aimeos/aimeos-core/pull/175 for discussion
			$coupons = $this->loadCoupons( $id, true, $this->loadProducts( $id, false ) );
		}

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) {
			$addresses = $this->loadAddresses( $id, true );
		}

		if( $parts & \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) {
			$services = $this->loadServices( $id, true );
		}

		$basket = $this->createItemBase( $price, $localeItem, $row );
		$basket->setId( null );

		foreach( $products as $item )
		{
			if( !( $item->getFlags() & \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE ) ) {
				$basket->addProduct( $item );
			}
		}

		foreach( $coupons as $code => $items ) {
			$basket->addCoupon( $code );
		}

		foreach( $addresses as $item ) {
			$basket->addAddress( $item, $item->getType() );
		}

		foreach( $services as $item ) {
			$basket->addService( $item, $item->getType() );
		}

		return $basket;
	}


	/**
	 * Retrieves the addresses of the order from the storage.
	 *
	 * @param string $id Order base ID
	 * @param bool $fresh Create new items by copying the existing ones and remove their IDs
	 * @return \Aimeos\MShop\Order\Item\Base\Address\Iface[] List of order address items
	 */
	protected function loadAddresses( string $id, bool $fresh ) : array
	{
		$map = $this->getAddresses( [$id], $fresh );

		if( ( $items = reset( $map ) ) !== false ) {
			return $items;
		}

		return [];
	}


	/**
	 * Retrieves the coupons of the order from the storage.
	 *
	 * @param string $id Order base ID
	 * @param bool $fresh Create new items by copying the existing ones and remove their IDs
	 * @param array $products Multi-dimensional associative list of order base IDs as keys and order product
	 *	IDs/items pairs in reversed order as values
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface[] Associative list of coupon codes as keys and items as values
	 */
	protected function loadCoupons( string $id, bool $fresh, array $products ) : array
	{
		$map = $this->getCoupons( [$id], $fresh, [$id => $products] );

		if( ( $items = reset( $map ) ) !== false ) {
			return $items;
		}

		return [];
	}


	/**
	 * Retrieves the ordered products from the storage.
	 *
	 * @param string $id Order base ID
	 * @param bool $fresh Create new items by copying the existing ones and remove their IDs
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface[] List of product items
	 */
	protected function loadProducts( string $id, bool $fresh ) : array
	{
		$items = current( $this->getProducts( [$id], $fresh ) );
		return $items ?: [];
	}


	/**
	 * Retrieves the services of the order from the storage.
	 *
	 * @param string $id Order base ID
	 * @param bool $fresh Create new items by copying the existing ones and remove their IDs
	 * @return \Aimeos\MShop\Order\Item\Base\Service\Iface[] List of order service items
	 */
	protected function loadServices( string $id, bool $fresh ) : array
	{
		$map = $this->getServices( [$id], $fresh );

		if( ( $items = reset( $map ) ) !== false ) {
			return $items;
		}

		return [];
	}


	/**
	 * Saves the ordered products to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing ordered products or bundles
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 */
	protected function storeProducts( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : \Aimeos\MShop\Order\Manager\Base\Iface
	{
		$position = 0;
		$manager = $this->getObject()->getSubManager( 'product' );
		$attrManager = $manager->getSubManager( 'attribute' );

		foreach( $basket->getProducts() as $item )
		{
			$baseId = $basket->getId();
			$item->setBaseId( $baseId );

			if( ( $pos = $item->getPosition() ) === null ) {
				$item = $item->setPosition( $position++ );
			} else {
				$position = ++$pos;
			}

			$item = $manager->save( $item );
			$productId = $item->getId();

			foreach( $item->getAttributeItems() as $attribute )
			{
				$attribute->setParentId( $productId );
				$attrManager->save( $attribute );
			}

			// if the item is a bundle, it probably contains sub-products
			foreach( $item->getProducts() as $subProduct )
			{
				$subProduct->setBaseId( $baseId );
				$subProduct->setOrderProductId( $productId );

				if( ( $pos = $subProduct->getPosition() ) === null ) {
					$subProduct = $subProduct->setPosition( $position++ );
				} else {
					$position = ++$pos;
				}

				$subProduct = $manager->save( $subProduct );
				$subProductId = $subProduct->getId();

				foreach( $subProduct->getAttributeItems() as $attribute )
				{
					$attribute->setParentId( $subProductId );
					$attrManager->save( $attribute );
				}
			}
		}

		return $this;
	}


	/**
	 * Saves the addresses of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing address items
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 */
	protected function storeAddresses( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : \Aimeos\MShop\Order\Manager\Base\Iface
	{
		$position = 0;
		$manager = $this->getObject()->getSubManager( 'address' );

		foreach( $basket->getAddresses() as $type => $list )
		{
			foreach( $list as $item )
			{
				if( ( $pos = $item->getPosition() ) === null ) {
					$item = $item->setPosition( $position++ );
				} else {
					$position = ++$pos;
				}

				$manager->save( $item->setBaseId( $basket->getId() ) );
			}
		}

		return $this;
	}


	/**
	 * Saves the coupons of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing coupon items
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 */
	protected function storeCoupons( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : \Aimeos\MShop\Order\Manager\Base\Iface
	{
		$manager = $this->getObject()->getSubManager( 'coupon' );

		$item = $manager->create();
		$item->setBaseId( $basket->getId() );

		foreach( $basket->getCoupons() as $code => $products )
		{
			$item->setCode( $code );

			if( empty( $products ) )
			{
				$item->setId( null );
				$manager->save( $item );
				continue;
			}

			foreach( $products as $product )
			{
				$item->setId( null );
				$item->setProductId( $product->getId() );
				$manager->save( $item );
			}
		}

		return $this;
	}


	/**
	 * Saves the services of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing service items
	 * @return \Aimeos\MShop\Order\Manager\Base\Iface Manager object for chaining method calls
	 */
	protected function storeServices( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : \Aimeos\MShop\Order\Manager\Base\Iface
	{
		$manager = $this->getObject()->getSubManager( 'service' );
		$attrManager = $manager->getSubManager( 'attribute' );
		$position = 0;

		foreach( $basket->getServices() as $type => $list )
		{
			foreach( $list as $item )
			{
				if( ( $pos = $item->getPosition() ) === null ) {
					$item = $item->setPosition( $position++ );
				} else {
					$position = ++$pos;
				}

				$item = $item->setBaseId( $basket->getId() )->setType( $type );
				$item = $manager->save( $item );

				foreach( $item->getAttributeItems() as $attribute )
				{
					if( $attribute->getType() !== 'session' )
					{
						$attribute->setParentId( $item->getId() );
						$attrManager->save( $attribute );
					}
				}
			}
		}

		return $this;
	}
}
