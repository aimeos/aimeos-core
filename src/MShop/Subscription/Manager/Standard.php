<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2024
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
	private array $searchConfig = [
		'subscription.orderid' => [
			'label' => 'Order ID',
			'internalcode' => 'orderid',
			'type' => 'int',
			'public' => false,
		],
		'subscription.ordprodid' => [
			'label' => 'Order product ID',
			'internalcode' => 'ordprodid',
			'type' => 'int',
			'public' => false,
		],
		'subscription.datenext' => [
			'label' => 'Next renewal date/time',
			'internalcode' => 'next',
			'type' => 'datetime',
		],
		'subscription.dateend' => [
			'label' => 'End of subscription',
			'internalcode' => 'end',
			'type' => 'datetime',
		],
		'subscription.interval' => [
			'label' => 'Renewal interval',
			'internalcode' => 'interval',
		],
		'subscription.reason' => [
			'label' => 'Subscription end reason',
			'internalcode' => 'reason',
			'type' => 'int',
		],
		'subscription.period' => [
			'label' => 'Subscription period count',
			'internalcode' => 'period',
			'type' => 'int',
		],
		'subscription.productid' => [
			'label' => 'Subscription product ID',
			'internalcode' => 'productid',
		],
		'subscription.status' => [
			'label' => 'Subscription status',
			'internalcode' => 'status',
			'type' => 'int',
		],
	];


	/**
	 * Creates the manager that will use the given context object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object with required objects
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		/** mshop/subscription/manager/resource
		 * Name of the database connection resource to use
		 *
		 * You can configure a different database connection for each data domain
		 * and if no such connection name exists, the "db" connection will be used.
		 * It's also possible to use the same database connection for different
		 * data domains by configuring the same connection name using this setting.
		 *
		 * @param string Database connection name
		 * @since 2023.04
		 */
		$this->setResourceName( $context->config()->get( 'mshop/subscription/manager/resource', 'db-order' ) );
	}


	/**
	 * Counts the number items that are available for the values of the given key.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria
	 * @param array|string $key Search key or list of keys to aggregate items for
	 * @param string|null $value Search key for aggregating the value column
	 * @param string|null $type Type of the aggregation, empty string for count or "sum" or "avg" (average)
	 * @return \Aimeos\Map List of the search keys as key and the number of counted items as value
	 */
	public function aggregate( \Aimeos\Base\Criteria\Iface $search, $key, ?string $value = null, ?string $type = null ) : \Aimeos\Map
	{
		/** mshop/subscription/manager/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/subscription/manager/aggregate/ansi
		 */

		/** mshop/subscription/manager/aggregate/ansi
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
		 * @see mshop/subscription/manager/insert/ansi
		 * @see mshop/subscription/manager/update/ansi
		 * @see mshop/subscription/manager/newid/ansi
		 * @see mshop/subscription/manager/delete/ansi
		 * @see mshop/subscription/manager/search/ansi
		 * @see mshop/subscription/manager/count/ansi
		 */

		$cfgkey = 'mshop/subscription/manager/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['subscription'], $value, $type );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Subscription\Item\Iface New subscription item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['subscription.siteid'] = $values['subscription.siteid'] ?? $this->context()->locale()->getSiteId();
		return new \Aimeos\MShop\Subscription\Item\Standard( 'subscription.', $values );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		$filter = $this->filterBase( 'subscription', $default );

		if( $site )
		{
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
			$filter->add( $this->siteCondition( 'order.product.siteid', $level ) );
		}

		return $filter;
	}


	/**
	 * Returns the additional column/search definitions
	 *
	 * @return array Associative list of column names as keys and items implementing \Aimeos\Base\Criteria\Attribute\Iface
	 */
	public function getSaveAttributes() : array
	{
		return $this->createAttributes( $this->searchConfig );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = parent::getSearchAttributes( $withsub );

		if( $withsub ) {
			$list += \Aimeos\MShop::create( $this->context(), 'order' )->getSearchAttributes( $withsub );
		}

		return $list;
	}


	/**
	 * Searches for subscriptions based on the given criteria.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Subscription\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], ?int &$total = null ) : \Aimeos\Map
	{
		$context = $this->context();

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
		 * (mshop/locale/manager/sitelevel) to one of the constants.
		 * If you set it to the same value, it will work as described but you
		 * can also use different modes. For example, if inheritance and
		 * aggregation is configured the locale manager but only inheritance
		 * in the domain manager because aggregating items makes no sense in
		 * this domain, then items wil be only inherited. Thus, you have full
		 * control over inheritance and aggregation in each domain.
		 *
		 * @param int Constant from Aimeos\MShop\Locale\Manager\Base class
		 * @since 2018.04
		 * @see mshop/locale/manager/sitelevel
		 */
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;
		$level = $context->config()->get( 'mshop/subscription/manager/sitemode', $level );

		/** mshop/subscription/manager/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/subscription/manager/search/ansi
		 */

		/** mshop/subscription/manager/search/ansi
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
		 * If the records that are retrieved should be ordered by one or more
		 * columns, the generated string of column / sort direction pairs
		 * replaces the ":order" placeholder.
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
		 * @see mshop/subscription/manager/insert/ansi
		 * @see mshop/subscription/manager/update/ansi
		 * @see mshop/subscription/manager/newid/ansi
		 * @see mshop/subscription/manager/delete/ansi
		 * @see mshop/subscription/manager/count/ansi
		 */
		$cfgPathSearch = 'mshop/subscription/manager/search';

		/** mshop/subscription/manager/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/subscription/manager/count/ansi
		 */

		/** mshop/subscription/manager/count/ansi
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
		 * @see mshop/subscription/manager/insert/ansi
		 * @see mshop/subscription/manager/update/ansi
		 * @see mshop/subscription/manager/newid/ansi
		 * @see mshop/subscription/manager/delete/ansi
		 * @see mshop/subscription/manager/search/ansi
		 */
		$cfgPathCount = 'mshop/subscription/manager/count';

		$items = [];
		$required = ['subscription', 'order'];
		$conn = $context->db( $this->getResourceName() );
		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

		try
		{
			while( $row = $results->fetch() )
			{
				if( $item = $this->applyFilter( $this->create( $row ) ) ) {
					$items[$row['subscription.id']] = $item;
				}
			}
		}
		catch( \Exception $e )
		{
			$results->finish();
			throw $e;
		}


		if( in_array( 'order', $ref ) )
		{
			$ids = array_column( $items, 'subscription.orderid' );
			$manager = \Aimeos\MShop::create( $context, 'order' );
			$search = $manager->filter()->add( 'order.id', '==', $ids )->slice( 0, count( $ids ) );
			$orderItems = $manager->search( $search, $ref );

			foreach( $items as $item ) {
				$item->set( '.orderitem', $orderItems[$item['subscription.orderid']] ?? null );
			}
		}

		return map( $items );
	}


	/**
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function prefix() : string
	{
		return 'subscription.';
	}


	/**
	 * Creates a one-time subscription in the storage from the given invoice object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Subscription item with necessary values
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Subscription\Item\Iface Updated item including the generated ID
	 */
	protected function saveBase( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Subscription\Item\Iface
	{
		if( $item->getOrderProductId() === null ) {
			throw new \Aimeos\MShop\Subscription\Exception( 'Required order product ID is missing' );
		}

		if( $orderItem = $item->getOrderItem() ) {
			\Aimeos\MShop::create( $this->context(), 'order' )->save( $orderItem );
		}

		return parent::saveBase( $item, $fetch );
	}


	/** mshop/subscription/manager/name
	 * Class name of the used subscription manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Subscription\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Subscription\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/subscription/manager/name = Mymanager
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyManager"!
	 *
	 * @param string Last part of the class name
	 * @since 2018.04
	 */

	/** mshop/subscription/manager/decorators/excludes
	 * Excludes decorators added by the "common" option from the subscription manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the subscription manager.
	 *
	 *  mshop/subscription/manager/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the subscription manager.
	 *
	 * @param array List of decorator names
	 * @since 2018.04
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/subscription/manager/decorators/global
	 * @see mshop/subscription/manager/decorators/local
	 */

	/** mshop/subscription/manager/decorators/global
	 * Adds a list of globally available decorators only to the subscription manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the subscription manager.
	 *
	 *  mshop/subscription/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the subscription
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2018.04
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/subscription/manager/decorators/excludes
	 * @see mshop/subscription/manager/decorators/local
	 */

	/** mshop/subscription/manager/decorators/local
	 * Adds a list of local decorators only to the subscription manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Subscription\Manager\Decorator\*") around the subscription manager.
	 *
	 *  mshop/subscription/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Subscription\Manager\Decorator\Decorator2" only to the subscription
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2018.04
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/subscription/manager/decorators/excludes
	 * @see mshop/subscription/manager/decorators/global
	 */

	/** mshop/subscription/manager/delete/mysql
	 * Deletes the items matched by the given IDs from the database
	 *
	 * @see mshop/subscription/manager/delete/ansi
	 */

	/** mshop/subscription/manager/delete/ansi
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
	 * @see mshop/subscription/manager/insert/ansi
	 * @see mshop/subscription/manager/update/ansi
	 * @see mshop/subscription/manager/newid/ansi
	 * @see mshop/subscription/manager/search/ansi
	 * @see mshop/subscription/manager/count/ansi
	 */

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
	 */

	/** mshop/subscription/manager/insert/mysql
	 * Inserts a new subscription record into the database table
	 *
	 * @see mshop/subscription/manager/insert/ansi
	 */

	/** mshop/subscription/manager/insert/ansi
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
	 * statement. The catalog of the columns must correspond to the
	 * catalog in the save() method, so the correct values are
	 * bound to the columns.
	 *
	 * The SQL statement should conform to the ANSI standard to be
	 * compatible with most relational database systems. This also
	 * includes using double quotes for table and column names.
	 *
	 * @param string SQL statement for inserting records
	 * @since 2018.04
	 * @see mshop/subscription/manager/update/ansi
	 * @see mshop/subscription/manager/newid/ansi
	 * @see mshop/subscription/manager/delete/ansi
	 * @see mshop/subscription/manager/search/ansi
	 * @see mshop/subscription/manager/count/ansi
	 */

	/** mshop/subscription/manager/update/mysql
	 * Updates an existing subscription record in the database
	 *
	 * @see mshop/subscription/manager/update/ansi
	 */

	/** mshop/subscription/manager/update/ansi
	 * Updates an existing subscription record in the database
	 *
	 * Items which already have an ID (i.e. the ID is not NULL) will
	 * be updated in the database.
	 *
	 * The SQL statement must be a string suitable for being used as
	 * prepared statement. It must include question marks for binding
	 * the values from the subscription item to the statement before they are
	 * sent to the database server. The catalog of the columns must
	 * correspond to the catalog in the save() method, so the
	 * correct values are bound to the columns.
	 *
	 * The SQL statement should conform to the ANSI standard to be
	 * compatible with most relational database systems. This also
	 * includes using double quotes for table and column names.
	 *
	 * @param string SQL statement for updating records
	 * @since 2018.04
	 * @see mshop/subscription/manager/insert/ansi
	 * @see mshop/subscription/manager/newid/ansi
	 * @see mshop/subscription/manager/delete/ansi
	 * @see mshop/subscription/manager/search/ansi
	 * @see mshop/subscription/manager/count/ansi
	 */

	/** mshop/subscription/manager/newid/mysql
	 * Retrieves the ID generated by the database when inserting a new record
	 *
	 * @see mshop/subscription/manager/newid/ansi
	 */

	/** mshop/subscription/manager/newid/ansi
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
	 *  SELECT currval('seq_mrul_id')
	 * For SQL Server:
	 *  SELECT SCOPE_IDENTITY()
	 * For Oracle:
	 *  SELECT "seq_mrul_id".CURRVAL FROM DUAL
	 *
	 * There's no way to retrive the new ID by a SQL statements that
	 * fits for most database servers as they implement their own
	 * specific way.
	 *
	 * @param string SQL statement for retrieving the last inserted record ID
	 * @since 2018.04
	 * @see mshop/subscription/manager/insert/ansi
	 * @see mshop/subscription/manager/update/ansi
	 * @see mshop/subscription/manager/delete/ansi
	 * @see mshop/subscription/manager/search/ansi
	 * @see mshop/subscription/manager/count/ansi
	 */
}
