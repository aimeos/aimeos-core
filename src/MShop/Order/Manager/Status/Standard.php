<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Order
 */

namespace Aimeos\MShop\Order\Manager\Status;


/**
 * Default implementation for order status manager.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Status\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private array $searchConfig = [
		'order.status.parentid' => [
			'label' => 'Status parent ID',
			'internalcode' => 'parentid',
			'type' => 'int',
			'public' => false,
		],
		'order.status.type' => [
			'label' => 'Status type',
			'internalcode' => 'type',
		],
		'order.status.value' => [
			'label' => 'Status value',
			'internalcode' => 'value',
		],
	];


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
		/** mshop/order/manager/status/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/status/aggregate/ansi
		 */

		/** mshop/order/manager/status/aggregate/ansi
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
		 * @since 2015.10
		 * @see mshop/order/manager/status/insert/ansi
		 * @see mshop/order/manager/status/update/ansi
		 * @see mshop/order/manager/status/newid/ansi
		 * @see mshop/order/manager/status/delete/ansi
		 * @see mshop/order/manager/status/search/ansi
		 * @see mshop/order/manager/status/count/ansi
		 */
		$cfgkey = 'mshop/order/manager/status/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['order.status'], $value, $type );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Status\Iface New order status item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['order.status.siteid'] = $values['order.status.siteid'] ?? $this->context()->locale()->getSiteId();
		return new \Aimeos\MShop\Order\Item\Status\Standard( 'order.status.', $values );
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
		return array_replace( parent::getSearchAttributes( $withsub ), $this->createAttributes( [
			'order.status.id' => [
				'label' => 'Order status ID',
				'internalcode' => 'id',
				'internaldeps' => ['LEFT JOIN "mshop_order_status" AS mordst ON ( mord."id" = mordst."parentid" )'],
				'type' => 'int',
				'public' => false,
			],
		] ) );
	}


	/**
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function prefix() : string
	{
		return 'order.status.';
	}


	/** mshop/order/manager/status/insert/mysql
	 * Inserts a new order record into the database table
	 *
	 * @see mshop/order/manager/status/insert/ansi
	 */

	/** mshop/order/manager/status/insert/ansi
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
	 * order in the save() method, so the correct values are
	 * bound to the columns.
	 *
	 * The SQL statement should conform to the ANSI standard to be
	 * compatible with most relational database systems. This also
	 * includes using double quotes for table and column names.
	 *
	 * @param string SQL statement for inserting records
	 * @since 2015.10
	 * @see mshop/order/manager/status/update/ansi
	 * @see mshop/order/manager/status/newid/ansi
	 * @see mshop/order/manager/status/delete/ansi
	 * @see mshop/order/manager/status/search/ansi
	 * @see mshop/order/manager/status/count/ansi
	 */

	/** mshop/order/manager/status/update/mysql
	 * Updates an existing order record in the database
	 *
	 * @see mshop/order/manager/status/update/ansi
	 */

	/** mshop/order/manager/status/update/ansi
	 * Updates an existing order record in the database
	 *
	 * Items which already have an ID (i.e. the ID is not NULL) will
	 * be updated in the database.
	 *
	 * The SQL statement must be a string suitable for being used as
	 * prepared statement. It must include question marks for binding
	 * the values from the order item to the statement before they are
	 * sent to the database server. The order of the columns must
	 * correspond to the order in the save() method, so the
	 * correct values are bound to the columns.
	 *
	 * The SQL statement should conform to the ANSI standard to be
	 * compatible with most relational database systems. This also
	 * includes using double quotes for table and column names.
	 *
	 * @param string SQL statement for updating records
	 * @since 2015.10
	 * @see mshop/order/manager/status/insert/ansi
	 * @see mshop/order/manager/status/newid/ansi
	 * @see mshop/order/manager/status/delete/ansi
	 * @see mshop/order/manager/status/search/ansi
	 * @see mshop/order/manager/status/count/ansi
	 */

	/** mshop/order/manager/status/newid/mysql
	 * Retrieves the ID generated by the database when inserting a new record
	 *
	 * @see mshop/order/manager/status/newid/ansi
	 */

	/** mshop/order/manager/status/newid/ansi
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
	 * @since 2015.10
	 * @see mshop/order/manager/status/insert/ansi
	 * @see mshop/order/manager/status/update/ansi
	 * @see mshop/order/manager/status/delete/ansi
	 * @see mshop/order/manager/status/search/ansi
	 * @see mshop/order/manager/status/count/ansi
	 */

	/** mshop/order/manager/status/delete/mysql
	 * Deletes the items matched by the given IDs from the database
	 *
	 * @see mshop/order/manager/status/delete/ansi
	 */

	/** mshop/order/manager/status/delete/ansi
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
	 * @since 2015.10
	 * @see mshop/order/manager/status/insert/ansi
	 * @see mshop/order/manager/status/update/ansi
	 * @see mshop/order/manager/status/newid/ansi
	 * @see mshop/order/manager/status/search/ansi
	 * @see mshop/order/manager/status/count/ansi
	 */

	/** mshop/order/manager/status/submanagers
	 * List of manager names that can be instantiated by the order status manager
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
	 * @since 2015.10
	 */

	/** mshop/order/manager/status/name
	 * Class name of the used order status manager implementation
	 *
	 * Each default order status manager can be replaced by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Order\Manager\Status\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Order\Manager\Status\Mystatus
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/order/manager/status/name = Mystatus
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyStatus"!
	 *
	 * @param string Last part of the class name
	 * @since 2015.10
	 */

	/** mshop/order/manager/status/decorators/excludes
	 * Excludes decorators added by the "common" option from the order status manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the order status manager.
	 *
	 *  mshop/order/manager/status/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the order status manager.
	 *
	 * @param array List of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/status/decorators/global
	 * @see mshop/order/manager/status/decorators/local
	 */

	/** mshop/order/manager/status/decorators/global
	 * Adds a list of globally available decorators only to the order status manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order status
	 * manager.
	 *
	 *  mshop/order/manager/status/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order
	 * status manager.
	 *
	 * @param array List of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/status/decorators/excludes
	 * @see mshop/order/manager/status/decorators/local
	 */

	/** mshop/order/manager/status/decorators/local
	 * Adds a list of local decorators only to the order status manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Order\Manager\Status\Decorator\*") around the order
	 * status manager.
	 *
	 *  mshop/order/manager/status/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Order\Manager\Status\Decorator\Decorator2" only to the
	 * order status manager.
	 *
	 * @param array List of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/status/decorators/excludes
	 * @see mshop/order/manager/status/decorators/global
	 */

	/** mshop/order/manager/status/search/mysql
	 * Retrieves the records matched by the given criteria in the database
	 *
	 * @see mshop/order/manager/status/search/ansi
	 */

	/** mshop/order/manager/status/search/ansi
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
	 * replaces the ":order" placeholder. Columns of
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
	 * @since 2015.10
	 * @see mshop/order/manager/status/insert/ansi
	 * @see mshop/order/manager/status/update/ansi
	 * @see mshop/order/manager/status/newid/ansi
	 * @see mshop/order/manager/status/delete/ansi
	 * @see mshop/order/manager/status/count/ansi
	 */

	/** mshop/order/manager/status/count/mysql
	 * Counts the number of records matched by the given criteria in the database
	 *
	 * @see mshop/order/manager/status/count/ansi
	 */

	/** mshop/order/manager/status/count/ansi
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
	 * @since 2015.10
	 * @see mshop/order/manager/status/insert/ansi
	 * @see mshop/order/manager/status/update/ansi
	 * @see mshop/order/manager/status/newid/ansi
	 * @see mshop/order/manager/status/delete/ansi
	 * @see mshop/order/manager/status/search/ansi
	 */
}
