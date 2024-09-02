<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Manager\Address;


/**
 * Implementation for customer address manager.
 *
 * @package MShop
 * @subpackage Customer
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Address\Base
	implements \Aimeos\MShop\Customer\Manager\Address\Iface
{
	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Customer\Manager\Address\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/customer/manager/address/submanagers';
		foreach( $this->context()->config()->get( $path, [] ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/customer/manager/address/clear' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Order\Item\Address\Iface New order address item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['customer.address.siteid'] = $values['customer.address.siteid'] ?? $this->context()->locale()->getSiteId();
		return new \Aimeos\MShop\Customer\Item\Address\Standard( 'customer.address.', $values );
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
			'customer.address.id' => [
				'label' => 'Customer address ID',
				'internalcode' => 'id',
				'internaldeps' => ['LEFT JOIN "mshop_customer_address" AS mcusad ON ( mcus."id" = mcusad."parentid" )'],
				'type' => 'int',
				'public' => false,
			]
		] ) );
	}


	/**
	 * Deletes items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface|\Aimeos\Map|array|string $items List of item objects or IDs of the items
	 * @param string $cfgpath Configuration path to the SQL statement
	 * @param bool $siteid If siteid should be used in the statement
	 * @param string $name Name of the ID column
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object for chaining method calls
	 */
	protected function deleteItemsBase( $items, string $cfgpath, bool $siteid = true,
		string $name = 'id' ) : \Aimeos\MShop\Common\Manager\Iface
	{
		if( map( $items )->isEmpty() ) {
			return $this;
		}

		$search = $this->object()->filter();
		$search->setConditions( $search->compare( '==', $name, $items ) );

		$types = array( $name => \Aimeos\Base\DB\Statement\Base::PARAM_STR );
		$translations = array( $name => '"' . $name . '"' );

		$cond = $search->getConditionSource( $types, $translations );
		$sql = str_replace( ':cond', $cond, $this->getSqlConfig( $cfgpath ) );

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$stmt = $conn->create( $sql );

		if( $siteid )
		{
			$stmt->bind( 1, $context->locale()->getSiteId() . '%' );
			$stmt->bind( 2, $context->user()?->getSiteId() );
		}

		$stmt->execute()->finish();

		return $this;
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function getConfigPath() : string
	{
		return 'mshop/customer/manager/address/';
	}


	/**
	 * Returns the prefix for the item properties and search keys.
	 *
	 * @return string Prefix for the item properties and search keys
	 */
	protected function prefix() : string
	{
		return 'customer.address.';
	}


	/**
	 * Saves a common address item object.
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item common address item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Address\Iface $item Updated item including the generated ID
	 */
	protected function saveBase( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$columns = array_column( $this->object()->getSaveAttributes(), null, 'internalcode' );

		if( $id === null )
		{
			$path = 'mshop/customer/manager/address/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			$path = 'mshop/customer/manager/address/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$values = $item->toArray( true );
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $this->object()->getSaveAttributes() as $name => $entry )
		{
			$value = $values[$entry->getCode()] ?? null;
			$value = $entry->getType() === 'json' ? json_encode( $value, JSON_FORCE_OBJECT ) : $value;
			$stmt->bind( $idx++, $value, \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		$stmt->bind( $idx++, $context->datetime() ); //mtime
		$stmt->bind( $idx++, $context->editor() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $context->user()?->getSiteId() );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx++, $context->datetime() ); // ctime
		}

		$stmt->execute()->finish();

		if( $id === null && $fetch === true ) {
			$id = $this->newId( $conn, 'mshop/common/manager/newid' );
		}

		return $item->setId( $id );
	}


	/** mshop/customer/manager/address/name
	 * Class name of the used customer address manager implementation
	 *
	 * Each default customer address manager can be replaced by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Customer\Manager\Address\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Customer\Manager\Address\Myaddress
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/customer/manager/address/name = Myaddress
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

	/** mshop/customer/manager/address/decorators/excludes
	 * Excludes decorators added by the "common" option from the customer address manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the customer address manager.
	 *
	 *  mshop/customer/manager/address/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the customer address manager.
	 *
	 * @param array Address of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/customer/manager/address/decorators/global
	 * @see mshop/customer/manager/address/decorators/local
	 */

	/** mshop/customer/manager/address/decorators/global
	 * Adds a list of globally available decorators only to the customer address manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the customer address manager.
	 *
	 *  mshop/customer/manager/address/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the customer
	 * address manager.
	 *
	 * @param array Address of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/customer/manager/address/decorators/excludes
	 * @see mshop/customer/manager/address/decorators/local
	 */

	/** mshop/customer/manager/address/decorators/local
	 * Adds a list of local decorators only to the customer address manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Customer\Manager\Address\Decorator\*") around the customer
	 * address manager.
	 *
	 *  mshop/customer/manager/address/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Customer\Manager\Address\Decorator\Decorator2" only to the
	 * customer address manager.
	 *
	 * @param array Address of decorator names
	 * @since 2015.10
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/customer/manager/address/decorators/excludes
	 * @see mshop/customer/manager/address/decorators/global
	 */

	/** mshop/customer/manager/address/submanagers
	 * List of manager names that can be instantiated by the customer address manager
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

	/** mshop/customer/manager/address/insert/mysql
	 * Inserts a new customer address record into the database table
	 *
	 * @see mshop/customer/manager/address/insert/ansi
	 */

	/** mshop/customer/manager/address/insert/ansi
	 * Inserts a new customer address record into the database table
	 *
	 * Items with no ID yet (i.e. the ID is NULL) will be created in
	 * the database and the newly created ID retrieved afterwards
	 * using the "newid" SQL statement.
	 *
	 * The SQL statement must be a string suitable for being used as
	 * prepared statement. It must include question marks for binding
	 * the values from the customer list item to the statement before they are
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
	 * @see mshop/customer/manager/address/update/ansi
	 * @see mshop/customer/manager/address/newid/ansi
	 * @see mshop/customer/manager/address/delete/ansi
	 * @see mshop/customer/manager/address/search/ansi
	 * @see mshop/customer/manager/address/count/ansi
	 */

	/** mshop/customer/manager/address/update/mysql
	 * Updates an existing customer address record in the database
	 *
	 * @see mshop/customer/manager/address/update/ansi
	 */

	/** mshop/customer/manager/address/update/ansi
	 * Updates an existing customer address record in the database
	 *
	 * Items which already have an ID (i.e. the ID is not NULL) will
	 * be updated in the database.
	 *
	 * The SQL statement must be a string suitable for being used as
	 * prepared statement. It must include question marks for binding
	 * the values from the customer list item to the statement before they are
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
	 * @see mshop/customer/manager/address/insert/ansi
	 * @see mshop/customer/manager/address/newid/ansi
	 * @see mshop/customer/manager/address/delete/ansi
	 * @see mshop/customer/manager/address/search/ansi
	 * @see mshop/customer/manager/address/count/ansi
	 */

	/** mshop/customer/manager/address/newid/mysql
	 * Retrieves the ID generated by the database when inserting a new record
	 *
	 * @see mshop/customer/manager/address/newid/ansi
	 */

	/** mshop/customer/manager/address/newid/ansi
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
	 *  SELECT currval('seq_mcusad_id')
	 * For SQL Server:
	 *  SELECT SCOPE_IDENTITY()
	 * For Oracle:
	 *  SELECT "seq_mcusad_id".CURRVAL FROM DUAL
	 *
	 * There's no way to retrive the new ID by a SQL statements that
	 * fits for most database servers as they implement their own
	 * specific way.
	 *
	 * @param string SQL statement for retrieving the last inserted record ID
	 * @since 2015.10
	 * @see mshop/customer/manager/address/insert/ansi
	 * @see mshop/customer/manager/address/update/ansi
	 * @see mshop/customer/manager/address/delete/ansi
	 * @see mshop/customer/manager/address/search/ansi
	 * @see mshop/customer/manager/address/count/ansi
	 */

	/** mshop/customer/manager/address/delete/mysql
	 * Deletes the items matched by the given IDs from the database
	 *
	 * @see mshop/customer/manager/address/delete/ansi
	 */

	/** mshop/customer/manager/address/delete/ansi
	 * Deletes the items matched by the given IDs from the database
	 *
	 * Removes the records specified by the given IDs from the customer database.
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
	 * @see mshop/customer/manager/address/insert/ansi
	 * @see mshop/customer/manager/address/update/ansi
	 * @see mshop/customer/manager/address/newid/ansi
	 * @see mshop/customer/manager/address/search/ansi
	 * @see mshop/customer/manager/address/count/ansi
	 */

	/** mshop/customer/manager/address/search/mysql
	 * Retrieves the records matched by the given criteria in the database
	 *
	 * @see mshop/customer/manager/address/search/ansi
	 */

	/** mshop/customer/manager/address/search/ansi
	 * Retrieves the records matched by the given criteria in the database
	 *
	 * Fetches the records matched by the given criteria from the customer
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
	 * @see mshop/customer/manager/address/insert/ansi
	 * @see mshop/customer/manager/address/update/ansi
	 * @see mshop/customer/manager/address/newid/ansi
	 * @see mshop/customer/manager/address/delete/ansi
	 * @see mshop/customer/manager/address/count/ansi
	 */

	/** mshop/customer/manager/address/count/mysql
	 * Counts the number of records matched by the given criteria in the database
	 *
	 * @see mshop/customer/manager/address/count/ansi
	 */

	/** mshop/customer/manager/address/count/ansi
	 * Counts the number of records matched by the given criteria in the database
	 *
	 * Counts all records matched by the given criteria from the customer
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
	 * @see mshop/customer/manager/address/insert/ansi
	 * @see mshop/customer/manager/address/update/ansi
	 * @see mshop/customer/manager/address/newid/ansi
	 * @see mshop/customer/manager/address/delete/ansi
	 * @see mshop/customer/manager/address/search/ansi
	 */
}
