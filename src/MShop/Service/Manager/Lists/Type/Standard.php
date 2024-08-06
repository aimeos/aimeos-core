<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Manager\Lists\Type;


/**
 * Default service list type manager for creating and handling service list type items.
 * @package MShop
 * @subpackage Service
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Type\Base
	implements \Aimeos\MShop\Service\Manager\Lists\Type\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private array $searchConfig = array(
		'service.lists.type.id' => array(
			'code' => 'service.lists.type.id',
			'internalcode' => 'mserlity."id"',
			'label' => 'List type id',
			'type' => 'int',
			'public' => false,
		),
		'service.lists.type.siteid' => array(
			'code' => 'service.lists.type.siteid',
			'internalcode' => 'mserlity."siteid"',
			'label' => 'List type site id',
			'public' => false,
		),
		'service.lists.type.label' => array(
			'code' => 'service.lists.type.label',
			'internalcode' => 'mserlity."label"',
			'label' => 'List type label',
		),
		'service.lists.type.code' => array(
			'code' => 'service.lists.type.code',
			'internalcode' => 'mserlity."code"',
			'label' => 'List type code',
		),
		'service.lists.type.domain' => array(
			'code' => 'service.lists.type.domain',
			'internalcode' => 'mserlity."domain"',
			'label' => 'List type domain',
		),
		'service.lists.type.position' => array(
			'code' => 'service.lists.type.position',
			'internalcode' => 'mserlity."pos"',
			'label' => 'List type position',
			'type' => 'int',
		),
		'service.lists.type.status' => array(
			'code' => 'service.lists.type.status',
			'internalcode' => 'mserlity."status"',
			'label' => 'List type status',
			'type' => 'int',
		),
		'service.lists.type.ctime' => array(
			'code' => 'service.lists.type.ctime',
			'internalcode' => 'mserlity."ctime"',
			'label' => 'List type create date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'service.lists.type.mtime' => array(
			'code' => 'service.lists.type.mtime',
			'internalcode' => 'mserlity."mtime"',
			'label' => 'List type modify date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'service.lists.type.editor' => array(
			'code' => 'service.lists.type.editor',
			'internalcode' => 'mserlity."editor"',
			'label' => 'List type editor',
			'public' => false,
		),
		'service.lists.type.i18n' => array(
			'internalcode' => 'mserlity."i18n"',
			'label' => 'Type translation',
			'public' => false,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		/** mshop/service/manager/resource
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
		$this->setResourceName( $context->config()->get( 'mshop/service/manager/resource', 'db-service' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Service\Manager\Lists\Type\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/service/manager/lists/type/submanagers';
		foreach( $this->context()->config()->get( $path, [] ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/service/manager/lists/type/delete' );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/service/manager/lists/type/submanagers';
		return $this->getResourceTypeBase( 'service/lists/type', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/service/manager/lists/type/submanagers
		 * List of manager names that can be instantiated by the service list type manager
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
		 */
		$path = 'mshop/service/manager/lists/type/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for service list type extensions.
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/service/manager/lists/type/name
		 * Class name of the used service list type manager implementation
		 *
		 * Each default service list type manager can be replaced by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the manager factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Service\Manager\Lists\Type\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Service\Manager\Lists\Type\Mytype
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/service/manager/lists/type/name = Mytype
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyType"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.03
		 */

		/** mshop/service/manager/lists/type/decorators/excludes
		 * Excludes decorators added by the "common" option from the service list type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the service list type manager.
		 *
		 *  mshop/service/manager/lists/type/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the service list type manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/service/manager/lists/type/decorators/global
		 * @see mshop/service/manager/lists/type/decorators/local
		 */

		/** mshop/service/manager/lists/type/decorators/global
		 * Adds a list of globally available decorators only to the service list type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the service list
		 * type manager.
		 *
		 *  mshop/service/manager/lists/type/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the service
		 * list type manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/service/manager/lists/type/decorators/excludes
		 * @see mshop/service/manager/lists/type/decorators/local
		 */

		/** mshop/service/manager/lists/type/decorators/local
		 * Adds a list of local decorators only to the service list type manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Service\Manager\Lists\Type\Decorator\*") around the
		 * service list type manager.
		 *
		 *  mshop/service/manager/lists/type/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Service\Manager\Lists\Type\Decorator\Decorator2" only
		 * to the service list type manager.
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/service/manager/lists/type/decorators/excludes
		 * @see mshop/service/manager/lists/type/decorators/global
		 */

		return $this->getSubManagerBase( 'service', 'lists/type/' . $manager, $name );
	}


	/**
	 * Returns the config path for retrieving the configuration values.
	 *
	 * @return string Configuration path
	 */
	protected function getConfigPath() : string
	{
		/** mshop/service/manager/lists/type/insert/mysql
		 * Inserts a new service list type record into the database table
		 *
		 * @see mshop/service/manager/lists/type/insert/ansi
		 */

		/** mshop/service/manager/lists/type/insert/ansi
		 * Inserts a new service list type record into the database table
		 *
		 * Items with no ID yet (i.e. the ID is NULL) will be created in
		 * the database and the newly created ID retrieved afterwards
		 * using the "newid" SQL statement.
		 *
		 * The SQL statement must be a string suitable for being used as
		 * prepared statement. It must include question marks for binding
		 * the values from the service list type item to the statement before they are
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
		 * @since 2014.03
		 * @see mshop/service/manager/lists/type/update/ansi
		 * @see mshop/service/manager/lists/type/newid/ansi
		 * @see mshop/service/manager/lists/type/delete/ansi
		 * @see mshop/service/manager/lists/type/search/ansi
		 * @see mshop/service/manager/lists/type/count/ansi
		 */

		/** mshop/service/manager/lists/type/update/mysql
		 * Updates an existing service list type record in the database
		 *
		 * @see mshop/service/manager/lists/type/update/ansi
		 */

		/** mshop/service/manager/lists/type/update/ansi
		 * Updates an existing service list type record in the database
		 *
		 * Items which already have an ID (i.e. the ID is not NULL) will
		 * be updated in the database.
		 *
		 * The SQL statement must be a string suitable for being used as
		 * prepared statement. It must include question marks for binding
		 * the values from the service list type item to the statement before they are
		 * sent to the database server. The order of the columns must
		 * correspond to the order in the save() method, so the
		 * correct values are bound to the columns.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for updating records
		 * @since 2014.03
		 * @see mshop/service/manager/lists/type/insert/ansi
		 * @see mshop/service/manager/lists/type/newid/ansi
		 * @see mshop/service/manager/lists/type/delete/ansi
		 * @see mshop/service/manager/lists/type/search/ansi
		 * @see mshop/service/manager/lists/type/count/ansi
		 */

		/** mshop/service/manager/lists/type/newid/mysql
		 * Retrieves the ID generated by the database when inserting a new record
		 *
		 * @see mshop/service/manager/lists/type/newid/ansi
		 */

		/** mshop/service/manager/lists/type/newid/ansi
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
		 *  SELECT currval('seq_mserlity_id')
		 * For SQL Server:
		 *  SELECT SCOPE_IDENTITY()
		 * For Oracle:
		 *  SELECT "seq_mserlity_id".CURRVAL FROM DUAL
		 *
		 * There's no way to retrive the new ID by a SQL statements that
		 * fits for most database servers as they implement their own
		 * specific way.
		 *
		 * @param string SQL statement for retrieving the last inserted record ID
		 * @since 2014.03
		 * @see mshop/service/manager/lists/type/insert/ansi
		 * @see mshop/service/manager/lists/type/update/ansi
		 * @see mshop/service/manager/lists/type/delete/ansi
		 * @see mshop/service/manager/lists/type/search/ansi
		 * @see mshop/service/manager/lists/type/count/ansi
		 */

		/** mshop/service/manager/lists/type/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/service/manager/lists/type/delete/ansi
		 */

		/** mshop/service/manager/lists/type/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the service database.
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
		 * @see mshop/service/manager/lists/type/insert/ansi
		 * @see mshop/service/manager/lists/type/update/ansi
		 * @see mshop/service/manager/lists/type/newid/ansi
		 * @see mshop/service/manager/lists/type/search/ansi
		 * @see mshop/service/manager/lists/type/count/ansi
		 */

		/** mshop/service/manager/lists/type/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/service/manager/lists/type/search/ansi
		 */

		/** mshop/service/manager/lists/type/search/ansi
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * Fetches the records matched by the given criteria from the service
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
		 * @since 2014.03
		 * @see mshop/service/manager/lists/type/insert/ansi
		 * @see mshop/service/manager/lists/type/update/ansi
		 * @see mshop/service/manager/lists/type/newid/ansi
		 * @see mshop/service/manager/lists/type/delete/ansi
		 * @see mshop/service/manager/lists/type/count/ansi
		 */

		/** mshop/service/manager/lists/type/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/service/manager/lists/type/count/ansi
		 */

		/** mshop/service/manager/lists/type/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the service
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
		 * @see mshop/service/manager/lists/type/insert/ansi
		 * @see mshop/service/manager/lists/type/update/ansi
		 * @see mshop/service/manager/lists/type/newid/ansi
		 * @see mshop/service/manager/lists/type/delete/ansi
		 * @see mshop/service/manager/lists/type/search/ansi
		 */

		return 'mshop/service/manager/lists/type/';
	}


	/**
	 * Returns the search configuration for searching items.
	 *
	 * @return array Associative list of search keys and search definitions
	 */
	protected function getSearchConfig() : array
	{
		return $this->searchConfig;
	}
}
