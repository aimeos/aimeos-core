<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Coupon
 */


/**
 * Default coupon manager interface for creating and handling coupons.
 *
 * @package MShop
 * @subpackage Coupon
 */

class MShop_Coupon_Manager_Code_Default
	extends MShop_Common_Manager_Abstract
	implements MShop_Coupon_Manager_Code_Interface
{
	private $_searchConfig = array(
		'coupon.code.id'=> array(
			'code'=>'coupon.code.id',
			'internalcode'=>'mcouco."id"',
			'internaldeps' => array( 'LEFT JOIN "mshop_coupon_code" AS mcouco ON (mcou."id"=mcouco."couponid")' ),
			'label'=>'Coupon code ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'coupon.code.siteid'=> array(
			'code'=>'coupon.code.siteid',
			'internalcode'=>'mcouco."siteid"',
			'label'=>'Coupon code site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'coupon.code.couponid'=> array(
			'code'=>'coupon.code.couponid',
			'internalcode'=>'mcouco."couponid"',
			'label'=>'Coupon ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'coupon.code.code'=> array(
			'code'=>'coupon.code.code',
			'internalcode'=>'mcouco."code"',
			'label'=>'Coupon code value',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.count'=> array(
			'code'=>'coupon.code.count',
			'internalcode'=>'mcouco."count"',
			'label'=>'Coupon code quantity',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.datestart'=> array(
			'code'=>'coupon.code.datestart',
			'internalcode'=>'mcouco."start"',
			'label'=>'Coupon code start date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.dateend'=> array(
			'code'=>'coupon.code.dateend',
			'internalcode'=>'mcouco."end"',
			'label'=>'Coupon code end date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.ctime'=> array(
			'code'=>'coupon.code.ctime',
			'internalcode'=>'mcouco."ctime"',
			'label'=>'Coupon code create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.mtime'=> array(
			'code'=>'coupon.code.mtime',
			'internalcode'=>'mcouco."mtime"',
			'label'=>'Coupon code modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.code.editor'=> array(
			'code'=>'coupon.code.editor',
			'internalcode'=>'mcouco."editor"',
			'label'=>'Coupon code editor',
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
		$this->_setResourceName( 'db-coupon' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param array $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/coupon/manager/code/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/coupon/manager/code/default/item/delete' );
	}


	/**
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_List_Interface List manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** classes/coupon/manager/code/name
		 * Class name of the used coupon code manager implementation
		 *
		 * Each default coupon code manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  MShop_Coupon_Manager_Address_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  MShop_Coupon_Manager_Address_Mycode
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/coupon/manager/code/name = Mycode
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyAddress"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 * @category Developer
		 */

		/** mshop/coupon/manager/code/decorators/excludes
		 * Excludes decorators added by the "common" option from the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("MShop_Common_Manager_Decorator_*") added via
		 * "mshop/common/manager/decorators/default" for the coupon code manager.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/global
		 * @see mshop/coupon/manager/code/decorators/local
		 */

		/** mshop/coupon/manager/code/decorators/global
		 * Adds a list of globally available decorators only to the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("MShop_Common_Manager_Decorator_*") around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Common_Manager_Decorator_Decorator1" only to the coupon controller.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/excludes
		 * @see mshop/coupon/manager/code/decorators/local
		 */

		/** mshop/coupon/manager/code/decorators/local
		 * Adds a list of local decorators only to the coupon code manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("MShop_Common_Manager_Decorator_*") around the coupon code manager.
		 *
		 *  mshop/coupon/manager/code/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "MShop_Common_Manager_Decorator_Decorator2" only to the coupon
		 * controller.
		 *
		 * @param array Address of decorator names
		 * @since 2014.03
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/coupon/manager/code/decorators/excludes
		 * @see mshop/coupon/manager/code/decorators/global
		 */

		return $this->_getSubManager( 'coupon', 'code/' . $manager, $name );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/coupon/manager/code/submanagers
		 * List of manager names that can be instantiated by the coupon code manager
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
		$path = 'classes/coupon/manager/code/submanagers';

		return $this->_getSearchAttributes( $this->_searchConfig, $path, array(), $withsub );
	}


	/**
	 * Creates a new empty coupon code instance
	 *
	 * @return MShop_Coupon_Item_Code_Interface Emtpy coupon code object
	 */
	public function createItem()
	{
		$values = array( 'siteid'=> $this->_getContext()->getLocale()->getSiteId() );
		return $this->_createItem( $values );
	}


	/**
	 * Returns the coupon code object specified by its ID.
	 *
	 * @param integer $id Unique ID of the coupon code in the storage
	 * @return MShop_Coupon_Item_Code_Interface Coupon code object
	 * @throws MShop_Coupon_Exception If coupon couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'coupon.code.id', $id, $ref );
	}


	/**
	 * Saves a modified code object to the storage.
	 *
	 * @param MShop_Coupon_Item_Code_Interface $item Coupon code object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @throws MShop_Coupon_Exception If coupon couldn't be saved
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Coupon_Item_Code_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MShop_Coupon_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
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
				/** mshop/coupon/manager/code/default/item/insert
				 * Inserts a new coupon code record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the coupon item to the statement before they are
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
				 * @see mshop/coupon/manager/code/default/item/update
				 * @see mshop/coupon/manager/code/default/item/newid
				 * @see mshop/coupon/manager/code/default/item/delete
				 * @see mshop/coupon/manager/code/default/item/search
				 * @see mshop/coupon/manager/code/default/item/count
				 * @see mshop/coupon/manager/code/default/item/counter
				 */
				$path = 'mshop/coupon/manager/code/default/item/insert';
			}
			else
			{
				/** mshop/coupon/manager/code/default/item/update
				 * Updates an existing coupon code record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the coupon item to the statement before they are
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
				 * @see mshop/coupon/manager/code/default/item/insert
				 * @see mshop/coupon/manager/code/default/item/newid
				 * @see mshop/coupon/manager/code/default/item/delete
				 * @see mshop/coupon/manager/code/default/item/search
				 * @see mshop/coupon/manager/code/default/item/count
				 * @see mshop/coupon/manager/code/default/item/counter
				 */
				$path = 'mshop/coupon/manager/code/default/item/update';
			}

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getCouponId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 3, $item->getCode() );
			$stmt->bind( 4, $item->getCount(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, $item->getDateStart() );
			$stmt->bind( 6, $item->getDateEnd() );
			$stmt->bind( 7, $date ); // mtime
			$stmt->bind( 8, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 9, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 9, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/coupon/manager/code/default/item/newid
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
				 *  SELECT currval('seq_mcouco_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mcouco_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/coupon/manager/code/default/item/insert
				 * @see mshop/coupon/manager/code/default/item/update
				 * @see mshop/coupon/manager/code/default/item/delete
				 * @see mshop/coupon/manager/code/default/item/search
				 * @see mshop/coupon/manager/code/default/item/count
				 * @see mshop/coupon/manager/code/default/item/counter
				 */
				$path = 'mshop/coupon/manager/code/default/item/newid';
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
		/** mshop/coupon/manager/code/default/item/delete
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the coupon database.
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
		 * @see mshop/coupon/manager/code/default/item/insert
		 * @see mshop/coupon/manager/code/default/item/update
		 * @see mshop/coupon/manager/code/default/item/newid
		 * @see mshop/coupon/manager/code/default/item/search
		 * @see mshop/coupon/manager/code/default/item/count
		 * @see mshop/coupon/manager/code/default/item/counter
		 */
		$path = 'mshop/coupon/manager/code/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Searchs for coupon items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * Possible search keys: 'coupon.code.id', 'coupon.code.couponid',
	 * 'coupon.code.code', 'coupon.code.count'.
	 *
	 * @param integer &$total Number of items that are available in total (not yet implemented)
	 * @return array List of code items implementing MShop_Coupon_Item_Code_Interface's
	 * @throws MShop_Coupon_Exception
	 * @throws MW_Common_Exception
	 * @throws MW_DB_Exception
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );
		$items = array();

		try
		{
			$required = array( 'coupon.code' );
			$level = MShop_Locale_Manager_Abstract::SITE_PATH;

			/** mshop/coupon/manager/code/default/item/search
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the coupon
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
			 * @see mshop/coupon/manager/code/default/item/insert
			 * @see mshop/coupon/manager/code/default/item/update
			 * @see mshop/coupon/manager/code/default/item/newid
			 * @see mshop/coupon/manager/code/default/item/delete
			 * @see mshop/coupon/manager/code/default/item/count
			 * @see mshop/coupon/manager/code/default/item/counter
			 */
			$cfgPathSearch = 'mshop/coupon/manager/code/default/item/search';

			/** mshop/coupon/manager/code/default/item/count
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the coupon
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
			 * @see mshop/coupon/manager/code/default/item/insert
			 * @see mshop/coupon/manager/code/default/item/update
			 * @see mshop/coupon/manager/code/default/item/newid
			 * @see mshop/coupon/manager/code/default/item/delete
			 * @see mshop/coupon/manager/code/default/item/search
			 * @see mshop/coupon/manager/code/default/item/counter
			 */
			$cfgPathCount = 'mshop/coupon/manager/code/default/item/count';

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== false ) {
					$items[$row['id']] = $this->_createItem( $row );
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
	 * Decreases the counter of the coupon code.
	 *
	 * @param string $code Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be decreased
	 */
	public function decrease( $code, $amount )
	{
		$this->increase( $code, -$amount );
	}



	/**
	 * Increases the counter of the coupon code.
	 *
	 * @param string $code Unique code of a coupon
	 * @param integer $amount Amount the coupon count should be increased
	 */
	public function increase( $code, $amount )
	{
		$context = $this->_getContext();

		$search = $this->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.siteid', $context->getLocale()->getSitePath() ) );

		$types = array( 'coupon.code.siteid' => $this->_searchConfig['coupon.code.siteid']['internaltype'] );
		$translations = array( 'coupon.code.siteid' => 'siteid' );
		$conditions = $search->getConditionString( $types, $translations );

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			/** mshop/coupon/manager/code/default/item/counter
			 * Increases or decreases the counter of the coupon code record matched by the given code
			 *
			 * A counter is associated to each coupon code and it's decreased
			 * each time by one if a code used in an paid order was redeemed
			 * successfully. Shop owners can also use the coupon code counter to
			 * use the same code more often by setting it to an arbitrary value.
			 * In this case, the code can be redeemed until the counter reaches
			 * zero.
			 *
			 * The coupon codes must be from one of the sites that are configured
			 * via the context item. If the current site is part of a tree of
			 * sites, the statement can increase or decrease codes from the
			 * current site and all parent sites if the code is inherited by one
			 * of the parent sites.
			 *
			 * Each time the code is updated, the modification time is set to
			 * the current timestamp and the editor field is updated.
			 *
			 * @param string SQL statement for increasing/decreasing the coupon code count
			 * @since 2014.03
			 * @category Developer
			 * @see mshop/coupon/manager/code/default/item/insert
			 * @see mshop/coupon/manager/code/default/item/update
			 * @see mshop/coupon/manager/code/default/item/newid
			 * @see mshop/coupon/manager/code/default/item/delete
			 * @see mshop/coupon/manager/code/default/item/search
			 * @see mshop/coupon/manager/code/default/item/count
			 */
			$path = 'mshop/coupon/manager/code/default/item/counter';
			$stmt = $conn->create( str_replace( ':cond', $conditions, $context->getConfig()->get( $path, $path ) ) );

			$stmt->bind( 1, $amount, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, date( 'Y-m-d H:i:s' ) ); // mtime
			$stmt->bind( 3, $context->getEditor() );
			$stmt->bind( 4, $code );

			$stmt->execute()->finish();
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		$dbm->release( $conn, $dbname );
	}


	/**
	 * Creates a new code instance
	 *
	 * @return MShop_Coupon_Item_Code_Interface Emtpy coupon code object
	 */
	public function _createItem( array $values = array() )
	{
		return new MShop_Coupon_Item_Code_Default( $values );
	}


	/**
	 * creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		$object = new MW_Common_Criteria_SQL( $conn );

		$dbm->release( $conn, $dbname );

		if( $default === true )
		{
			$curDate = date( 'Y-m-d H:i:00', time() );

			$expr = array(
				$object->compare( '>', 'coupon.code.count', 0 )
			);

			$temp = array();
			$temp[] = $object->compare( '==', 'coupon.code.datestart', null );
			$temp[] = $object->compare( '<=', 'coupon.code.datestart', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$temp = array();
			$temp[] = $object->compare( '==', 'coupon.code.dateend', null );
			$temp[] = $object->compare( '>=', 'coupon.code.dateend', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$object->setConditions( $object->combine( '&&', $expr ) );
		}

		return $object;
	}
}