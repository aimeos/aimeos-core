<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Order
 */


/**
 * Default order manager implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class MShop_Order_Manager_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Order_Manager_Interface
{
	private $_searchConfig = array(
		'order.id'=> array(
			'code'=>'order.id',
			'internalcode'=>'mord."id"',
			'label'=>'Order invoice ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.siteid'=> array(
			'code'=>'order.siteid',
			'internalcode'=>'mord."siteid"',
			'label'=>'Order invoice site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.baseid'=> array(
			'code'=>'order.baseid',
			'internalcode'=>'mord."baseid"',
			'label'=>'Order base ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'order.type'=> array(
			'code'=>'order.type',
			'internalcode'=>'mord."type"',
			'label'=>'Order type',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.datepayment'=> array(
			'code'=>'order.datepayment',
			'internalcode'=>'mord."datepayment"',
			'label'=>'Order purchase date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.datedelivery'=> array(
			'code'=>'order.datedelivery',
			'internalcode'=>'mord."datedelivery"',
			'label'=>'Order delivery date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.statusdelivery'=> array(
			'code'=>'order.statusdelivery',
			'internalcode'=>'mord."statusdelivery"',
			'label'=>'Order delivery status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.statuspayment'=> array(
			'code'=>'order.statuspayment',
			'internalcode'=>'mord."statuspayment"',
			'label'=>'Order payment status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.relatedid'=> array(
			'code'=>'order.relatedid',
			'internalcode'=>'mord."relatedid"',
			'label'=>'Order related order ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'order.mtime'=> array(
			'code'=>'order.mtime',
			'internalcode'=>'mord."mtime"',
			'label'=>'Order modification date',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.ctime'=> array(
			'code'=>'order.ctime',
			'internalcode'=>'mord."ctime"',
			'label'=>'Order creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.editor'=> array(
			'code'=>'order.editor',
			'internalcode'=>'mord."editor"',
			'label'=>'Order editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'order.containsStatus' => array(
			'code'=>'order.containsStatus()',
			'internalcode'=>'( SELECT COUNT(mordst_cs."parentid")
				FROM "mshop_order_status" AS mordst_cs
				WHERE mord."id" = mordst_cs."parentid" AND :site
				AND mordst_cs."type" = $1 AND mordst_cs."value" IN ( $2 ) )',
			'label'=>'Number of order status items, parameter(<type>,<value>)',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
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
		$this->_setResourceName( 'db-order' );


		$sites = $context->getLocale()->getSiteSubTree();
		$this->_replaceSiteMarker( $this->_searchConfig['order.containsStatus'], 'mordst_cs."siteid"', $sites, ':site' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/order/manager/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'status', 'base' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/order/manager/default/item/delete' );
	}


	/**
	 * Returns a new and empty invoice.
	 *
	 * @return MShop_Order_Item_Interface Invoice without assigned values or items
	 */
	public function createItem()
	{
		$values = array('siteid'=> $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Creates a search object.
	 *
	 * @param boolean $default Add default criteria; Optional
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		$search = parent::createSearch( $default );

		if( $default === true )
		{
			$expr = array(
				$search->getConditions(),
				$search->compare( '!=', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_UNFINISHED ),
			);

			$search->setConditions( $search->combine( '&&', $expr ) );
		}

		return $search;
	}


	/**
	 * Creates a one-time order in the storage from the given invoice object.
	 *
	 * @param MShop_Common_Item_Interface $item Order item with necessary values
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Order_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if($item->getBaseId() === null) {
			throw new MShop_Order_Exception('Required order base ID is missing');
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
				/** mshop/order/manager/default/item/insert
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
				 * @see mshop/order/manager/default/item/update
				 * @see mshop/order/manager/default/item/newid
				 * @see mshop/order/manager/default/item/delete
				 * @see mshop/order/manager/default/item/search
				 * @see mshop/order/manager/default/item/count
				 */
				$path = 'mshop/order/manager/default/item/insert';
			}
			else
			{
				/** mshop/order/manager/default/item/update
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
				 * @see mshop/order/manager/default/item/insert
				 * @see mshop/order/manager/default/item/newid
				 * @see mshop/order/manager/default/item/delete
				 * @see mshop/order/manager/default/item/search
				 * @see mshop/order/manager/default/item/count
				 */
				$path = 'mshop/order/manager/default/item/update';
			}

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind( 1, $item->getBaseId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $item->getType() );
			$stmt->bind( 4, $item->getDatePayment() );
			$stmt->bind( 5, $item->getDateDelivery() );
			$stmt->bind( 6, $item->getDeliveryStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $item->getPaymentStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 8, $item->getRelatedId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 9, $date ); //mtime
			$stmt->bind( 10, $context->getEditor());

			if( $id !== null ) {
				$stmt->bind( 11, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id ); //is not modified anymore
			} else {
				$stmt->bind( 11, $date ); //ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/order/manager/default/item/newid
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
				 * @see mshop/order/manager/default/item/insert
				 * @see mshop/order/manager/default/item/update
				 * @see mshop/order/manager/default/item/delete
				 * @see mshop/order/manager/default/item/search
				 * @see mshop/order/manager/default/item/count
				 */
				$path = 'mshop/order/manager/default/item/newid';
				$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}


		$this->_addStatus( $item );
	}


	/**
	 * Returns an order invoice item built from database values.
	 *
	 * @param integer $id Unique id of the order invoice
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Order_Item_Interface Returns order invoice item of the given id
	 * @throws MShop_Order_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array())
	{
		return $this->_getItem( 'order.id', $id, $ref );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/order/manager/default/item/delete
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
		 * @see mshop/order/manager/default/item/insert
		 * @see mshop/order/manager/default/item/update
		 * @see mshop/order/manager/default/item/newid
		 * @see mshop/order/manager/default/item/search
		 * @see mshop/order/manager/default/item/count
		 */
		$path = 'mshop/order/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/order/manager/submanagers
		 * List of manager names that can be instantiated by the order manager
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
		$path = 'classes/order/manager/submanagers';
		$default = array( 'base', 'status' );

		return $this->_getSearchAttributes( $this->_searchConfig, $path, $default, $withsub );
	}


	/**
	 * Searches for orders based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref Not used
	 * @param integer &$total Number of items that are available in total
	 * @return array List of items implementing MShop_Order_Item_Interface
	 * @throws MShop_Order_Exception If creating items failed
	 * @throws MW_DB_Exception If a database operation fails
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		$items = array();

		try
		{
			$required = array( 'order' );
			$sitelevel = MShop_Locale_Manager_Abstract::SITE_SUBTREE;

			/** mshop/order/manager/default/item/search
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
			 * @see mshop/order/manager/default/item/insert
			 * @see mshop/order/manager/default/item/update
			 * @see mshop/order/manager/default/item/newid
			 * @see mshop/order/manager/default/item/delete
			 * @see mshop/order/manager/default/item/count
			 */
			$cfgPathSearch = 'mshop/order/manager/default/item/search';

			/** mshop/order/manager/default/item/count
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
			 * @see mshop/order/manager/default/item/insert
			 * @see mshop/order/manager/default/item/update
			 * @see mshop/order/manager/default/item/newid
			 * @see mshop/order/manager/default/item/delete
			 * @see mshop/order/manager/default/item/search
			 */
			$cfgPathCount =  'mshop/order/manager/default/item/count';

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $sitelevel );

			try
			{
				while( ( $row = $results->fetch() ) !== false ) {
					$items[ $row['id'] ] = $this->_createItem( $row );
				}
			}
			catch( Exception $e )
			{
				$results->finish();
				throw $e;
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
	 * Returns a new manager for order extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Interface Manager for different extensions, e.g base, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/order/manager/name
		 * Class name of the used order manager implementation
		 *
		 * Each default order manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Order_Manager_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Order_Manager_Myorder
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/order/manager/name = Myorder
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyOrder"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/order/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the order manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the order manager.
		 *
		 *  mshop/order/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the order manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/decorators/global
		 * @see mshop/order/manager/decorators/local
		 */

		/** mshop/order/manager/decorators/global
		 * Adds a list of globally available decorators only to the order manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order manager.
		 *
		 *  mshop/order/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the order controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/decorators/excludes
		 * @see mshop/order/manager/decorators/local
		 */

		/** mshop/order/manager/decorators/local
		 * Adds a list of local decorators only to the order manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the order manager.
		 *
		 *  mshop/order/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the order
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/order/manager/decorators/excludes
		 * @see mshop/order/manager/decorators/global
		 */

		return $this->_getSubManager( 'order', $manager, $name );
	}


	/**
	 * Adds the new payment and delivery values to the order status log.
	 *
	 * @param MShop_Order_Item_Interface $item Order item object
	 */
	protected function _addStatus( MShop_Order_Item_Interface $item )
	{
		$statusManager = MShop_Factory::createManager( $this->_getContext(), 'order/status' );

		$statusItem = $statusManager->createItem();
		$statusItem->setParentId( $item->getId() );

		if( $item->getPaymentStatus() != $item->oldPaymentStatus )
		{
			$statusItem->setId( null );
			$statusItem->setType( MShop_Order_Item_Status_Abstract::STATUS_PAYMENT );
			$statusItem->setValue( $item->getPaymentStatus() );

			$statusManager->saveItem( $statusItem, false );
		}

		if( $item->getDeliveryStatus() != $item->oldDeliveryStatus )
		{
			$statusItem->setId( null );
			$statusItem->setType( MShop_Order_Item_Status_Abstract::STATUS_DELIVERY );
			$statusItem->setValue( $item->getDeliveryStatus() );

			$statusManager->saveItem( $statusItem, false );
		}
	}


	/**
	 * Creates a new order item.
	 *
	 * @param array $values List of attributes for order item
	 * @return MShop_Order_Item_Interface New order item
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Order_Item_Default( $values );
	}
}
