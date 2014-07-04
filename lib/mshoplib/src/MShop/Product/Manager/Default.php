<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Product
 */


/**
 * Default product manager.
 *
 * @package MShop
 * @subpackage Product
 */
class MShop_Product_Manager_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Product_Manager_Interface
{
	private $_searchConfig = array(
		'product.id'=> array(
			'code'=>'product.id',
			'internalcode'=>'mpro."id"',
			'label'=>'Product ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.siteid'=> array(
			'code'=>'product.siteid',
			'internalcode'=>'mpro."siteid"',
			'label'=>'Product site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.typeid'=> array(
			'code'=>'product.typeid',
			'internalcode'=>'mpro."typeid"',
			'label'=>'Product type ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'product.code'=> array(
			'code'=>'product.code',
			'internalcode'=>'mpro."code"',
			'label'=>'Product code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.label'=> array(
			'code'=>'product.label',
			'internalcode'=>'mpro."label"',
			'label'=>'Product label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.suppliercode'=> array(
			'code'=>'product.suppliercode',
			'internalcode'=>'mpro."suppliercode"',
			'label'=>'Product supplier code',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.datestart'=> array(
			'code'=>'product.datestart',
			'internalcode'=>'mpro."start"',
			'label'=>'Product start date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.dateend'=> array(
			'code'=>'product.dateend',
			'internalcode'=>'mpro."end"',
			'label'=>'Product end date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.ctime'=> array(
			'code'=>'product.ctime',
			'internalcode'=>'mpro."ctime"',
			'label'=>'Product create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.mtime'=> array(
			'code'=>'product.mtime',
			'internalcode'=>'mpro."mtime"',
			'label'=>'Product modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.editor'=> array(
			'code'=>'product.editor',
			'internalcode'=>'mpro."editor"',
			'label'=>'Product editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'product.status'=> array(
			'code'=>'product.status',
			'internalcode'=>'mpro."status"',
			'label'=>'Product status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'product.contains' => array(
			'code'=>'product.contains()',
			'internalcode'=>'',
			'label'=>'Number of product list items, parameter(<domain>,<list type ID>,<reference IDs>)',
			'type'=> 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
	);


	/**
	 * Creates the product manager that will use the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 */
	public function __construct(MShop_Context_Item_Interface $context)
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-product' );

		$date = date( 'Y-m-d H:i:00' );

		$this->_searchConfig['product.contains']['internalcode'] =
			'( SELECT COUNT(mproli2."parentid") FROM "mshop_product_list" AS mproli2
				WHERE mpro."id" = mproli2."parentid" AND :site
					AND mproli2."domain" = $1 AND mproli2."refid" IN ( $3 ) AND mproli2."typeid" = $2
					AND ( mproli2."start" IS NULL OR mproli2."start" <= \'' . $date . '\' )
					AND ( mproli2."end" IS NULL OR mproli2."end" >= \'' . $date . '\' ) )';

		$sites = $context->getLocale()->getSitePath();
		$this->_replaceSiteMarker( $this->_searchConfig['product.contains'], 'mproli2."siteid"', $sites, ':site' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/product/manager/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'type', 'stock', 'list' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->_cleanup( $siteids, 'mshop/product/manager/default/item/delete' );
	}


	/**
	 * Create new product item object.
	 *
	 * @return MShop_Product_Item_Interface
	 */
	public function createItem()
	{
		$values = array('siteid' => $this->_getContext()->getLocale()->getSiteId());
		return $this->_createItem($values);
	}


	/**
	 * Adds a new product to the storage.
	 *
	 * @param MShop_Product_Item_Interface $product Product item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Product_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Product_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
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
				/** mshop/product/manager/default/item/insert
				 * Inserts a new product record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the product item to the statement before they are
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
				 * @see mshop/product/manager/default/item/update
				 * @see mshop/product/manager/default/item/newid
				 * @see mshop/product/manager/default/item/delete
				 * @see mshop/product/manager/default/item/search
				 * @see mshop/product/manager/default/item/count
				 */
				$path = 'mshop/product/manager/default/item/insert';
			}
			else
			{
				/** mshop/product/manager/default/item/update
				 * Updates an existing product record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the product item to the statement before they are
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
				 * @see mshop/product/manager/default/item/insert
				 * @see mshop/product/manager/default/item/newid
				 * @see mshop/product/manager/default/item/delete
				 * @see mshop/product/manager/default/item/search
				 * @see mshop/product/manager/default/item/count
				 */
				$path = 'mshop/product/manager/default/item/update';
			}

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getTypeId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $item->getCode() );
			$stmt->bind( 4, $item->getSupplierCode() );
			$stmt->bind( 5, $item->getLabel() );
			$stmt->bind( 6, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $item->getDateStart() );
			$stmt->bind( 8, $item->getDateEnd() );
			$stmt->bind( 9, $date ); // mtime
			$stmt->bind( 10, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 11, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id ); //so item is no longer modified
			} else {
				$stmt->bind( 11, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/product/manager/default/item/newid
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
				 *  SELECT currval('seq_mpro_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mpro_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/product/manager/default/item/insert
				 * @see mshop/product/manager/default/item/update
				 * @see mshop/product/manager/default/item/delete
				 * @see mshop/product/manager/default/item/search
				 * @see mshop/product/manager/default/item/count
				 */
				$path = 'mshop/product/manager/default/item/newid';
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
		/** mshop/product/manager/default/item/delete
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
		 * @see mshop/product/manager/default/item/insert
		 * @see mshop/product/manager/default/item/update
		 * @see mshop/product/manager/default/item/newid
		 * @see mshop/product/manager/default/item/search
		 * @see mshop/product/manager/default/item/count
		 */
		$path = 'mshop/product/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Returns the product item for the given product ID.
	 *
	 * @param integer $id Unique ID of the product item
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Product_Item_Interface Returns the product item of the given id
	 * @throws MShop_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'product.id', $id, $ref );
	}


	/**
	 * Search for products based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 *
	 * @return array List of products implementing MShop_Product_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$map = $typeIds = array();
		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'product' );
			$level = MShop_Locale_Manager_Abstract::SITE_ALL;

			/** mshop/product/manager/default/item/search
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
			 * @see mshop/product/manager/default/item/insert
			 * @see mshop/product/manager/default/item/update
			 * @see mshop/product/manager/default/item/newid
			 * @see mshop/product/manager/default/item/delete
			 * @see mshop/product/manager/default/item/count
			 */
			$cfgPathSearch = 'mshop/product/manager/default/item/search';

			/** mshop/product/manager/default/item/count
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
			 * @see mshop/product/manager/default/item/insert
			 * @see mshop/product/manager/default/item/update
			 * @see mshop/product/manager/default/item/newid
			 * @see mshop/product/manager/default/item/delete
			 * @see mshop/product/manager/default/item/search
			 */
			$cfgPathCount =  'mshop/product/manager/default/item/count';

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$map[ $row['id'] ] = $row;
				$typeIds[ $row['typeid'] ] = null;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		if( !empty( $typeIds ) )
		{
			$typeManager = $this->getSubManager( 'type' );
			$typeSearch = $typeManager->createSearch();
			$typeSearch->setConditions( $typeSearch->compare( '==', 'product.type.id', array_keys( $typeIds ) ) );
			$typeSearch->setSlice( 0, $search->getSliceSize() );
			$typeItems = $typeManager->searchItems( $typeSearch );

			foreach( $map as $id => $row )
			{
				if( isset( $typeItems[ $row['typeid'] ] ) ) {
					$map[$id]['type'] = $typeItems[ $row['typeid'] ]->getCode();
				}
			}
		}

		return $this->_buildItems( $map, $ref, 'product' );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withsub === true )
		{
			/** classes/product/manager/submanagers
			 * List of manager names that can be instantiated by the product manager
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
			$path = 'classes/product/manager/submanagers';

			foreach( $this->_getContext()->getConfig()->get( $path, array( 'type', 'stock', 'list' ) ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes( true ) );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for product extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/product/manager/name
		 * Class name of the used product manager implementation
		 *
		 * Each default product manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Product_Manager_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Product_Manager_Myproduct
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/product/manager/name = Myproduct
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyProduct"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/product/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the product manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the product manager.
		 *
		 *  mshop/product/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the product manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/product/manager/decorators/global
		 * @see mshop/product/manager/decorators/local
		 */

		/** mshop/product/manager/decorators/global
		 * Adds a list of globally available decorators only to the product manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the product manager.
		 *
		 *  mshop/product/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the product controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/product/manager/decorators/excludes
		 * @see mshop/product/manager/decorators/local
		 */

		/** mshop/product/manager/decorators/local
		 * Adds a list of local decorators only to the product manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the product manager.
		 *
		 *  mshop/product/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the product
		 * controller.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/product/manager/decorators/excludes
		 * @see mshop/product/manager/decorators/global
		 */

		return $this->_getSubManager( 'product', $manager, $name );
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$curDate = date( 'Y-m-d H:i:00', time() );
			$object = $this->_createSearch( 'product' );

			$expr = array( $object->getConditions() );

			$temp = array();
			$temp[] = $object->compare( '==', 'product.datestart', null );
			$temp[] = $object->compare( '<=', 'product.datestart', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$temp = array();
			$temp[] = $object->compare( '==', 'product.dateend', null );
			$temp[] = $object->compare( '>=', 'product.dateend', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$object->setConditions( $object->combine( '&&', $expr ) );

			return $object;
		}

		return parent::createSearch();
	}


	/**
	* Returns the type search configurations array for the type manager.
	*
	* @return array associative array of the search code as key and the definitions as associative array
	*/
	protected function _getListTypeSearchConfig()
	{
		return $this->_listTypeSearchConfig;
	}


	/**
	* Returns the list search definitions for the list manager.
	*
	* @return array Associative array of the search code as key and the definition as associative array
	*/
	protected function _getListSearchConfig()
	{
		return $this->_listSearchConfig;
	}


	/**
	* Returns the type search configuration definitons for the type manager.
	*
	* @return array Associative array of the search code as key and the definition as associative array
	*/
	protected function _getTypeSearchConfig()
	{
		return $this->_typeSearchConfig;
	}


	/**
	 * Create new product item object initialized with given parameters.
	 *
	 * @param MShop_Product_Item_Interface $product Product item object
	 * @return array Associative list of key/value pairs suitable for product item constructor
	 */
	protected function _createArray( MShop_Product_Item_Interface $item )
	{
		return array(
			'id' => $item->getId(),
			'typeid' => $item->getTypeId(),
			'type' => $item->getType(),
			'status' => $item->getStatus(),
			'label' => $item->getLabel(),
			'start' => $item->getDateStart(),
			'end' => $item->getDateEnd(),
			'code' => $item->getCode(),
			'suppliercode' => $item->getSupplierCode(),
			'ctime' => $item->getTimeCreated(),
			'mtime' => $item->getTimeModified(),
			'editor' => $item->getEditor(),
		);
	}


	/**
	 * Create new product item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listitems List of items implementing MShop_Common_Item_List_Interface
	 * @param array $textItems List of items implementing MShop_Text_Item_Interface
	 * @return MShop_Product_Item_Interface New product item
	 */
	protected function _createItem( array $values = array(), array $listitems = array(), array $textItems = array() )
	{
		return new MShop_Product_Item_Default( $values, $listitems, $textItems );
	}
}