<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MShop
 * @subpackage Group
 */


namespace Aimeos\MShop\Group\Manager;


/**
 * Default implementation of the group manager
 *
 * @package MShop
 * @subpackage Group
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Group\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private array $searchConfig = array(
		'group.id' => array(
			'code' => 'group.id',
			'internalcode' => 'mgro."id"',
			'label' => 'Group ID',
			'type' => 'int',
			'public' => false,
		),
		'group.siteid' => array(
			'code' => 'group.siteid',
			'internalcode' => 'mgro."siteid"',
			'label' => 'Group site ID',
			'public' => false,
		),
		'group.code' => array(
			'code' => 'group.code',
			'internalcode' => 'mgro."code"',
			'label' => 'Group code',
		),
		'group.label' => array(
			'code' => 'group.label',
			'internalcode' => 'mgro."label"',
			'label' => 'Group label',
		),
		'group.ctime' => array(
			'code' => 'group.ctime',
			'internalcode' => 'mgro."ctime"',
			'label' => 'Group create date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'group.mtime' => array(
			'code' => 'group.mtime',
			'internalcode' => 'mgro."mtime"',
			'label' => 'Group modify date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'group.editor' => array(
			'code' => 'group.editor',
			'internalcode' => 'mgro."editor"',
			'label' => 'Group editor',
			'public' => false,
		),
	);


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		/** mshop/group/manager/resource
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
		$this->setResourceName( $context->config()->get( 'mshop/group/manager/resource', 'db-group' ) );
	}


	/**
	 * Removes old entries from the database
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Group\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/group/manager/submanagers';
		foreach( $this->context()->config()->get( $path, [] ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/group/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Group\Item\Iface New group item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['group.siteid'] = $values['group.siteid'] ?? $this->context()->locale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/group/manager/submanagers';
		return $this->getResourceTypeBase( 'group', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching
	 *
	 * @param bool $withsub Return attributes of sub-managers too if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/group/manager/submanagers
		 * List of manager names that can be instantiated by the group manager
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
		 * @since 2015.08
		 * @category Developer
		 */
		$path = 'mshop/group/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Group\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/group/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/group/manager/delete/ansi
		 */

		/** mshop/group/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the group
		 * database. The records must be from the site that is configured via the
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
		 * @since 2015.08
		 * @category Developer
		 * @see mshop/group/manager/insert/ansi
		 * @see mshop/group/manager/update/ansi
		 * @see mshop/group/manager/newid/ansi
		 * @see mshop/group/manager/search/ansi
		 * @see mshop/group/manager/count/ansi
		 */
		$path = 'mshop/group/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function find( string $code, array $ref = [], string $domain = null, string $type = null,
		?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->findBase( array( 'group.code' => $code ), $ref, $default );
	}


	/**
	 * Returns the group item object specificed by its ID
	 *
	 * @param string $id Unique group ID referencing an existing group
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Group\Item\Iface Returns the group item for the given ID
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'group.id', $id, $ref, $default );
	}


	/**
	 * Inserts a new or updates an existing group item
	 *
	 * @param \Aimeos\MShop\Group\Item\Iface $item Group group item
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Group\Item\Iface $item Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Group\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Group\Item\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$columns = $this->object()->getSaveAttributes();

		if( $id === null )
		{
			/** mshop/group/manager/insert/mysql
			 * Inserts a new group record into the database table
			 *
			 * @see mshop/group/manager/insert/ansi
			 */

			/** mshop/group/manager/insert/ansi
			 * Inserts a new group record into the database table
			 *
			 * Items with no ID yet (i.e. the ID is NULL) will be created in
			 * the database and the newly created ID retrieved afterwards
			 * using the "newid" SQL statement.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the group item to the statement before
			 * they are sent to the database server. The number of question
			 * marks must be the same as the number of columns listed in the
			 * INSERT statement. The order of the columns must correspond to
			 * the order in the save() method, so the correct values are
			 * bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for inserting records
			 * @since 2015.08
			 * @category Developer
			 * @see mshop/group/manager/update/ansi
			 * @see mshop/group/manager/newid/ansi
			 * @see mshop/group/manager/delete/ansi
			 * @see mshop/group/manager/search/ansi
			 * @see mshop/group/manager/count/ansi
			 */
			$path = 'mshop/group/manager/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			/** mshop/group/manager/update/mysql
			 * Updates an existing group record in the database
			 *
			 * @see mshop/group/manager/update/ansi
			 */

			/** mshop/group/manager/update/ansi
			 * Updates an existing group record in the database
			 *
			 * Items which already have an ID (i.e. the ID is not NULL) will
			 * be updated in the database.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the group item to the statement before
			 * they are sent to the database server. The order of the columns
			 * must correspond to the order in the save() method, so the
			 * correct values are bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for updating records
			 * @since 2015.08
			 * @category Developer
			 * @see mshop/group/manager/insert/ansi
			 * @see mshop/group/manager/newid/ansi
			 * @see mshop/group/manager/delete/ansi
			 * @see mshop/group/manager/search/ansi
			 * @see mshop/group/manager/count/ansi
			 */
			$path = 'mshop/group/manager/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		$stmt->bind( $idx++, $item->getCode() );
		$stmt->bind( $idx++, $item->getLabel() );
		$stmt->bind( $idx++, $context->datetime() ); // mtime
		$stmt->bind( $idx++, $context->editor() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx++, $context->datetime() ); // ctime
		}

		$stmt->execute()->finish();

		if( $id === null && $fetch === true )
		{
			/** mshop/group/manager/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see mshop/group/manager/newid/ansi
			 */

			/** mshop/group/manager/newid/ansi
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
			 *  SELECT currval('seq_mcus_id')
			 * For SQL Server:
			 *  SELECT SCOPE_IDENTITY()
			 * For Oracle:
			 *  SELECT "seq_mcus_id".CURRVAL FROM DUAL
			 *
			 * There's no way to retrive the new ID by a SQL statements that
			 * fits for most database servers as they implement their own
			 * specific way.
			 *
			 * @param string SQL statement for retrieving the last inserted record ID
			 * @since 2015.08
			 * @category Developer
			 * @see mshop/group/manager/insert/ansi
			 * @see mshop/group/manager/update/ansi
			 * @see mshop/group/manager/delete/ansi
			 * @see mshop/group/manager/search/ansi
			 * @see mshop/group/manager/count/ansi
			 */
			$path = 'mshop/group/manager/newid';
			$id = $this->newId( $conn, $path );
		}

		return $item->setId( $id );
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Group\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$required = array( 'group' );
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;

		/** mshop/group/manager/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see mshop/group/manager/search/ansi
		 */

		/** mshop/group/manager/search/ansi
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * Fetches the records matched by the given criteria from the group
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
		 * @since 2015.08
		 * @category Developer
		 * @see mshop/group/manager/insert/ansi
		 * @see mshop/group/manager/update/ansi
		 * @see mshop/group/manager/newid/ansi
		 * @see mshop/group/manager/delete/ansi
		 * @see mshop/group/manager/count/ansi
		 */
		$cfgPathSearch = 'mshop/group/manager/search';

		/** mshop/group/manager/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see mshop/group/manager/count/ansi
		 */

		/** mshop/group/manager/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the group
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
		 * @since 2015.08
		 * @category Developer
		 * @see mshop/group/manager/insert/ansi
		 * @see mshop/group/manager/update/ansi
		 * @see mshop/group/manager/newid/ansi
		 * @see mshop/group/manager/delete/ansi
		 * @see mshop/group/manager/search/ansi
		 */
		$cfgPathCount = 'mshop/group/manager/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

		while( $row = $results->fetch() )
		{
			if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
				$items[$row['group.id']] = $item;
			}
		}

		return map( $items );
	}


	/**
	 * Returns a new manager for group extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/group/manager/name
		 * Class name of the used group manager implementation
		 *
		 * Each default group manager can be replaced by an alternative
		 * imlementation. To use this implementation, you have to set the last
		 * part of the class name as configuration value so the manager factory
		 * knows which class it has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Group\Manager\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Group\Manager\Mygroup
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/group/manager/name = Mygroup
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyGroup"!
		 *
		 * @param string Last part of the class name
		 * @since 2015.08
		 * @category Developer
		 */

		/** mshop/group/manager/decorators/excludes
		 * Excludes decorators added by the "common" option from the group manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/decorators/default" before they are wrapped
		 * around the group manager.
		 *
		 *  mshop/group/manager/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the group manager.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/group/manager/decorators/global
		 * @see mshop/group/manager/decorators/local
		 */

		/** mshop/group/manager/decorators/global
		 * Adds a list of globally available decorators only to the group manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the group manager.
		 *
		 *  mshop/group/manager/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the group
		 * group manager.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/group/manager/decorators/excludes
		 * @see mshop/group/manager/decorators/local
		 */

		/** mshop/group/manager/decorators/local
		 * Adds a list of local decorators only to the group manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Group\Manager\Decorator\*") around the group
		 * group manager.
		 *
		 *  mshop/group/manager/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Group\Manager\Decorator\Decorator2" only to the
		 * group manager.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/group/manager/decorators/excludes
		 * @see mshop/group/manager/decorators/global
		 */

		return $this->getSubManagerBase( 'group', $manager, $name );
	}


	/**
	 * Creates a new group item
	 *
	 * @param array $values List of attributes for group item
	 * @return \Aimeos\MShop\Group\Item\Iface New group item
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MShop\Group\Item\Iface
	{
		return new \Aimeos\MShop\Group\Item\Standard( $values );
	}
}
