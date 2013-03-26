<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 * @version $Id: Default.php 14854 2012-01-13 12:54:14Z doleiynyk $
 */


/**
 * Default implementation for order base manager.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Default extends MShop_Order_Manager_Base_Abstract
{
	private $_dbname = 'db';

	private $_searchConfig = array(
		'order.base.id'=> array(
			'code'=>'order.base.id',
			'internalcode'=>'mordba."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_order_base" AS mordba ON ( mord."baseid" = mordba."id" )' ),
			'label'=>'Order base ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.siteid'=> array(
			'code'=>'order.base.siteid',
			'internalcode'=>'mordba."siteid"',
			'label'=>'Order base site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.base.sitecode'=> array(
			'code'=>'order.base.sitecode',
			'internalcode'=>'mordba."sitecode"',
			'label'=>'Order base site code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.customerid'=> array(
			'code'=>'order.base.customerid',
			'internalcode'=>'mordba."customerid"',
			'label'=>'Order base customer ID',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.languageid'=> array(
			'code'=>'order.base.languageid',
			'internalcode'=>'mordba."langid"',
			'label'=>'Order base language code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.currencyid'=> array(
			'code'=>'order.base.currencyid',
			'internalcode'=>'mordba."currencyid"',
			'label'=>'Order base currencyid code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.price'=> array(
			'code'=>'order.base.price',
			'internalcode'=>'mordba."price"',
			'label'=>'Order base price amount',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.shipping'=> array(
			'code'=>'order.base.shipping',
			'internalcode'=>'mordba."shipping"',
			'label'=>'Order base shipping amount',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.rebate'=> array(
			'code'=>'order.base.rebate',
			'internalcode'=>'mordba."rebate"',
			'label'=>'Order base rebate amount',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.comment'=> array(
			'code'=>'order.base.comment',
			'internalcode'=>'mordba."comment"',
			'label'=>'Order base comment',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.status'=> array(
			'code'=>'order.base.status',
			'internalcode'=>'mordba."status"',
			'label'=>'Order base status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.base.mtime'=> array(
			'code'=>'order.base.mtime',
			'internalcode'=>'mordba."mtime"',
			'label'=>'Order base modification time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.ctime'=> array(
			'code'=>'order.base.ctime',
			'internalcode'=>'mordba."ctime"',
			'label'=>'Order base create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.base.editor'=> array(
			'code'=>'order.base.editor',
			'internalcode'=>'mordba."editor"',
			'label'=>'Order base editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Creates the manager that will use the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		if( $context->getConfig()->get( 'resource/db-order/adapter', null ) !== null ) {
			$this->_dbname = 'db-order';
		}
	}


	/**
	 * Returns a new and empty order base item (shopping basket).
	 *
	 * @return MShop_Order_Item_Base_Interface Order base object
	 */
	public function createItem()
	{
		$context = $this->_getContext();
		$priceManager = MShop_Price_Manager_Factory::createManager( $context );
		$values = array('siteid'=> $context->getLocale()->getSiteId());

		$base = $this->_createItem($priceManager->createItem(), clone $context->getLocale(), $values);

		$pluginManager = MShop_Plugin_Manager_Factory::createManager( $context );
		$pluginManager->register( $base, 'order' );

		return $base;
	}


	/**
	 * Deletes an order including its subelements (addresses, delivery, payment, products, coupons) completely.
	 *
	 * @param integer $id Id of the order base
	 */
	public function deleteItem( $id )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$stmt = $this->_getCachedStatement($conn, 'mshop/order/manager/base/default/item/delete');
			$stmt->bind(1, $id, MW_DB_Statement_Abstract::PARAM_INT);
			$result = $stmt->execute()->finish();

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Returns the order base item specified by the given ID.
	 *
	 * @param integer $id Unique id of the order base
	 * @return MShop_Order_Item_Base_Interface Order base object including all subelements
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'order.base.id', $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			$config = $this->_getContext()->getConfig();
			$default = array( 'address', 'product', 'service' );

			foreach( $config->get( 'classes/order/manager/base/submanagers', $default ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for order base extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g address, coupon, product, service, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'order', 'base/' . $manager, $name );
	}


	/**
	 * Adds or updates an order base item in the storage.
	 *
	 * @param MShop_Order_Item_Base_Interface $base Order base object without sub-elements
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Base_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$id = $item->getId();

			$localeItem = $context->getLocale();
			$priceItem = $item->getPrice();

			$path = 'mshop/order/manager/base/default/item/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind(1, $localeItem->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT);
			$stmt->bind(2, $item->getCustomerId());
			$stmt->bind(3, $localeItem->getSite()->getCode());
			$stmt->bind(4, $item->getLocale()->getLanguageId());
			$stmt->bind(5, $priceItem->getCurrencyId());
			$stmt->bind(6, $priceItem->getValue());
			$stmt->bind(7, $priceItem->getShipping());
			$stmt->bind(8, $priceItem->getRebate());
			$stmt->bind(9, $item->getComment() );
			$stmt->bind(10, $item->getStatus() );
			$stmt->bind(11, date( 'Y-m-d H:i:s', time()));
			$stmt->bind(12, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind(13, $id, MW_DB_Statement_Abstract::PARAM_INT);
			} else {
				$stmt->bind(13, date( 'Y-m-d H:i:s', time() ), MW_DB_Statement_Abstract::PARAM_STR );// ctime
			}

			$result = $stmt->execute()->finish();

			if( $fetch === true )
			{
				if( $id === null ) {
					$path = 'mshop/order/manager/base/default/item/newid';
					$item->setId( $this->_newId( $conn, $config->get($path, $path) ) );
				} else {
					$item->setId( $id );
				}
			}

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Search for orders based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref Not used
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Order_Item_Base_Interface
	 * @throws MShop_Order_Exception If creating items fails
	 * @throws MW_DB_Exception If a database operation fails
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = array();

		$context = $this->_getContext();
		$priceManager = MShop_Price_Manager_Factory::createManager( $context );
		$localeManager = MShop_Locale_Manager_Factory::createManager( $context );

		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/base/default/item/search';
			$cfgPathCount =  'mshop/order/manager/base/default/item/count';
			$required = array( 'order.base' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			while( ( $row = $results->fetch() ) !== false )
			{
				$price = $priceManager->createItem();
				$price->setCurrencyId( $row['currencyid'] );
				$price->setValue( $row['price'] );
				$price->setShipping( $row['shipping'] );
				$price->setRebate( $row['rebate'] );

				// you may need the site object! take care!
				$localeItem = $localeManager->createItem();
				$localeItem->setLanguageId($row['langid']);
				$localeItem->setCurrencyId($row['currencyid']);
				$localeItem->setSiteId($row['siteid']);

				$items[ $row['id'] ] = $this->_createItem( $price, $localeItem, $row );
			}

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns the current basket of the customer.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return MShop_Order_Item_Base_Interface Shopping basket
	 */
	public function getSession( $type = '' )
	{
		$context = $this->_getContext();
		$session = $context->getSession();
		$sitecode = $context->getLocale()->getSite()->getCode();

		$name = 'basket_' . strval( $type ) . '_' . $sitecode;

		if( ( $serorder = $session->get( $name ) ) === null ) {
			return $this->createItem();
		}

		$iface = 'MShop_Order_Item_Base_Interface';

		if( ( $order = unserialize( $serorder ) ) === false || !( $order instanceof $iface ) )
		{
			$msg = sprintf( 'An error occured in the order. Invalid serialized basket. "%1$s" returns "%2$s".', __METHOD__, $serorder );
			$context->getLogger()->log( $msg, MW_Logger_Abstract::WARN );

			return $this->createItem();
		}

		MShop_Plugin_Manager_Factory::createManager( $context )->register( $order, 'order' );

		return $order;
	}


	/**
	 * Returns the current lock status of the basket.
	 *
	 * @param string $type Basket type if a customer can have more than one basket
	 * @return integer Lock status (@see MShop_Order_Manager_Base_Abstract)
	 */
	public function getSessionLock( $type = '' )
	{
		$context = $this->_getContext();
		$session = $context->getSession();
		$sitecode = $context->getLocale()->getSite()->getCode();

		if( ( $value = $session->get( 'basket-lock_' . strval( $type ) . '_' . $sitecode ) ) !== null ) {
			return (int) $value;
		}

		return MShop_Order_Manager_Base_Abstract::LOCK_DISABLE;
	}


	/**
	 * Saves the current shopping basket of the customer.
	 *
	 * @param MShop_Order_Item_Base_Interface $order Shopping basket
	 * @param string $type Order type if a customer can have more than one order at once
	 */
	public function setSession( MShop_Order_Item_Base_Interface $order, $type = '' )
	{
		$context = $this->_getContext();
		$session = $context->getSession();
		$sitecode = $context->getLocale()->getSite()->getCode();

		$session->set( 'basket_' . strval( $type ) . '_' . $sitecode, serialize( clone $order ) );
	}


	/**
	 * Locks or unlocks the session by setting the lock value.
	 * The lock is a cooperative lock and you have to check the lock value before you proceed.
	 *
	 * @param integer $lock Lock value (@see MShop_Order_Manager_Base_Abstract)
	 * @param string $type Order type if a customer can have more than one order at once
	 * @throws MShop_Order_Exception if the lock value is invalid
	 */
	public function setSessionLock( $lock, $type = '' )
	{
		$this->_checkLock( $lock );

		$context = $this->_getContext();
		$session = $context->getSession();
		$sitecode = $context->getLocale()->getSite()->getCode();

		$session->set( 'basket-lock_' . strval( $type ) . '_' . $sitecode, strval( $lock ) );
	}


	/**
	 * Creates a new basket containing all items from the order excluding the coupons.
	 * The items will be marked as new and modified so an additional order is
	 * stored when the basket is saved.
	 *
	 * @param integer $id Base ID of the order to load
	 * @param boolean $fresh Create a new basket by copying the existing one and remove IDs
	 * @return MShop_Order_Item_Base_Interface Basket including all items
	 */
	public function load( $id, $fresh = false )
	{
		$search = $this->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.id', $id ) );

		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$cfgPathSearch = 'mshop/order/manager/base/default/item/search';
			$cfgPathCount =  'mshop/order/manager/base/default/item/count';
			$required = array( 'order.base' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required );

			if( ( $row = $results->fetch() ) === false ) {
				throw new MShop_Order_Exception( sprintf( 'An error occured in a search. Order base item with order ID "%1$s" not found.', $id ) );
			}
			$results->finish();

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}

		$priceManager = MShop_Price_Manager_Factory::createManager( $context );
		$localeManager = MShop_Locale_Manager_Factory::createManager( $context );

		$price = $priceManager->createItem();
		$price->setCurrencyId( $row['currencyid'] );
		$price->setValue( $row['price'] );
		$price->setShipping( $row['shipping'] );
		$price->setRebate( $row['rebate'] );

		// you may need the site object! take care!
		$localeItem = $localeManager->createItem();
		$localeItem->setLanguageId($row['langid']);
		$localeItem->setCurrencyId($row['currencyid']);
		$localeItem->setSiteId($row['siteid']);

		if( $fresh === false ) {
			$basket = $this->_load( $id, $price, $localeItem, $row );
		} else {
			$basket = $this->_loadFresh( $id, $price, $localeItem, $row );
		}

		return $basket;
	}


	/**
	 * Saves the complete basket to the storage including all items attached.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object containing all information
	 */
	public function store( MShop_Order_Item_Base_Interface $basket )
	{
		$this->_begin();

		$this->saveItem( $basket );

		$this->_storeProducts( $basket );
		$this->_storeAddresses( $basket );
		$this->_storeServices( $basket );

		$this->_commit();
	}


	/**
	 * Returns a new and empty order base item (shopping basket).
	 *
	 * @return MShop_Order_Item_Base_Interface Order base object
	 */
	protected function _createItem( MShop_Price_Item_Interface $price, MShop_Locale_Item_Interface $locale,
		array $values = array(), array $products = array(), array $addresses = array(),
		array $services = array(), array $coupons = array() )
	{
		return new MShop_Order_Item_Base_Default( $price, $locale,
			$values, $products, $addresses, $services, $coupons );
	}


	/**
	 * Retrieves the ordered products from the storage.
	 *
	 * @param integer $id Order base ID
	 * @param boolean $fresh Create new items by copying the existing ones and remove their IDs
	 * @return array List of items implementing MShop_Order_Item_Product_Interface
	 */
	protected function _loadProducts( $id, $fresh )
	{
		$attributes = $products = $subProducts = array();
		$manager = $this->getSubManager( 'product' );
		$attrManager = $manager->getSubManager( 'attribute' );

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.product.baseid', $id ) );
		$criteria->setSortations( array( $criteria->sort( '-', 'order.base.product.position' ) ) );
		$items = $manager->searchItems( $criteria );


		$criteria = $attrManager->createSearch();
		$expr = $criteria->compare( '==', 'order.base.product.attribute.productid', array_keys( $items ) );
		$criteria->setConditions( $expr );

		foreach( $attrManager->searchItems( $criteria ) as $id => $attribute )
		{
			if(  $fresh == true )
			{
				$attributes[ $attribute->getProductId() ][] = $attribute;
				$attribute->setProductId( null );
				$attribute->setId( null );
			}
			else
			{
				$attributes[ $attribute->getProductId() ][$id] = $attribute;
			}
		}

		foreach( $items as $id => $item )
		{
			if( isset( $attributes[$id] ) ) {
				$item->setAttributes( $attributes[$id] );
			}

			if( $item->getOrderProductId() === null )
			{
				$item->setProducts( $subProducts );
				$products[ $item->getPosition() ] = $item;

				$subProducts = array();
			}
			else
			{	// in case it's a sub-product
				$subProducts[ $item->getPosition() ] = $item ;
			}

			if( $fresh == true )
			{
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
	 * @return array List of items implementing MShop_Order_Item_Address_Interface
	 */
	protected function _loadAddresses( $id, $fresh )
	{
		$items = array();
		$manager = $this->getSubManager( 'address' );

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.address.baseid', $id ) );

		foreach( $manager->searchItems( $criteria ) as $item )
		{
			if( $fresh === true )
			{
				$item->setBaseId( null );
				$item->setId( null );
			}

			$items[ $item->getType() ] = $item;
		}

		return $items;
	}


	/**
	 * Retrieves the services of the order from the storage.
	 *
	 * @param integer $id Order base ID
	 * @param boolean $fresh Create new items by copying the existing ones and remove their IDs
	 * @return array List of items implementing MShop_Order_Item_Service_Interface
	 */
	protected function _loadServices( $id, $fresh )
	{
		$items = array();
		$manager = $this->getSubManager( 'service' );

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.service.baseid', $id ) );

		foreach( $manager->searchItems( $criteria ) as $item )
		{
			if( $fresh === true )
			{
				foreach( $item->getAttributes() as $attribute )
				{
						$attribute->setId( null );
						$attribute->setServiceId( null );
				}

				$item->setBaseId( null );
				$item->setId( null );
			}

			$items[ $item->getType() ] = $item;
		}

		return $items;
	}


	/**
	 * Saves the ordered products to the storage.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket containing ordered products or bundles
	 */
	protected function _storeProducts( MShop_Order_Item_Base_Interface $basket )
	{
		$manager = $this->getSubManager( 'product' );
		$attrManager = $manager->getSubManager( 'attribute' );

		foreach( $basket->getProducts() as $item )
		{
			$baseId = $basket->getId();
			$item->setBaseId( $baseId );
			$manager->saveItem( $item );
			$productId = $item->getId();

			foreach( $item->getAttributes() as $attribute )
			{
				$attribute->setProductId( $productId );
				$attrManager->saveItem( $attribute );
			}

			// if the item is a bundle, it probably contains sub-products
			foreach ( $item->getProducts() as $subProduct )
			{

				$subProduct->setOrderProductId( $productId );
				$subProduct->setBaseId( $baseId );
				$manager->saveItem( $subProduct );
				$subProductId = $subProduct->getId();

				foreach ( $subProduct->getAttributes() as $attribute )
				{
					$attribute->setProductId( $subProductId );
					$attrManager->saveItem( $attribute );
				}
			}
		}
	}

	/**
	 * Saves the addresses of the order to the storage.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket containing address items
	 */
	protected function _storeAddresses( MShop_Order_Item_Base_Interface $basket )
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
	 * Saves the services of the order to the storage.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket containing service items
	 */
	protected function _storeServices( MShop_Order_Item_Base_Interface $basket )
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
				$attribute->setServiceId( $item->getId() );
				$attrManager->saveItem( $attribute );
			}
		}
	}


	/**
	 * Load the basket item for the given ID.
	 *
	 * @param integer $id Order base ID
	 * @param MShop_Price_Item $price
	 * @param MShop_Locale_Item $localeItem
	 * @param array $row Array of values with all relevant order information
	 * @return MShop_Order_Base_Item The loaded order item for the given ID
	 */
	protected function _load( $id, $price, $localeItem, $row )
	{
		$products = $this->_loadProducts( $id, false );
		$addresses = $this->_loadAddresses( $id, false );
		$services = $this->_loadServices( $id, false );

		$basket =  $this->_createItem( $price, $localeItem, $row, $products, $addresses, $services );

		return $basket;
	}


	/**
	 * Create a new basket item as a clone from an existing order ID.
	 *
	 * @param integer $id Order base ID
	 * @param MShop_Price_Item $price
	 * @param MShop_Locale_Item $localeItem
	 * @param array $row Array of values with all relevant order information
	 * @return MShop_Order_Base_Item The loaded order item for the given ID
	 */
	protected function _loadFresh( $id, $price, $localeItem, $row )
	{
		$products = $this->_loadProducts( $id, true );
		$addresses = $this->_loadAddresses( $id, true );
		$services = $this->_loadServices( $id, true );

		$basket =  $this->_createItem( $price, $localeItem, $row );
		$basket->setId( null );

		$pluginManager = MShop_Plugin_Manager_Factory::createManager( $this->_getContext() );
		$pluginManager->register( $basket, 'order' );

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
}
