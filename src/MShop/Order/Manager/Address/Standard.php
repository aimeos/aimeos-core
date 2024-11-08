<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Address;


/**
 * Default order address manager implementation.
 *
 * @package MShop
 * @subpackage Order
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Order\Manager\Address\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private array $searchConfig = [
		'order.address.parentid' => [
			'label' => 'Order ID',
			'internalcode' => 'parentid',
			'type' => 'int',
			'public' => false,
		],
		'order.address.addressid' => [
			'label' => 'Customer address ID',
			'internalcode' => 'addrid',
			'public' => false,
		],
		'order.address.type' => [
			'label' => 'Address type',
			'internalcode' => 'type',
		],
		'order.address.company' => [
			'label' => 'Address company',
			'internalcode' => 'company',
		],
		'order.address.vatid' => [
			'label' => 'Address Vat ID',
			'internalcode' => 'vatid',
		],
		'order.address.salutation' => [
			'label' => 'Address salutation',
			'internalcode' => 'salutation',
		],
		'order.address.title' => [
			'label' => 'Address title',
			'internalcode' => 'title',
		],
		'order.address.firstname' => [
			'label' => 'Address firstname',
			'internalcode' => 'firstname',
		],
		'order.address.lastname' => [
			'label' => 'Address lastname',
			'internalcode' => 'lastname',
		],
		'order.address.address1' => [
			'label' => 'Address part one',
			'internalcode' => 'address1',
		],
		'order.address.address2' => [
			'label' => 'Address part two',
			'internalcode' => 'address2',
		],
		'order.address.address3' => [
			'label' => 'Address part three',
			'internalcode' => 'address3',
		],
		'order.address.postal' => [
			'label' => 'Address postal',
			'internalcode' => 'postal',
		],
		'order.address.city' => [
			'label' => 'Address city',
			'internalcode' => 'city',
		],
		'order.address.state' => [
			'label' => 'Address state',
			'internalcode' => 'state',
		],
		'order.address.countryid' => [
			'label' => 'Address country ID',
			'internalcode' => 'countryid',
		],
		'order.address.languageid' => [
			'label' => 'Address language ID',
			'internalcode' => 'langid',
		],
		'order.address.telephone' => [
			'label' => 'Address telephone',
			'internalcode' => 'telephone',
		],
		'order.address.telefax' => [
			'label' => 'Address telefax',
			'internalcode' => 'telefax',
		],
		'order.address.mobile' => [
			'label' => 'Address mobile number',
			'internalcode' => 'mobile',
		],
		'order.address.email' => [
			'label' => 'Address email',
			'internalcode' => 'email',
		],
		'order.address.website' => [
			'label' => 'Address website',
			'internalcode' => 'website',
		],
		'order.address.birthday' => [
			'label' => 'Address birthday',
			'internalcode' => 'birthday',
			'type' => 'date',
		],
		'order.address.longitude' => [
			'label' => 'Address longitude',
			'internalcode' => 'longitude',
			'public' => false,
		],
		'order.address.latitude' => [
			'label' => 'Address latitude',
			'internalcode' => 'latitude',
			'public' => false,
		],
		'order.address.position' => [
			'label' => 'Address position',
			'internalcode' => 'pos',
			'type' => 'int',
			'public' => false,
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
		/** mshop/order/manager/address/aggregate/mysql
		 * Counts the number of records grouped by the values in the key column and matched by the given criteria
		 *
		 * @see mshop/order/manager/address/aggregate/ansi
		 */

		/** mshop/order/manager/address/aggregate/ansi
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
		 * @see mshop/order/manager/address/insert/ansi
		 * @see mshop/order/manager/address/update/ansi
		 * @see mshop/order/manager/address/newid/ansi
		 * @see mshop/order/manager/address/delete/ansi
		 * @see mshop/order/manager/address/search/ansi
		 * @see mshop/order/manager/address/count/ansi
		 */
		$cfgkey = 'mshop/order/manager/address/aggregate';
		return $this->aggregateBase( $search, $key, $cfgkey, ['order.address'], $value, $type );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Address\Iface New order address item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['order.address.siteid'] = $values['order.address.siteid'] ?? $this->context()->locale()->getSiteId();
		return new \Aimeos\MShop\Order\Item\Address\Standard( 'order.address.', $values );
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
		return parent::filter( $default )->order( 'order.address.position' );
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
			'order.address.id' => [
				'label' => 'Order address ID',
				'internalcode' => 'id',
				'internaldeps' => ['LEFT JOIN "mshop_order_address" AS mordad ON ( mord."id" = mordad."parentid" )'],
				'type' => 'int',
				'public' => false,
			]
		] ) );
	}


	/**
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function prefix() : string
	{
		return 'order.address.';
	}


	/** mshop/order/manager/address/delete/mysql
	 * Deletes the items matched by the given IDs from the database
	 *
	 * @see mshop/order/manager/address/delete/ansi
	 */

	/** mshop/order/manager/address/delete/ansi
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
	 * @see mshop/order/manager/address/insert/ansi
	 * @see mshop/order/manager/address/update/ansi
	 * @see mshop/order/manager/address/newid/ansi
	 * @see mshop/order/manager/address/search/ansi
	 * @see mshop/order/manager/address/count/ansi
	 */

	/** mshop/order/manager/address/submanagers
	 * List of manager names that can be instantiated by the order base address manager
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

	/** mshop/order/manager/address/name
	 * Class name of the used order base address manager implementation
	 *
	 * Each default order base address manager can be replaced by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Order\Manager\Address\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Order\Manager\Address\Myaddress
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/order/manager/address/name = Myaddress
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
	 * @since 2015.10
	 */

	/** mshop/order/manager/address/decorators/excludes
	 * Excludes decorators added by the "common" option from the order base address manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the order base address manager.
	 *
	 *  mshop/order/manager/address/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the order base address manager.
	 *
	 * @param array List of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/address/decorators/global
	 * @see mshop/order/manager/address/decorators/local
	 */

	/** mshop/order/manager/address/decorators/global
	 * Adds a list of globally available decorators only to the order base address manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the order base
	 * address manager.
	 *
	 *  mshop/order/manager/address/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the order base
	 * address manager.
	 *
	 * @param array List of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/address/decorators/excludes
	 * @see mshop/order/manager/address/decorators/local
	 */

	/** mshop/order/manager/address/decorators/local
	 * Adds a list of local decorators only to the order base address manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Order\Manager\Address\Decorator\*") around the
	 * order base address manager.
	 *
	 *  mshop/order/manager/address/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Order\Manager\Address\Decorator\Decorator2" only
	 * to the order base address manager.
	 *
	 * @param array List of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/order/manager/address/decorators/excludes
	 * @see mshop/order/manager/address/decorators/global
	 */

	/** mshop/order/manager/address/insert/mysql
	 * Inserts a new order record into the database table
	 *
	 * @see mshop/order/manager/address/insert/ansi
	 */

	/** mshop/order/manager/address/insert/ansi
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
	 * @see mshop/order/manager/address/update/ansi
	 * @see mshop/order/manager/address/newid/ansi
	 * @see mshop/order/manager/address/delete/ansi
	 * @see mshop/order/manager/address/search/ansi
	 * @see mshop/order/manager/address/count/ansi
	 */

	/** mshop/order/manager/address/update/mysql
	 * Updates an existing order record in the database
	 *
	 * @see mshop/order/manager/address/update/ansi
	 */

	/** mshop/order/manager/address/update/ansi
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
	 * @see mshop/order/manager/address/insert/ansi
	 * @see mshop/order/manager/address/newid/ansi
	 * @see mshop/order/manager/address/delete/ansi
	 * @see mshop/order/manager/address/search/ansi
	 * @see mshop/order/manager/address/count/ansi
	 */

	/** mshop/order/manager/address/newid/mysql
	 * Retrieves the ID generated by the database when inserting a new record
	 *
	 * @see mshop/order/manager/address/newid/ansi
	 */

	/** mshop/order/manager/address/newid/ansi
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
	 * @see mshop/order/manager/address/insert/ansi
	 * @see mshop/order/manager/address/update/ansi
	 * @see mshop/order/manager/address/delete/ansi
	 * @see mshop/order/manager/address/search/ansi
	 * @see mshop/order/manager/address/count/ansi
	 */

	/** mshop/order/manager/address/search/mysql
	 * Retrieves the records matched by the given criteria in the database
	 *
	 * @see mshop/order/manager/address/search/ansi
	 */

	/** mshop/order/manager/address/search/ansi
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
	 * @see mshop/order/manager/address/insert/ansi
	 * @see mshop/order/manager/address/update/ansi
	 * @see mshop/order/manager/address/newid/ansi
	 * @see mshop/order/manager/address/delete/ansi
	 * @see mshop/order/manager/address/count/ansi
	 */

	/** mshop/order/manager/address/count/mysql
	 * Counts the number of records matched by the given criteria in the database
	 *
	 * @see mshop/order/manager/address/count/ansi
	 */

	/** mshop/order/manager/address/count/ansi
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
	 * @see mshop/order/manager/address/insert/ansi
	 * @see mshop/order/manager/address/update/ansi
	 * @see mshop/order/manager/address/newid/ansi
	 * @see mshop/order/manager/address/delete/ansi
	 * @see mshop/order/manager/address/search/ansi
	 */
}
