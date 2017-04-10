<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	implements \Aimeos\MShop\Order\Manager\Base\Iface
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
	 * Load/store no additional order information
	 */
	const PARTS_NONE = 0;

	/**
	 * Load/store order addresses
	 * Only the addresses of the order will be loaded additionally to the base
	 * order information.
	 */
	const PARTS_ADDRESS = 1;

	/**
	 * Load/store order coupons
	 * Only the coupon information stored in the order will be loaded additionally
	 * to the base order information.
	 */
	const PARTS_COUPON = 2;

	/**
	 * Load/store order products
	 * Only the ordered products and their associated data of the order will be
	 * loaded additionally to the base order information.
	 */
	const PARTS_PRODUCT = 4;

	/**
	 * Load/store order services
	 * Only the services (delivery, payment, etc.) and their associated data of
	 * the order will be loaded additionally to the base order information.
	 */
	const PARTS_SERVICE = 8;

	/**
	 * Load/store all order content
	 * The complete order with all associated data will be loaded additionally
	 * to the base order information. This is the same as the basket content
	 * of the customer when purchased.
	 */
	const PARTS_ALL = 15;


	/**
	 * Returns the current basket of the customer.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Shopping basket
	 */
	public function getSession( $type = 'default' )
	{
		$context = $this->getContext();
		$session = $context->getSession();
		$locale = $context->getLocale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSite()->getCode();
		$key = 'aimeos/basket/content-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		if( ( $serorder = $session->get( $key ) ) === null ) {
			return $this->createItem();
		}

		$iface = '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface';

		if( ( $order = unserialize( $serorder ) ) === false || !( $order instanceof $iface ) )
		{
			$msg = sprintf( 'Invalid serialized basket. "%1$s" returns "%2$s".', __METHOD__, $serorder );
			$context->getLogger()->log( $msg, \Aimeos\MW\Logger\Base::WARN );

			return $this->createItem();
		}

		\Aimeos\MShop\Factory::createManager( $context, 'plugin' )->register( $order, 'order' );

		return $order;
	}


	/**
	 * Returns the current lock status of the basket.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return integer Lock status (@see \Aimeos\MShop\Order\Manager\Base\Base)
	 */
	public function getSessionLock( $type = 'default' )
	{
		$context = $this->getContext();
		$session = $context->getSession();
		$locale = $context->getLocale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSite()->getCode();
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
	 */
	public function setSession( \Aimeos\MShop\Order\Item\Base\Iface $order, $type = 'default' )
	{
		$context = $this->getContext();
		$session = $context->getSession();
		$locale = $context->getLocale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSite()->getCode();
		$key = 'aimeos/basket/content-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		$session->set( $key, serialize( clone $order ) );
	}


	/**
	 * Locks or unlocks the session by setting the lock value.
	 * The lock is a cooperative lock and you have to check the lock value before you proceed.
	 *
	 * @param integer $lock Lock value (@see \Aimeos\MShop\Order\Manager\Base\Base)
	 * @param string $type Order type if a customer can have more than one order at once
	 * @throws \Aimeos\MShop\Order\Exception if the lock value is invalid
	 */
	public function setSessionLock( $lock, $type = 'default' )
	{
		$this->checkLock( $lock );

		$context = $this->getContext();
		$session = $context->getSession();
		$locale = $context->getLocale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSite()->getCode();
		$key = 'aimeos/basket/lock-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		$session->set( $key, strval( $lock ) );
	}


	/**
	 * Checks if the lock value is a valid constant.
	 *
	 * @param integer $value Lock constant
	 * @throws \Aimeos\MShop\Order\Exception If given value is invalid
	 */
	protected function checkLock( $value )
	{
		switch( $value )
		{
			case \Aimeos\MShop\Order\Manager\Base\Base::LOCK_DISABLE:
			case \Aimeos\MShop\Order\Manager\Base\Base::LOCK_ENABLE:
				break;
			default:
				throw new \Aimeos\MShop\Order\Exception( sprintf( 'Lock flag "%1$d" not within allowed range', $value ) );
		}
	}


	/**
	 * Returns a new and empty order base item (shopping basket).
	 *
	 * @param \Aimeos\MShop\Price\Item\Iface $price Default price of the basket (usually 0.00)
	 * @param \Aimeos\MShop\Locale\Item\Iface $locale Locale item containing the site, language and currency
	 * @param array $values Associative list of key/value pairs containing, e.g. the order or user ID
	 * @param array $products List of ordered products implementing \Aimeos\MShop\Order\Item\Base\Product\Iface
	 * @param array $addresses List of order addresses implementing \Aimeos\MShop\Order\Item\Base\Address\Iface
	 * @param array $services List of order services implementing \Aimeos\MShop\Order\Item\Base\Service\Iface
	 * @param array $coupons Associative list of coupon codes as keys and ordered products implementing \Aimeos\MShop\Order\Item\Base\Product\Iface as values
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base object
	 */
	protected function createItemBase( \Aimeos\MShop\Price\Item\Iface $price, \Aimeos\MShop\Locale\Item\Iface $locale,
		array $values = [], array $products = [], array $addresses = [],
		array $services = [], array $coupons = [] )
	{
		return new \Aimeos\MShop\Order\Item\Base\Standard( $price, $locale,
			$values, $products, $addresses, $services, $coupons );
	}


	/**
	 * Load the basket item for the given ID.
	 *
	 * @param integer $id Unique order base ID
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price object with total order value
	 * @param \Aimeos\MShop\Locale\Item\Iface $localeItem Locale object of the order
	 * @param array $row Array of values with all relevant order information
	 * @param integer $parts Bitmap of the basket parts that should be loaded
	 * @return \Aimeos\MShop\Order\Item\Base\Standard The loaded order item for the given ID
	 */
	protected function loadItems( $id, \Aimeos\MShop\Price\Item\Iface $price,
		\Aimeos\MShop\Locale\Item\Iface $localeItem, $row, $parts )
	{
		$products = $coupons = $addresses = $services = [];

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_PRODUCT
			|| $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_COUPON
		) {
			$products = $this->loadProducts( $id, false );
		}

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_COUPON ) {
			$coupons = $this->loadCoupons( $id, false, $products );
		}

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ADDRESS ) {
			$addresses = $this->loadAddresses( $id, false );
		}

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_SERVICE ) {
			$services = $this->loadServices( $id, false );
		}

		$basket = $this->createItemBase( $price, $localeItem, $row, $products, $addresses, $services, $coupons );

		return $basket;
	}


	/**
	 * Create a new basket item as a clone from an existing order ID.
	 *
	 * @param integer $id Unique order base ID
	 * @param \Aimeos\MShop\Price\Item\Iface $price Price object with total order value
	 * @param \Aimeos\MShop\Locale\Item\Iface $localeItem Locale object of the order
	 * @param array $row Array of values with all relevant order information
	 * @param integer $parts Bitmap of the basket parts that should be loaded
	 * @return \Aimeos\MShop\Order\Item\Base\Standard The loaded order item for the given ID
	 */
	protected function loadFresh( $id, \Aimeos\MShop\Price\Item\Iface $price,
		\Aimeos\MShop\Locale\Item\Iface $localeItem, $row, $parts )
	{
		$products = $addresses = $services = [];

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_PRODUCT ) {
			$products = $this->loadProducts( $id, true );
		}

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ADDRESS ) {
			$addresses = $this->loadAddresses( $id, true );
		}

		if( $parts & \Aimeos\MShop\Order\Manager\Base\Base::PARTS_SERVICE ) {
			$services = $this->loadServices( $id, true );
		}


		$basket = $this->createItemBase( $price, $localeItem, $row );
		$basket->setId( null );

		foreach( $products as $item ) {
			$basket->addProduct( $item );
		}

		foreach( $addresses as $item ) {
			$basket->setAddress( $item, $item->getType() );
		}

		foreach( $services as $item ) {
			$basket->setService( $item, $item->getType() );
		}

		return $basket;
	}


	/**
	 * Retrieves the ordered products from the storage.
	 *
	 * @param integer $id Order base ID
	 * @param boolean $fresh Create new items by copying the existing ones and remove their IDs
	 * @return array List of items implementing \Aimeos\MShop\Order\Item\Product\Iface
	 */
	protected function loadProducts( $id, $fresh )
	{
		$attributes = $products = $subProducts = [];
		$manager = $this->getSubManager( 'product' );
		$attrManager = $manager->getSubManager( 'attribute' );

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.product.baseid', $id ) );
		$criteria->setSortations( array( $criteria->sort( '-', 'order.base.product.position' ) ) );
		$items = $manager->searchItems( $criteria );


		$criteria = $attrManager->createSearch();
		$expr = $criteria->compare( '==', 'order.base.product.attribute.parentid', array_keys( $items ) );
		$criteria->setConditions( $expr );

		foreach( $attrManager->searchItems( $criteria ) as $id => $attribute )
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
				$item->setAttributes( $attributes[$id] );
			}

			if( $item->getOrderProductId() === null )
			{
				ksort( $subProducts ); // bring the array into the right order because it's reversed
				$item->setProducts( $subProducts );
				$products[$item->getPosition()] = $item;

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

		return array_reverse( $products, true );
	}


	/**
	 * Retrieves the addresses of the order from the storage.
	 *
	 * @param integer $id Order base ID
	 * @param boolean $fresh Create new items by copying the existing ones and remove their IDs
	 * @return array List of items implementing \Aimeos\MShop\Order\Item\Address\Iface
	 */
	protected function loadAddresses( $id, $fresh )
	{
		$items = [];
		$manager = $this->getSubManager( 'address' );

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.address.baseid', $id ) );
		$criteria->setSortations( array( $criteria->sort( '+', 'order.base.address.type' ) ) );

		foreach( $manager->searchItems( $criteria ) as $item )
		{
			if( $fresh === true )
			{
				$item->setBaseId( null );
				$item->setId( null );
			}

			$items[$item->getType()] = $item;
		}

		return $items;
	}


	/**
	 * Retrieves the coupons of the order from the storage.
	 *
	 * @param integer $id Order base ID
	 * @param boolean $fresh Create new items by copying the existing ones and remove their IDs
	 * @param array List of order products from the basket
	 * @return array Associative list of coupon codes as keys and items implementing \Aimeos\MShop\Order\Item\Product\Iface
	 */
	protected function loadCoupons( $id, $fresh, array $products )
	{
		$items = [];
		$manager = $this->getSubManager( 'coupon' );

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.coupon.baseid', $id ) );
		$criteria->setSortations( array( $criteria->sort( '+', 'order.base.coupon.code' ) ) );

		foreach( $manager->searchItems( $criteria ) as $item )
		{
			if( !isset( $items[$item->getCode()] ) ) {
				$items[$item->getCode()] = [];
			}

			if( $item->getProductId() !== null )
			{
				foreach( $products as $product )
				{
					if( $product->getId() == $item->getProductId() ) {
						$items[$item->getCode()][] = $product;
					}
				}
			}
		}

		return $items;
	}


	/**
	 * Retrieves the services of the order from the storage.
	 *
	 * @param integer $id Order base ID
	 * @param boolean $fresh Create new items by copying the existing ones and remove their IDs
	 * @return array List of items implementing \Aimeos\MShop\Order\Item\Service\Iface
	 */
	protected function loadServices( $id, $fresh )
	{
		$items = [];
		$manager = $this->getSubManager( 'service' );

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.service.baseid', $id ) );
		$criteria->setSortations( array( $criteria->sort( '+', 'order.base.service.type' ) ) );

		foreach( $manager->searchItems( $criteria ) as $item )
		{
			if( $fresh === true )
			{
				foreach( $item->getAttributes() as $attribute )
				{
						$attribute->setId( null );
						$attribute->setParentId( null );
				}

				$item->setBaseId( null );
				$item->setId( null );
			}

			$items[$item->getType()] = $item;
		}

		return $items;
	}


	/**
	 * Saves the ordered products to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing ordered products or bundles
	 */
	protected function storeProducts( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$position = 1;
		$manager = $this->getSubManager( 'product' );
		$attrManager = $manager->getSubManager( 'attribute' );

		foreach( $basket->getProducts() as $item )
		{
			$baseId = $basket->getId();
			$item->setBaseId( $baseId );

			if( ( $pos = $item->getPosition() ) === null ) {
				$item->setPosition( $position++ );
			} else {
				$position = ++$pos;
			}

			$manager->saveItem( $item );
			$productId = $item->getId();

			foreach( $item->getAttributes() as $attribute )
			{
				$attribute->setParentId( $productId );
				$attrManager->saveItem( $attribute );
			}

			// if the item is a bundle, it probably contains sub-products
			foreach( $item->getProducts() as $subProduct )
			{
				$subProduct->setBaseId( $baseId );
				$subProduct->setOrderProductId( $productId );

				if( ( $pos = $subProduct->getPosition() ) === null ) {
					$subProduct->setPosition( $position++ );
				} else {
					$position = ++$pos;
				}

				$manager->saveItem( $subProduct );
				$subProductId = $subProduct->getId();

				foreach( $subProduct->getAttributes() as $attribute )
				{
					$attribute->setParentId( $subProductId );
					$attrManager->saveItem( $attribute );
				}
			}
		}
	}


	/**
	 * Saves the addresses of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing address items
	 */
	protected function storeAddresses( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$manager = $this->getSubManager( 'address' );

		foreach( $basket->getAddresses() as $type => $item )
		{
			$item->setBaseId( $basket->getId() );
			$item->setType( $type );
			$manager->saveItem( $item );
		}
	}


	/**
	 * Saves the coupons of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing coupon items
	 */
	protected function storeCoupons( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$manager = $this->getSubManager( 'coupon' );

		$item = $manager->createItem();
		$item->setBaseId( $basket->getId() );

		foreach( $basket->getCoupons() as $code => $products )
		{
			$item->setCode( $code );

			if( empty( $products ) )
			{
				$item->setId( null );
				$manager->saveItem( $item );
				continue;
			}

			foreach( $products as $product )
			{
				$item->setId( null );
				$item->setProductId( $product->getId() );
				$manager->saveItem( $item );
			}
		}
	}


	/**
	 * Saves the services of the order to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket containing service items
	 */
	protected function storeServices( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$manager = $this->getSubManager( 'service' );
		$attrManager = $manager->getSubManager( 'attribute' );

		foreach( $basket->getServices() as $type => $item )
		{
			$item->setBaseId( $basket->getId() );
			$item->setType( $type );
			$manager->saveItem( $item );

			foreach( $item->getAttributes() as $attribute )
			{
				if( $attribute->getType() !== 'session' )
				{
					$attribute->setParentId( $item->getId() );
					$attrManager->saveItem( $attribute );
				}
			}
		}
	}
}
