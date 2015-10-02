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
class MShop_Coupon_Manager_Default
	extends MShop_Coupon_Manager_Abstract
	implements MShop_Coupon_Manager_Interface
{
	private $_searchConfig = array(
		'coupon.id'=> array(
			'code'=>'coupon.id',
			'internalcode'=>'mcou."id"',
			'label'=>'Coupon ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'coupon.siteid'=> array(
			'code'=>'coupon.siteid',
			'internalcode'=>'mcou."siteid"',
			'label'=>'Coupon site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
			'public' => false,
		),
		'coupon.label'=> array(
			'code'=>'coupon.label',
			'internalcode'=>'mcou."label"',
			'label'=>'Coupon label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.provider'=> array(
			'code'=>'coupon.provider',
			'internalcode'=>'mcou."provider"',
			'label'=>'Coupon method',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.config'=> array(
			'code'=>'coupon.config',
			'internalcode'=>'mcou."config"',
			'label'=>'Coupon config',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.datestart'=> array(
			'code'=>'coupon.datestart',
			'internalcode'=>'mcou."start"',
			'label'=>'Coupon start date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.dateend'=> array(
			'code'=>'coupon.dateend',
			'internalcode'=>'mcou."end"',
			'label'=>'Coupon end date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.status'=> array(
			'code'=>'coupon.status',
			'internalcode'=>'mcou."status"',
			'label'=>'Coupon status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'coupon.ctime'=> array(
			'code'=>'coupon.ctime',
			'internalcode'=>'mcou."ctime"',
			'label'=>'Coupon create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.mtime'=> array(
			'code'=>'coupon.mtime',
			'internalcode'=>'mcou."mtime"',
			'label'=>'Coupon modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'coupon.editor'=> array(
			'code'=>'coupon.editor',
			'internalcode'=>'mcou."editor"',
			'label'=>'Coupon editor',
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
		$path = 'classes/coupon/manager/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array( 'code' ) ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/coupon/manager/default/item/delete' );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/coupon/manager/submanagers
		 * List of manager names that can be instantiated by the coupon manager
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
		$path = 'classes/coupon/manager/submanagers';

		return $this->getSearchAttributesBase( $this->_searchConfig, $path, array( 'code' ), $withsub );
	}


	/**
	 * Creates a new empty coupon item instance
	 *
	 * @return MShop_Coupon_Item_Interface Creates a blank coupon item
	 */
	public function createItem()
	{
		$values = array( 'siteid'=> $this->_getContext()->getLocale()->getSiteId() );
		return $this->_createItem( $values );
	}


	/**
	 * Returns the coupons item specified by its ID.
	 *
	 * @param string $id Unique ID of the coupon item in the storage
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MShop_Coupon_Item_Interface Returns the coupon item of the given ID
	 * @throws MShop_Exception If coupon couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		return $this->_getItem( 'coupon.id', $id, $ref );
	}


	/**
	 * Saves a coupon item to the storage.
	 *
	 * @param MShop_Coupon_Item_Interface $item Coupon implementing the coupon interface
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @throws MShop_Coupon_Exception If coupon couldn't be saved
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MShop_Coupon_Item_Interface';
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
				/** mshop/coupon/manager/default/item/insert
				 * Inserts a new coupon record into the database table
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
				 * @see mshop/coupon/manager/default/item/update
				 * @see mshop/coupon/manager/default/item/newid
				 * @see mshop/coupon/manager/default/item/delete
				 * @see mshop/coupon/manager/default/item/search
				 * @see mshop/coupon/manager/default/item/count
				 */
				$path = 'mshop/coupon/manager/default/item/insert';
			}
			else
			{
				/** mshop/coupon/manager/default/item/update
				 * Updates an existing coupon record in the database
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
				 * @see mshop/coupon/manager/default/item/insert
				 * @see mshop/coupon/manager/default/item/newid
				 * @see mshop/coupon/manager/default/item/delete
				 * @see mshop/coupon/manager/default/item/search
				 * @see mshop/coupon/manager/default/item/count
				 */
				$path = 'mshop/coupon/manager/default/item/update';
			}

			$stmt = $this->_getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId() );
			$stmt->bind( 2, $item->getLabel() );
			$stmt->bind( 3, $item->getProvider() );
			$stmt->bind( 4, json_encode( $item->getConfig() ) );
			$stmt->bind( 5, $item->getDateStart() );
			$stmt->bind( 6, $item->getDateEnd() );
			$stmt->bind( 7, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 8, $date ); // mtime
			$stmt->bind( 9, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 10, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 10, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/coupon/manager/default/item/newid
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
				 *  SELECT currval('seq_mcou_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_mcou_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see mshop/coupon/manager/default/item/insert
				 * @see mshop/coupon/manager/default/item/update
				 * @see mshop/coupon/manager/default/item/delete
				 * @see mshop/coupon/manager/default/item/search
				 * @see mshop/coupon/manager/default/item/count
				 */
				$path = 'mshop/coupon/manager/default/item/newid';
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
		/** mshop/coupon/manager/default/item/delete
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
		 * @see mshop/coupon/manager/default/item/insert
		 * @see mshop/coupon/manager/default/item/update
		 * @see mshop/coupon/manager/default/item/newid
		 * @see mshop/coupon/manager/default/item/search
		 * @see mshop/coupon/manager/default/item/count
		 */
		$path = 'mshop/coupon/manager/default/item/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Searchs for coupon items based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array Returns a list of coupon items implementing MShop_Coupon_Item_Interface
	 *
	 * @throws MW_DB_Exception On failures with the db object
	 * @throws MShop_Common_Exception On failures with the MW_Common_Criteria_ object
	 * @throws MShop_Coupon_Exception On failures with the coupon items
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$dbm = $this->_getContext()->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );
		$items = array();

		try
		{
			$required = array( 'coupon' );
			$level = MShop_Locale_Manager_Abstract::SITE_PATH;

			/** mshop/coupon/manager/default/item/search
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
			 * @see mshop/coupon/manager/default/item/insert
			 * @see mshop/coupon/manager/default/item/update
			 * @see mshop/coupon/manager/default/item/newid
			 * @see mshop/coupon/manager/default/item/delete
			 * @see mshop/coupon/manager/default/item/count
			 */
			$cfgPathSearch = 'mshop/coupon/manager/default/item/search';

			/** mshop/coupon/manager/default/item/count
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
			 * @see mshop/coupon/manager/default/item/insert
			 * @see mshop/coupon/manager/default/item/update
			 * @see mshop/coupon/manager/default/item/newid
			 * @see mshop/coupon/manager/default/item/delete
			 * @see mshop/coupon/manager/default/item/search
			 */
			$cfgPathCount = 'mshop/coupon/manager/default/item/count';

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== false )
				{
					$config = $row['config'];

					if( ( $row['config'] = json_decode( $row['config'], true ) ) === null )
					{
						$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'mshop_locale.config', $row['id'], $config );
						$this->_getContext()->getLogger()->log( $msg, MW_Logger_Abstract::WARN );
					}

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
	 * Returns a new sub manager of the given type and name.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_List_Interface List manager
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'coupon', $manager, $name );
	}


	/**
	 * Returns the coupon model which belongs to the given code.
	 *
	 * @param MShop_Coupon_Item_Interface $item Coupon item interface
	 * @return MShop_Coupon_Provider_Interface Returns a coupon provider instance
	 * @throws MShop_Coupon_Exception If coupon couldn't be found
	 */
	public function getProvider( MShop_Coupon_Item_Interface $item, $code )
	{
		$names = explode( ',', $item->getProvider() );

		if( ( $providername = array_shift( $names ) ) === null ) {
			throw new MShop_Coupon_Exception( sprintf( 'Provider in "%1$s" not available', $item->getProvider() ) );
		}

		if( ctype_alnum( $providername ) === false ) {
			throw new MShop_Coupon_Exception( sprintf( 'Invalid characters in provider name "%1$s"', $providername ) );
		}

		$interface = 'MShop_Coupon_Provider_Factory_Interface';
		$classname = 'MShop_Coupon_Provider_' . $providername;

		if( class_exists( $classname ) === false ) {
			throw new MShop_Coupon_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$context = $this->_getContext();
		$provider = new $classname( $context, $item, $code );

		if( ( $provider instanceof $interface ) === false )
		{
			$msg = sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface );
			throw new MShop_Coupon_Exception( $msg );
		}

		/** mshop/coupon/provider/decorators
		 * Adds a list of decorators to all coupon provider objects automatcally
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap decorators
		 * ("MShop_Coupon_Provider_Decorator_*") around the coupon provider.
		 *
		 *  mshop/coupon/provider/decorators = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "MShop_Coupon_Provider_Decorator_Decorator1" to all coupon provider
		 * objects.
		 *
		 * @param array List of decorator names
		 * @since 2014.05
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/account/favorite/decorators/excludes
		 * @see client/html/account/favorite/decorators/local
		 */
		$decorators = $context->getConfig()->get( 'mshop/coupon/provider/decorators', array() );

		$object = $this->_addCouponDecorators( $item, $code, $provider, $names );
		$object = $this->_addCouponDecorators( $item, $code, $object, $decorators );
		$object->setObject( $object );

		return $object;
	}


	/**
	 * Creates a search object and sets base criteria
	 *
	 * @param boolean $default
	 * @return MW_Common_Criteria_Interface
	 */
	public function createSearch( $default = false )
	{
		if( $default === true )
		{
			$object = $this->createSearchBase( 'coupon' );
			$curDate = date( 'Y-m-d H:i:00', time() );

			$expr = array();
			$expr[] = $object->getConditions();

			$temp = array();
			$temp[] = $object->compare( '==', 'coupon.datestart', null );
			$temp[] = $object->compare( '<=', 'coupon.datestart', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$temp = array();
			$temp[] = $object->compare( '==', 'coupon.dateend', null );
			$temp[] = $object->compare( '>=', 'coupon.dateend', $curDate );
			$expr[] = $object->combine( '||', $temp );

			$object->setConditions( $object->combine( '&&', $expr ) );

			return $object;
		}

		return parent::createSearch();
	}


	/**
	 * Creates a new coupon item instance
	 *
	 * @param array $values Values of the coupon item from the storage
	 * @return MShop_Coupon_Item_Default Returns a new created coupon item instance
	 */
	protected function _createItem( array $values = array() )
	{
		return new MShop_Coupon_Item_Default( $values );
	}
}