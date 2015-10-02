<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Order
 */


/**
 * Default implementation for order base manager.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Base_Default extends MShop_Order_Manager_Base_Abstract
{
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
		'order.base.costs'=> array(
			'code'=>'order.base.costs',
			'internalcode'=>'mordba."costs"',
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
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-order' );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param MW_Common_Criteria_Interface $search Search criteria
	 * @param string $key Search key to aggregate items for
	 * @return array List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( MW_Common_Criteria_Interface $search, $key )
	{
		/** mshop/order/manager/base/default/aggregate
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * Groups all records by the values in the key column and counts their
		 * occurence. The matched records can be limited by the given criteria
		 * from the order database. The records must be from one of the sites
		 * that are configured via the context item. If the current site is part
		 * of a tree of sites, the statement can count all records from the
		 * current site and the complete sub-tree of sites.
		 *
		 * As the records can normally be limited by criteria from sub-managers,
		 * their tables must be joined in the SQL context. This is done by
		 * using the "internaldeps" property from the definition of the ID
		 * column of the sub-managers. These internal dependencies specify
		 * the JOIN between the tables and the used columns for joining. The
		 * ":joins" placeholder is then replaced by the JOIN strings from
		 * the sub-managers.
		 *
		 * To limit the records matched, conditions can be added to the given
		 * criteria object. It can contain comparisons like column names that
		 * must match specific values which can be combined by AND, OR or NOT
		 * operators. The resulting string of SQL conditions replaces the
		 * ":cond" placeholder before the statement is sent to the database
		 * server.
		 *
		 * This statement doesn't return any records. Instead, it returns pairs
		 * of the different values found in the key column together with the
		 * number of records that have been found for that key values.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for aggregating order items
		 * @since 2014.09
		 * @category Developer
		 * @see mshop/order/manager/base/default/item/insert
		 * @see mshop/order/manager/base/default/item/update
		 * @see mshop/order/manager/base/default/item/newid
		 * @see mshop/order/manager/base/default/item/delete
		 * @see mshop/order/manager/base/default/item/search
		 * @see mshop/order/manager/base/default/item/count
		 */
		$cfgkey = 'mshop/order/manager/base/default/aggregate';
		return $this->_aggregate( $search, $key, $cfgkey, array( 'order.base' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/order/manager/base/submanagers';
		$default = array( 'address', 'coupon', 'product', 'service' );

		foreach( $this->_getContext()->getConfig()->get( $path, $default ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/order/manager/base/default/item/delete' );
	}


	/**
	 * Returns a new and empty order base item (shopping basket).
	 *
	 * @return MShop_Order_Item_Base_Interface Order base object
	 */
	public function createItem()
	{
		$context = $this->_getContext();
		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$values = array( 'siteid'=> $context->getLocale()->getSiteId() );

		$base = $this->_createItem( $priceManager->createItem(), clone $context->getLocale(), $values );

		$pluginManager = MShop_Factory::createManager( $context, 'plugin' );
		$pluginManager->register( $base, 'order' );

		return $base;
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/order/manager/base/default/item/delete
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the order database.
		 * The records must be from the site that is configured via the
		 * context item.
		 *
		 * The ":cond" placeholder is replaced by the name of the ID column and
		 * the given ID or list of IDs while the site ID is bound to the question
		 * mark.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting items
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/order/manager/base/default/item/insert
		 * @see mshop/order/manager/base/default/item/update
		 * @see mshop/order/manager/base/default/item/newid
		 * @see mshop/order/manager/base/default/item/search
		 * @see mshop/order/manager/base/default/item/count
		 */
		$path = 'mshop/order/manager/base/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the order base item specified by the given ID.
	 *
	 * @param integer $id Unique id of the order base
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Order_Item_Base_Interface Returns Order base item of the given id
	 * @throws MShop_Exception If item couldn't be found
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
		/** classes/order/manager/base/submanagers
		 * List of manager names that can be instantiated by the order base manager
		 *
		 * Managers provide a generic interface to the underlying storage.
		 * Each manager has or can have sub-managers caring about particular
		 * aspects. Each of these sub-managers can be instantiated by its
		 * parent manager using the getSubManager() method.
		 *
		 * The search keys from sub-managers can be normally used in the
		 * manager as well. It allows you to search for items of the manager
		 * using the search keys of the sub-managers to further limit the
		 * retrieved list of items.
		 *
		 * @param array List of sub-manager names
		 * @since 2014.03
		 * @category Developer
		 */
		$path = 'classes/order/manager/base/submanagers';
		$default = array( 'address', 'coupon', 'product', 'service' );

		return $this->_getSearchAttributes( $this->_searchConfig, $path, $default, $withsub );
	}


	/**
	 * Returns a new manager for order base extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Interface Manager for different extensions, e.g address, coupon, product, service, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/order/manager/base/name
		 * Class name of the used order base manager implementation
		 *
		 * Each default order base manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Order_Manager_Base_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Order_Manager_Base_Mybase
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/order/manager/base/name = Mybase
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyBase"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/base/decorators/excludes
		 * Excludes decorators added by the "common" option from the order base manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order base manager.
		 *
		 *  mshop/order/manager/base/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the order base manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/decorators/global
		 * @see mshop/order/manager/base/decorators/local
		 */

		/** mshop/order/manager/base/decorators/global
		 * Adds a list of globally available decorators only to the order base manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base manager.
		 *
		 *  mshop/order/manager/base/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/decorators/excludes
		 * @see mshop/order/manager/base/decorators/local
		 */

		/** mshop/order/manager/base/decorators/local
		 * Adds a list of local decorators only to the order base manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order base manager.
		 *
		 *  mshop/order/manager/base/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/base/decorators/excludes
		 * @see mshop/order/manager/base/decorators/global
		 */

		return $this->getSubManagerBase( 'order', 'base/' . $manager, $name );
	}


	/**
	 * Adds or updates an order base item in the storage.
	 *
	 * @param MShop_Common_Item_Interface $item Order base object (sub-items are not saved)
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

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );

			if( $id === null )
			{
				/** mshop/order/manager/base/default/item/insert
				 * Inserts a new order record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the order item to the statement before they are
				 * sent to the database server. The number of question marks must
				 * be the same as the number of columns listed in the INSERT
				 * statement. The order of the columns must correspond to the
				 * order in the saveItems() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/order/manager/base/default/item/update
				 * @see mshop/order/manager/base/default/item/newid
				 * @see mshop/order/manager/base/default/item/delete
				 * @see mshop/order/manager/base/default/item/search
				 * @see mshop/order/manager/base/default/item/count
				 */
				$path = 'mshop/order/manager/base/default/item/insert';
			}
			else
			{
				/** mshop/order/manager/base/default/item/update
				 * Updates an existing order record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the order item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the saveItems() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/order/manager/base/default/item/insert
				 * @see mshop/order/manager/base/default/item/newid
				 * @see mshop/order/manager/base/default/item/delete
				 * @see mshop/order/manager/base/default/item/search
				 * @see mshop/order/manager/base/default/item/count
				 */
				$path = 'mshop/order/manager/base/default/item/update';
			}

			$priceItem = $item->getPrice();
			$localeItem = $context->getLocale();

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind( 1, $localeItem->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getCustomerId() );
			$stmt->bind( 3, $localeItem->getSite()->getCode() );
			$stmt->bind( 4, $item->getLocale()->getLanguageId() );
			$stmt->bind( 5, $priceItem->getCurrencyId() );
			$stmt->bind( 6, $priceItem->getValue() );
			$stmt->bind( 7, $priceItem->getCosts() );
			$stmt->bind( 8, $priceItem->getRebate() );
			$stmt->bind( 9, $item->getComment() );
			$stmt->bind( 10, $item->getStatus() );
			$stmt->bind( 11, $date ); // mtime
			$stmt->bind( 12, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 13, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 13, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/order/manager/base/default/item/newid
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * As soon as a new record is inserted into the database table,
				 * the database server generates a new and unique identifier for
				 * that record. This ID can be used for retrieving, updating and
				 * deleting that specific record from the table again.
				 *
				 * For MySQL:
				 *  SELECT LAST_INSERT_ID()
				 * For PostgreSQL:
				 *  SELECT currval('seq_mord_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mord_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/order/manager/base/default/item/insert
				 * @see mshop/order/manager/base/default/item/update
				 * @see mshop/order/manager/base/default/item/delete
				 * @see mshop/order/manager/base/default/item/search
				 * @see mshop/order/manager/base/default/item/count
				 */
				$path = 'mshop/order/manager/base/default/item/newid';
				$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
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
		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$localeManager = MShop_Factory::createManager( $context, 'locale' );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'order.base' );
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;

			/** mshop/order/manager/base/default/item/search
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the order
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the SELECT statement can retrieve all records
			 * from the current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL context. This is done by
			 * using the "internaldeps" property from the definition of the ID
			 * column of the sub-managers. These internal dependencies specify
			 * the JOIN between the tables and the used columns for joining. The
			 * ":joins" placeholder is then replaced by the JOIN strings from
			 * the sub-managers.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
			 *
			 * If the records that are retrieved should be ordered by one or more
			 * columns, the generated string of column / sort direction pairs
			 * replaces the ":order" placeholder. In case no ordering is required,
			 * the complete ORDER BY part including the "\/*-orderby*\/...\/*orderby-*\/"
			 * markers is removed to speed up retrieving the records. Columns of
			 * sub-managers can also be used for ordering the result set but then
			 * no index can be used.
			 *
			 * The number of returned records can be limited and can start at any
			 * number between the begining and the end of the result set. For that
			 * the ":size" and ":start" placeholders are replaced by the
			 * corresponding values from the criteria object. The default values
			 * are 0 for the start and 100 for the size value.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for searching items
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/order/manager/base/default/item/insert
			 * @see mshop/order/manager/base/default/item/update
			 * @see mshop/order/manager/base/default/item/newid
			 * @see mshop/order/manager/base/default/item/delete
			 * @see mshop/order/manager/base/default/item/count
			 */
			$cfgPathSearch = 'mshop/order/manager/base/default/item/search';

			/** mshop/order/manager/base/default/item/count
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the order
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the statement can count all records from the
			 * current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL context. This is done by
			 * using the "internaldeps" property from the definition of the ID
			 * column of the sub-managers. These internal dependencies specify
			 * the JOIN between the tables and the used columns for joining. The
			 * ":joins" placeholder is then replaced by the JOIN strings from
			 * the sub-managers.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
			 *
			 * Both, the strings for ":joins" and for ":cond" are the same as for
			 * the "search" SQL statement.
			 *
			 * Contrary to the "search" statement, it doesn't return any records
			 * but instead the number of records that have been found. As counting
			 * thousands of records can be a long running task, the maximum number
			 * of counted records is limited for performance reasons.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for counting items
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/order/manager/base/default/item/insert
			 * @see mshop/order/manager/base/default/item/update
			 * @see mshop/order/manager/base/default/item/newid
			 * @see mshop/order/manager/base/default/item/delete
			 * @see mshop/order/manager/base/default/item/search
			 */
			$cfgPathCount = 'mshop/order/manager/base/default/item/count';

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			while( ( $row = $results->fetch() ) !== false )
			{
				$price = $priceManager->createItem();
				$price->setCurrencyId( $row['currencyid'] );
				$price->setValue( $row['price'] );
				$price->setCosts( $row['costs'] );
				$price->setRebate( $row['rebate'] );

				// you may need the site object! take care!
				$localeItem = $localeManager->createItem();
				$localeItem->setLanguageId( $row['langid'] );
				$localeItem->setCurrencyId( $row['currencyid'] );
				$localeItem->setSiteId( $row['siteid'] );

				$items[$row['id']] = $this->_createItem( $price, $localeItem, $row );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
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
		$locale = $context->getLocale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSite()->getCode();
		$key = 'aimeos/basket/content-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		if( ( $serorder = $session->get( $key ) ) === null ) {
			return $this->createItem();
		}

		$iface = 'MShop_Order_Item_Base_Interface';

		if( ( $order = unserialize( $serorder ) ) === false || !( $order instanceof $iface ) )
		{
			$msg = sprintf( 'Invalid serialized basket. "%1$s" returns "%2$s".', __METHOD__, $serorder );
			$context->getLogger()->log( $msg, MW_Logger_Abstract::WARN );

			return $this->createItem();
		}

		MShop_Factory::createManager( $context, 'plugin' )->register( $order, 'order' );

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
		$locale = $context->getLocale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSite()->getCode();
		$key = 'aimeos/basket/lock-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		if( ( $value = $session->get( $key ) ) !== null ) {
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
	 * @param integer $lock Lock value (@see MShop_Order_Manager_Base_Abstract)
	 * @param string $type Order type if a customer can have more than one order at once
	 * @throws MShop_Order_Exception if the lock value is invalid
	 */
	public function setSessionLock( $lock, $type = '' )
	{
		$this->_checkLock( $lock );

		$context = $this->_getContext();
		$session = $context->getSession();
		$locale = $context->getLocale();
		$currency = $locale->getCurrencyId();
		$language = $locale->getLanguageId();
		$sitecode = $locale->getSite()->getCode();
		$key = 'aimeos/basket/lock-' . $sitecode . '-' . $language . '-' . $currency . '-' . strval( $type );

		$session->set( $key, strval( $lock ) );
	}


	/**
	 * Creates a new basket containing the items from the order excluding the coupons.
	 * If the last parameter is ture, the items will be marked as new and
	 * modified so an additional order is stored when the basket is saved.
	 *
	 * @param integer $id Base ID of the order to load
	 * @param integer $parts Bitmap of the basket parts that should be loaded
	 * @param boolean $fresh Create a new basket by copying the existing one and remove IDs
	 * @return MShop_Order_Item_Base_Interface Basket including all items
	 */
	public function load( $id, $parts = MShop_Order_Manager_Base_Abstract::PARTS_ALL, $fresh = false )
	{
		$search = $this->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.id', $id ) );

		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'mshop/order/manager/base/default/item/search';
			$cfgPathCount = 'mshop/order/manager/base/default/item/count';
			$required = array( 'order.base' );
			$total = null;

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $sitelevel );

			if( ( $row = $results->fetch() ) === false ) {
				throw new MShop_Order_Exception( sprintf( 'Order base item with order ID "%1$s" not found', $id ) );
			}
			$results->finish();

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$localeManager = MShop_Factory::createManager( $context, 'locale' );

		$price = $priceManager->createItem();
		$price->setCurrencyId( $row['currencyid'] );
		$price->setValue( $row['price'] );
		$price->setCosts( $row['costs'] );
		$price->setRebate( $row['rebate'] );

		// you may need the site object! take care!
		$localeItem = $localeManager->createItem();
		$localeItem->setLanguageId( $row['langid'] );
		$localeItem->setCurrencyId( $row['currencyid'] );
		$localeItem->setSiteId( $row['siteid'] );

		if( $fresh === false ) {
			$basket = $this->_load( $id, $price, $localeItem, $row, $parts );
		} else {
			$basket = $this->_loadFresh( $id, $price, $localeItem, $row, $parts );
		}

		return $basket;
	}


	/**
	 * Saves the complete basket to the storage including the items attached.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket object containing all information
	 * @param integer $parts Bitmap of the basket parts that should be stored
	 */
	public function store( MShop_Order_Item_Base_Interface $basket, $parts = MShop_Order_Manager_Base_Abstract::PARTS_ALL )
	{
		$this->saveItem( $basket );

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_PRODUCT
			|| $parts & MShop_Order_Manager_Base_Abstract::PARTS_COUPON
		) {
			$this->_storeProducts( $basket );
		}

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_COUPON ) {
			$this->_storeCoupons( $basket );
		}

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_ADDRESS ) {
			$this->_storeAddresses( $basket );
		}

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_SERVICE ) {
			$this->_storeServices( $basket );
		}
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
			if( $fresh == true )
			{
				$attributes[$attribute->getProductId()][] = $attribute;
				$attribute->setProductId( null );
				$attribute->setId( null );
			}
			else
			{
				$attributes[$attribute->getProductId()][$id] = $attribute;
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

				$subProducts = array();
			}
			else
			{	// in case it's a sub-product
				$subProducts[$item->getPosition()] = $item;
			}

			if( $fresh == true )
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
	 * @return array List of items implementing MShop_Order_Item_Address_Interface
	 */
	protected function _loadAddresses( $id, $fresh )
	{
		$items = array();
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
	 * @return array Associative list of coupon codes as keys and items implementing MShop_Order_Item_Product_Interface
	 */
	protected function _loadCoupons( $id, $fresh, array $products )
	{
		$items = array();
		$manager = $this->getSubManager( 'coupon' );

		$criteria = $manager->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'order.base.coupon.baseid', $id ) );
		$criteria->setSortations( array( $criteria->sort( '+', 'order.base.coupon.code' ) ) );

		foreach( $manager->searchItems( $criteria ) as $item )
		{
			if( !isset( $items[$item->getCode()] ) ) {
				$items[$item->getCode()] = array();
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
	 * @return array List of items implementing MShop_Order_Item_Service_Interface
	 */
	protected function _loadServices( $id, $fresh )
	{
		$items = array();
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
						$attribute->setServiceId( null );
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
	 * @param MShop_Order_Item_Base_Interface $basket Basket containing ordered products or bundles
	 */
	protected function _storeProducts( MShop_Order_Item_Base_Interface $basket )
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
				$attribute->setProductId( $productId );
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
	 * Saves the coupons of the order to the storage.
	 *
	 * @param MShop_Order_Item_Base_Interface $basket Basket containing coupon items
	 */
	protected function _storeCoupons( MShop_Order_Item_Base_Interface $basket )
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
	 * @param integer $id Unique order base ID
	 * @param MShop_Price_Item $price Price object with total order value
	 * @param MShop_Locale_Item $localeItem Locale object of the order
	 * @param array $row Array of values with all relevant order information
	 * @param integer $parts Bitmap of the basket parts that should be loaded
	 * @return MShop_Order_Item_Base_Default The loaded order item for the given ID
	 */
	protected function _load( $id, $price, $localeItem, $row, $parts )
	{
		$products = $coupons = $addresses = $services = array();

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_PRODUCT
			|| $parts & MShop_Order_Manager_Base_Abstract::PARTS_COUPON
		) {
			$products = $this->_loadProducts( $id, false );
		}

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_COUPON ) {
			$coupons = $this->_loadCoupons( $id, false, $products );
		}

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_ADDRESS ) {
			$addresses = $this->_loadAddresses( $id, false );
		}

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_SERVICE ) {
			$services = $this->_loadServices( $id, false );
		}

		$basket = $this->_createItem( $price, $localeItem, $row, $products, $addresses, $services, $coupons );

		return $basket;
	}


	/**
	 * Create a new basket item as a clone from an existing order ID.
	 *
	 * @param integer $id Unique order base ID
	 * @param MShop_Price_Item $price Price object with total order value
	 * @param MShop_Locale_Item $localeItem Locale object of the order
	 * @param array $row Array of values with all relevant order information
	 * @param integer $parts Bitmap of the basket parts that should be loaded
	 * @return MShop_Order_Item_Base_Default The loaded order item for the given ID
	 */
	protected function _loadFresh( $id, $price, $localeItem, $row, $parts )
	{
		$products = $addresses = $services = array();

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_PRODUCT ) {
			$products = $this->_loadProducts( $id, true );
		}

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_ADDRESS ) {
			$addresses = $this->_loadAddresses( $id, true );
		}

		if( $parts & MShop_Order_Manager_Base_Abstract::PARTS_SERVICE ) {
			$services = $this->_loadServices( $id, true );
		}


		$basket = $this->_createItem( $price, $localeItem, $row );
		$basket->setId( null );

		$pluginManager = MShop_Factory::createManager( $this->_getContext(), 'plugin' );
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
