<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 */


/**
 * Default product stock warehouse manager implementation.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Manager_Stock_Warehouse_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Product_Manager_Stock_Warehouse_Interface
{
	private $_searchConfig = array(
		'product.stock.warehouse.id'=> array(
			'code'=>'product.stock.warehouse.id',
			'internalcode'=>'mprostwa."id"',
			'internaldeps'=>array( 'LEFT JOIN "mshop_product_stock_warehouse" AS mprostwa ON mprost."warehouseid" = mprostwa."id"' ),
			'label'=>'Product stock warehouse ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.stock.warehouse.siteid'=> array(
			'code'=>'product.stock.warehouse.siteid',
			'internalcode'=>'mprostwa."siteid"',
			'label'=>'Product stock warehouse site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.stock.warehouse.code'=> array(
			'code'=>'product.stock.warehouse.code',
			'internalcode'=>'mprostwa."code"',
			'label'=>'Product stock warehouse code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.warehouse.label'=> array(
			'code'=>'product.stock.warehouse.label',
			'internalcode'=>'mprostwa."label"',
			'label'=>'Product stock warehouse label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.warehouse.status'=> array(
			'code'=>'product.stock.warehouse.status',
			'internalcode'=>'mprostwa."status"',
			'label'=>'Product stock warehouse status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.stock.warehouse.mtime'=> array(
			'code'=>'product.stock.warehouse.mtime',
			'internalcode'=>'mprostwa."mtime"',
			'label'=>'Product stock warehouse modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.warehouse.ctime'=> array(
			'code'=>'product.stock.warehouse.ctime',
			'internalcode'=>'mprostwa."ctime"',
			'label'=>'Product stock warehouse creation date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.stock.warehouse.editor'=> array(
			'code'=>'product.stock.warehouse.editor',
			'internalcode'=>'mprostwa."editor"',
			'label'=>'Product stock warehouse editor',
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
		$this->_setResourceName( 'db-product' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/product/manager/stock/wareshouse/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/product/manager/stock/warehouse/default/item/delete' );
	}


	/**
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Product_Manager_Interface manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/product/manager/stock/warehouse/name
		 * Class name of the used product stock warehouse manager implementation
		 *
		 * Each default product stock warehouse manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Product_Manager_Stock_Warehouse_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Product_Manager_Stock_Warehouse_Mywarehouse
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/product/manager/stock/warehouse/name = Mywarehouse
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyWarehouse"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/product/manager/stock/warehouse/decorators/excludes
		 * Excludes decorators added by the "common" option from the product stock warehouse manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the product stock warehouse manager.
		 *
		 *  mshop/product/manager/stock/warehouse/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the product stock warehouse manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/product/manager/stock/warehouse/decorators/global
		 * @see mshop/product/manager/stock/warehouse/decorators/local
		 */

		/** mshop/product/manager/stock/warehouse/decorators/global
		 * Adds a list of globally available decorators only to the product stock warehouse manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the product stock warehouse manager.
		 *
		 *  mshop/product/manager/stock/warehouse/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the product controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/product/manager/stock/warehouse/decorators/excludes
		 * @see mshop/product/manager/stock/warehouse/decorators/local
		 */

		/** mshop/product/manager/stock/warehouse/decorators/local
		 * Adds a list of local decorators only to the product stock warehouse manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the product stock warehouse manager.
		 *
		 *  mshop/product/manager/stock/warehouse/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the product
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/product/manager/stock/warehouse/decorators/excludes
		 * @see mshop/product/manager/stock/warehouse/decorators/global
		 */

		return $this->_getSubManager( 'product', 'stock/warehouse/' . $manager, $name );
	}


	/**
	 * Creates new warehouse item object.
	 *
	 * @return MShop_Product_Item_Stock_Warehouse_Default New product warehouse item object
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->_getContext()->getLocale()->getSiteId() );
		return $this->_createItem( $values );
	}


	/**
	 * Inserts the new warehouse item
	 *
	 * @param MShop_Product_Item_Stock_Warehouse_Interface $item Warehouse item which should be saved
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Product_Item_Stock_Warehouse_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Product_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

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
				/** mshop/product/manager/stock/warehouse/default/item/insert
				 * Inserts a new product stock warehouse record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the product stock warehouse item to the statement before they are
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
				 * @see mshop/product/manager/stock/warehouse/default/item/update
				 * @see mshop/product/manager/stock/warehouse/default/item/newid
				 * @see mshop/product/manager/stock/warehouse/default/item/delete
				 * @see mshop/product/manager/stock/warehouse/default/item/search
				 * @see mshop/product/manager/stock/warehouse/default/item/count
				 */
				$path = 'mshop/product/manager/stock/warehouse/default/item/insert';
			}
			else
			{
				/** mshop/product/manager/stock/warehouse/default/item/update
				 * Updates an existing product stock warehouse record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the product stock warehouse item to the statement before they are
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
				 * @see mshop/product/manager/stock/warehouse/default/item/insert
				 * @see mshop/product/manager/stock/warehouse/default/item/newid
				 * @see mshop/product/manager/stock/warehouse/default/item/delete
				 * @see mshop/product/manager/stock/warehouse/default/item/search
				 * @see mshop/product/manager/stock/warehouse/default/item/count
				 */
				$path = 'mshop/product/manager/stock/warehouse/default/item/update';
			}

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getCode() );
			$stmt->bind( 3, $item->getLabel() );
			$stmt->bind( 4, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, $date ); //mtime
			$stmt->bind( 6, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 7, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 7, $date ); //ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/product/manager/stock/warehouse/default/item/newid
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
				 *  SELECT currval('seq_mprostwa_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mprostwa_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/product/manager/stock/warehouse/default/item/insert
				 * @see mshop/product/manager/stock/warehouse/default/item/update
				 * @see mshop/product/manager/stock/warehouse/default/item/delete
				 * @see mshop/product/manager/stock/warehouse/default/item/search
				 * @see mshop/product/manager/stock/warehouse/default/item/count
				 */
				$path = 'mshop/product/manager/stock/warehouse/default/item/newid';
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
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/product/manager/stock/warehouse/default/item/delete
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the product database.
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
		 * @see mshop/product/manager/stock/warehouse/default/item/insert
		 * @see mshop/product/manager/stock/warehouse/default/item/update
		 * @see mshop/product/manager/stock/warehouse/default/item/newid
		 * @see mshop/product/manager/stock/warehouse/default/item/search
		 * @see mshop/product/manager/stock/warehouse/default/item/count
		 */
		$path = 'mshop/product/manager/stock/warehouse/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Creates a warehouse item object for the given item id.
	 *
	 * @param integer $id Id of the warehouse item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Common_Item_Interface Returns product warehouse item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'product.stock.warehouse.id', $id, $ref );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/product/manager/stock/warehouse/submanagers
		 * List of manager names that can be instantiated by the product stock warehouse manager
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
		$path = 'classes/product/manager/stock/warehouse/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array(), $withsub );
	}


	/**
	 * Search for warehouse items based on the given critera.
	 *
	 * Possible search keys: 'product.warehouse.id', 'product.warehouse.siteid', 'product.warehouse.code'
	 *
	 * @param MW_Common_Criteria_Interface $search Search object with search conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of warehouse items implementing MShop_Product_Item_Warehouse_Interface
	 * @throws MShop_Product_Exception if creating items failed
	 * @see MW_Common_Criteria_SQL
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = array();
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'product.stock.warehouse' );
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;

			/** mshop/product/manager/stock/warehouse/default/item/search
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the product
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
			 * @see mshop/product/manager/stock/warehouse/default/item/insert
			 * @see mshop/product/manager/stock/warehouse/default/item/update
			 * @see mshop/product/manager/stock/warehouse/default/item/newid
			 * @see mshop/product/manager/stock/warehouse/default/item/delete
			 * @see mshop/product/manager/stock/warehouse/default/item/count
			 */
			$cfgPathSearch = 'mshop/product/manager/stock/warehouse/default/item/search';

			/** mshop/product/manager/stock/warehouse/default/item/count
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the product
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
			 * @see mshop/product/manager/stock/warehouse/default/item/insert
			 * @see mshop/product/manager/stock/warehouse/default/item/update
			 * @see mshop/product/manager/stock/warehouse/default/item/newid
			 * @see mshop/product/manager/stock/warehouse/default/item/delete
			 * @see mshop/product/manager/stock/warehouse/default/item/search
			 */
			$cfgPathCount =  'mshop/product/manager/stock/warehouse/default/item/count';

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );
			while( ( $row = $results->fetch() ) !== false ) {
				$items[ $row['id'] ] = $this->_createItem( $row );
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
	 * Creates new warehouse item object.
	 *
	 * @param array $values Possible optional array keys can be given: id, siteid, code
	 * @return MShop_Product_Item_Stock_Warehouse_Default New Warehouse item object
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Product_Item_Stock_Warehouse_Default( $values );
	}
}
