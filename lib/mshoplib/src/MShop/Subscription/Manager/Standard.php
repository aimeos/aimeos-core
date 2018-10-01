<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 * @package MShop
 * @subpackage Subscription
 */


namespace Aimeos\MShop\Subscription\Manager;


/**
 * Default subscription manager implementation
 *
 * @package MShop
 * @subpackage Subscription
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Subscription\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $searchConfig = array(
		'subscription.id' => array(
			'code' => 'subscription.id',
			'internalcode' => 'mord."id"',
			'label' => 'Subscription ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'subscription.siteid' => array(
			'code' => 'subscription.siteid',
			'internalcode' => 'mord."siteid"',
			'label' => 'Site ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'subscription.ordbaseid' => array(
			'code' => 'subscription.ordbaseid',
			'internalcode' => 'mord."baseid"',
			'label' => 'Order base ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'subscription.ordprodid' => array(
			'code' => 'subscription.ordprodid',
			'internalcode' => 'mord."ordprodid"',
			'label' => 'Order product ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
			'public' => false,
		),
		'subscription.datenext' => array(
			'code' => 'subscription.datenext',
			'internalcode' => 'mord."next"',
			'label' => 'Next renewal date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'subscription.dateend' => array(
			'code' => 'subscription.dateend',
			'internalcode' => 'mord."end"',
			'label' => 'End of subscription',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'subscription.interval' => array(
			'code' => 'subscription.interval',
			'internalcode' => 'mord."interval"',
			'label' => 'Renewal interval',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'subscription.reason' => array(
			'code' => 'subscription.reason',
			'internalcode' => 'mord."reason"',
			'label' => 'Subscription end reason',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'subscription.status' => array(
			'code' => 'subscription.status',
			'internalcode' => 'mord."status"',
			'label' => 'Subscription status',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'subscription.ctime' => array(
			'code' => 'subscription.ctime',
			'internalcode' => 'mord."ctime"',
			'label' => 'Create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'subscription.mtime' => array(
			'code' => 'subscription.mtime',
			'internalcode' => 'mord."mtime"',
			'label' => 'Modify date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'subscription.editor' => array(
			'code' => 'subscription.editor',
			'internalcode' => 'mord."editor"',
			'label' => 'Editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
	);


	/**
	 * Creates the manager that will use the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-order' );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria
	 * @param string $key Search key to aggregate items for
	 * @return array List of the search keys as key and the number of counted items as value
	 * @todo 2018.04 Add optional parameters to interface
	 */
	public function aggregate( \Aimeos\MW\Criteria\Iface $search, $key, $value = null, $type = null )
	{
		/** mshop/subscription/manager/standard/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/subscription/manager/standard/aggregate/ansi
		 */

		/** mshop/subscription/manager/standard/aggregate/ansi
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * Groups all records by the values in the key column and counts their
		 * occurence. The matched records can be limited by the given criteria
		 * from the subscription database. The records must be from one of the sites
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
		 * @param string SQL statement for aggregating subscription items
		 * @since 2018.04
		 * @category Developer
		 * @see mshop/subscription/manager/standard/insert/ansi
		 * @see mshop/subscription/manager/standard/update/ansi
		 * @see mshop/subscription/manager/standard/newid/ansi
		 * @see mshop/subscription/manager/standard/delete/ansi
		 * @see mshop/subscription/manager/standard/search/ansi
		 * @see mshop/subscription/manager/standard/count/ansi
		 */

		$cfgkey = 'mshop/subscription/manager/standard/aggregate' . $type;
		return $this->aggregateBase( $search, $key, $cfgkey, array( 'subscription' ), $value );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/subscription/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/subscription/manager/standard/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param string|null Type the item should be created with
	 * @param string|null Domain of the type the item should be created with
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Subscription\Item\Iface New subscription item object
	 */
	public function createItem( $type = null, $domain = null, array $values = [] )
	{
		$values['subscription.siteid'] = $this->getContext()->getLocale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MW\Criteria\Iface Criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return $this->createSearchBase( 'subscription' );
		}

		return parent::createSearch();
	}


	/**
	 * Creates a one-time subscription in the storage from the given invoice object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Subscription item with necessary values
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		self::checkClass( '\\Aimeos\\MShop\\Subscription\\Item\\Iface', $item );

		if( $item->getOrderProductId() === null ) {
			throw new \Aimeos\MShop\Subscription\Exception( 'Required order product ID is missing' );
		}

		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );

			if( $id === null )
			{
				/** mshop/subscription/manager/standard/insert/mysql
				 * Inserts a new subscription record into the database table
				 *
				 * @see mshop/subscription/manager/standard/insert/ansi
				 */

				/** mshop/subscription/manager/standard/insert/ansi
				 * Inserts a new subscription record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the subscription item to the statement before they are
				 * sent to the database server. The number of question marks must
				 * be the same as the number of columns listed in the INSERT
				 * statement. The subscription of the columns must correspond to the
				 * subscription in the saveItems() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2018.04
				 * @category Developer
				 * @see mshop/subscription/manager/standard/update/ansi
				 * @see mshop/subscription/manager/standard/newid/ansi
				 * @see mshop/subscription/manager/standard/delete/ansi
				 * @see mshop/subscription/manager/standard/search/ansi
				 * @see mshop/subscription/manager/standard/count/ansi
				 */
				$path = 'mshop/subscription/manager/standard/insert';
			}
			else
			{
				/** mshop/subscription/manager/standard/update/mysql
				 * Updates an existing subscription record in the database
				 *
				 * @see mshop/subscription/manager/standard/update/ansi
				 */

				/** mshop/subscription/manager/standard/update/ansi
				 * Updates an existing subscription record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the subscription item to the statement before they are
				 * sent to the database server. The subscription of the columns must
				 * correspond to the subscription in the saveItems() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2018.04
				 * @category Developer
				 * @see mshop/subscription/manager/standard/insert/ansi
				 * @see mshop/subscription/manager/standard/newid/ansi
				 * @see mshop/subscription/manager/standard/delete/ansi
				 * @see mshop/subscription/manager/standard/search/ansi
				 * @see mshop/subscription/manager/standard/count/ansi
				 */
				$path = 'mshop/subscription/manager/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );

			$stmt->bind( 1, $item->getOrderBaseId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getOrderProductId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 3, $item->getDateNext() );
			$stmt->bind( 4, $item->getDateEnd() );
			$stmt->bind( 5, $item->getInterval() );
			$stmt->bind( 6, $item->getReason(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 7, $item->getStatus(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 8, $date ); // mtime
			$stmt->bind( 9, $context->getEditor() );
			$stmt->bind( 10, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );

			if( $id !== null ) {
				$stmt->bind( 11, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id ); // is not modified anymore
			} else {
				$stmt->bind( 11, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/subscription/manager/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/subscription/manager/standard/newid/ansi
				 */

				/** mshop/subscription/manager/standard/newid/ansi
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
				 *  SELECT currval('seq_msub_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_msub_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2018.04
				 * @category Developer
				 * @see mshop/subscription/manager/standard/insert/ansi
				 * @see mshop/subscription/manager/standard/update/ansi
				 * @see mshop/subscription/manager/standard/delete/ansi
				 * @see mshop/subscription/manager/standard/search/ansi
				 * @see mshop/subscription/manager/standard/count/ansi
				 */
				$path = 'mshop/subscription/manager/standard/newid';
				$item->setId( $this->newId( $conn, $path ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $item;
	}


	/**
	 * Returns an subscription invoice item built from database values.
	 *
	 * @param integer $id Unique id of the subscription invoice
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Subscription\Item\Iface Returns subscription invoice item of the given id
	 * @throws \Aimeos\MShop\Subscription\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'subscription.id', $id, $ref, $default );
	}


	/**
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/subscription/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/subscription/manager/standard/delete/ansi
		 */

		/** mshop/subscription/manager/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the subscription database.
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
		 * @since 2018.04
		 * @category Developer
		 * @see mshop/subscription/manager/standard/insert/ansi
		 * @see mshop/subscription/manager/standard/update/ansi
		 * @see mshop/subscription/manager/standard/newid/ansi
		 * @see mshop/subscription/manager/standard/search/ansi
		 * @see mshop/subscription/manager/standard/count/ansi
		 */
		$path = 'mshop/subscription/manager/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/subscription/manager/submanagers';

		return $this->getResourceTypeBase( 'subscription', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/subscription/manager/submanagers
		 * List of manager names that can be instantiated by the subscription manager
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
		 * @since 2018.04
		 * @category Developer
		 */
		$path = 'mshop/subscription/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, ['base'], $withsub );
	}


	/**
	 * Searches for subscriptions based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Subscription\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
	{
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		$items = [];

		try
		{
			$required = array( 'subscription', 'order.base' );

			/** mshop/subscription/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole subscription domain if items from parent sites are inherited,
			 * sites from child sites are aggregated or both.
			 *
			 * Available constants for the site mode are:
			 * * 0 = only items from the current site
			 * * 1 = inherit items from parent sites
			 * * 2 = aggregate items from child sites
			 * * 3 = inherit and aggregate items at the same time
			 *
			 * You also need to set the mode in the locale manager
			 * (mshop/locale/manager/standard/sitelevel) to one of the constants.
			 * If you set it to the same value, it will work as described but you
			 * can also use different modes. For example, if inheritance and
			 * aggregation is configured the locale manager but only inheritance
			 * in the domain manager because aggregating items makes no sense in
			 * this domain, then items wil be only inherited. Thus, you have full
			 * control over inheritance and aggregation in each domain.
			 *
			 * @param integer Constant from Aimeos\MShop\Locale\Manager\Base class
			 * @category Developer
			 * @since 2018.04
			 * @see mshop/locale/manager/standard/sitelevel
			 */
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;
			$level = $context->getConfig()->get( 'mshop/subscription/manager/sitemode', $level );

			/** mshop/subscription/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/subscription/manager/standard/search/ansi
			 */

			/** mshop/subscription/manager/standard/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the subscription
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
			 * If the records that are retrieved should be subscriptioned by one or more
			 * columns, the generated string of column / sort direction pairs
			 * replaces the ":subscription" placeholder. In case no subscriptioning is required,
			 * the complete ORDER BY part including the "\/*-subscriptionby*\/...\/*subscriptionby-*\/"
			 * markers is removed to speed up retrieving the records. Columns of
			 * sub-managers can also be used for subscriptioning the result set but then
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
			 * @since 2018.04
			 * @category Developer
			 * @see mshop/subscription/manager/standard/insert/ansi
			 * @see mshop/subscription/manager/standard/update/ansi
			 * @see mshop/subscription/manager/standard/newid/ansi
			 * @see mshop/subscription/manager/standard/delete/ansi
			 * @see mshop/subscription/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/subscription/manager/standard/search';

			/** mshop/subscription/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/subscription/manager/standard/count/ansi
			 */

			/** mshop/subscription/manager/standard/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the subscription
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
			 * @since 2018.04
			 * @category Developer
			 * @see mshop/subscription/manager/standard/insert/ansi
			 * @see mshop/subscription/manager/standard/update/ansi
			 * @see mshop/subscription/manager/standard/newid/ansi
			 * @see mshop/subscription/manager/standard/delete/ansi
			 * @see mshop/subscription/manager/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/subscription/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount,
				$required, $total, $level );

			try
			{
				while( ( $row = $results->fetch() ) !== false ) {
					$items[$row['subscription.id']] = $this->createItemBase( $row );
				}
			}
			catch( \Exception $e )
			{
				$results->finish();
				throw $e;
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns a new manager for subscription extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g base, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'order', $manager, $name );
	}


	/**
	 * Creates a new subscription item.
	 *
	 * @param array $values List of attributes for subscription item
	 * @return \Aimeos\MShop\Subscription\Item\Iface New subscription item
	 */
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MShop\Subscription\Item\Standard( $values );
	}
}
