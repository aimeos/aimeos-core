<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Customer
 */


namespace Aimeos\MShop\Customer\Manager\Group;


/**
 * Default implementation of the customer group manager
 *
 * @package MShop
 * @subpackage Customer
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Customer\Manager\Group\Iface
{
	private $searchConfig = array(
		'customer.group.id' => array(
			'code' => 'customer.group.id',
			'internalcode' => 'mcusgr."id"',
			'label' => 'Customer group ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		// no siteid
		'customer.group.code' => array(
			'code' => 'customer.group.code',
			'internalcode' => 'mcusgr."code"',
			'label' => 'Customer group code',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.group.label' => array(
			'code' => 'customer.group.label',
			'internalcode' => 'mcusgr."label"',
			'label' => 'Customer group label',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.group.ctime'=> array(
			'code' => 'customer.group.ctime',
			'internalcode' => 'mcusgr."ctime"',
			'label' => 'Customer group creation time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.group.mtime'=> array(
			'code' => 'customer.group.mtime',
			'internalcode' => 'mcusgr."mtime"',
			'label' => 'Customer group modification time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'customer.group.editor'=> array(
			'code' => 'customer.group.editor',
			'internalcode' => 'mcusgr."editor"',
			'label' => 'Customer group editor',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
	);


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-customer' );
	}


	/**
	 * Removes old entries from the database
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'mshop/customer/manager/group/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'mshop/customer/manager/group/standard/delete' );
	}


	/**
	 * Instantiates a new customer group item object
	 *
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface
	 */
	public function createItem()
	{
		$values = array( 'customer.group.siteid'=> $this->getContext()->getLocale()->getSiteId() );

		return $this->createItemBase( $values );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'mshop/customer/manager/group/submanagers';

		return $this->getResourceTypeBase( 'customer/group', $path, array(), $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching
	 *
	 * @param boolean $withsub Return attributes of sub-managers too if true
	 * @return array List of attribute items implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** mshop/customer/manager/group/submanagers
		 * List of manager names that can be instantiated by the customer group manager
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
		$path = 'mshop/customer/manager/group/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, array(), $withsub );
	}


	/**
	 * Removes multiple items specified by their IDs
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** mshop/customer/manager/group/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/customer/manager/group/standard/delete/ansi
		 */

		/** mshop/customer/manager/group/standard/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the customer group
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
		 * @see mshop/customer/manager/group/standard/insert/ansi
		 * @see mshop/customer/manager/group/standard/update/ansi
		 * @see mshop/customer/manager/group/standard/newid/ansi
		 * @see mshop/customer/manager/group/standard/search/ansi
		 * @see mshop/customer/manager/group/standard/count/ansi
		 */
		$path = 'mshop/customer/manager/group/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Returns the item specified by its code and domain/type if necessary
	 *
	 * @param string $code Code of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function findItem( $code, array $ref = array(), $domain = null, $type = null )
	{
		return $this->findItemBase( array( 'customer.group.code' => $code ), $ref );
	}


	/**
	 * Returns the customer group item object specificed by its ID
	 *
	 * @param integer $id Unique customer ID referencing an existing customer group
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface Returns the customer group item for the given ID
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		return $this->getItemBase( 'customer.group.id', $id, $ref, $default );
	}


	/**
	 * Inserts a new or updates an existing customer group item
	 *
	 * @param \Aimeos\MShop\Customer\Item\Group\Iface $item Customer group item
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MShop\\Customer\\Item\\Group\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MShop\Customer\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) { return; }

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
				/** mshop/customer/manager/group/standard/insert/mysql
				 * Inserts a new customer group record into the database table
				 *
				 * @see mshop/customer/manager/group/standard/insert/ansi
				 */

				/** mshop/customer/manager/group/standard/insert/ansi
				 * Inserts a new customer group record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the customer group item to the statement before
				 * they are sent to the database server. The number of question
				 * marks must be the same as the number of columns listed in the
				 * INSERT statement. The order of the columns must correspond to
				 * the order in the saveItems() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2015.08
				 * @category Developer
				 * @see mshop/customer/manager/group/standard/update/ansi
				 * @see mshop/customer/manager/group/standard/newid/ansi
				 * @see mshop/customer/manager/group/standard/delete/ansi
				 * @see mshop/customer/manager/group/standard/search/ansi
				 * @see mshop/customer/manager/group/standard/count/ansi
				 */
				$path = 'mshop/customer/manager/group/standard/insert';
			}
			else
			{
				/** mshop/customer/manager/group/standard/update/mysql
				 * Updates an existing customer group record in the database
				 *
				 * @see mshop/customer/manager/group/standard/update/ansi
				 */

				/** mshop/customer/manager/group/standard/update/ansi
				 * Updates an existing customer group record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the customer group item to the statement before
				 * they are sent to the database server. The order of the columns
				 * must correspond to the order in the saveItems() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2015.08
				 * @category Developer
				 * @see mshop/customer/manager/group/standard/insert/ansi
				 * @see mshop/customer/manager/group/standard/newid/ansi
				 * @see mshop/customer/manager/group/standard/delete/ansi
				 * @see mshop/customer/manager/group/standard/search/ansi
				 * @see mshop/customer/manager/group/standard/count/ansi
				 */
				$path = 'mshop/customer/manager/group/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );

			$stmt->bind( 1, $context->getLocale()->getSiteId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getCode() );
			$stmt->bind( 3, $item->getLabel() );
			$stmt->bind( 4, $date ); // mtime
			$stmt->bind( 5, $context->getEditor() );

			if( $id !== null ) {
				$stmt->bind( 6, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id );
			} else {
				$stmt->bind( 6, $date ); // ctime
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** mshop/customer/manager/group/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see mshop/customer/manager/group/standard/newid/ansi
				 */

				/** mshop/customer/manager/group/standard/newid/ansi
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
				 * @see mshop/customer/manager/group/standard/insert/ansi
				 * @see mshop/customer/manager/group/standard/update/ansi
				 * @see mshop/customer/manager/group/standard/delete/ansi
				 * @see mshop/customer/manager/group/standard/search/ansi
				 * @see mshop/customer/manager/group/standard/count/ansi
				 */
				$path = 'mshop/customer/manager/group/standard/newid';
				$item->setId( $this->newId( $conn, $path ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}
	}


	/**
	 * Returns the item objects matched by the given search criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param integer|null &$total Number of items that are available in total
	 * @return array List of items implementing \Aimeos\MShop\Customer\Item\Group\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = array(), &$total = null )
	{
		$map = array();
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'customer.group' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;

			/** mshop/customer/manager/group/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/customer/manager/group/standard/search/ansi
			 */

			/** mshop/customer/manager/group/standard/search/ansi
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
			 * @since 2015.08
			 * @category Developer
			 * @see mshop/customer/manager/group/standard/insert/ansi
			 * @see mshop/customer/manager/group/standard/update/ansi
			 * @see mshop/customer/manager/group/standard/newid/ansi
			 * @see mshop/customer/manager/group/standard/delete/ansi
			 * @see mshop/customer/manager/group/standard/count/ansi
			 */
			$cfgPathSearch = 'mshop/customer/manager/group/standard/search';

			/** mshop/customer/manager/group/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/customer/manager/group/standard/count/ansi
			 */

			/** mshop/customer/manager/group/standard/count/ansi
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
			 * @since 2015.08
			 * @category Developer
			 * @see mshop/customer/manager/group/standard/insert/ansi
			 * @see mshop/customer/manager/group/standard/update/ansi
			 * @see mshop/customer/manager/group/standard/newid/ansi
			 * @see mshop/customer/manager/group/standard/delete/ansi
			 * @see mshop/customer/manager/group/standard/search/ansi
			 */
			$cfgPathCount = 'mshop/customer/manager/group/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$map[$row['customer.group.id']] = $this->createItemBase( $row );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $map;
	}


	/**
	 * Returns a new manager for customer group extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions
	 */
	public function getSubManager( $manager, $name = null )
	{
		/** mshop/customer/manager/group/name
		 * Class name of the used customer group manager implementation
		 *
		 * Each default customer group manager can be replaced by an alternative
		 * imlementation. To use this implementation, you have to set the last
		 * part of the class name as configuration value so the manager factory
		 * knows which class it has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\MShop\Customer\Manager\Group\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\MShop\Customer\Manager\Group\Mygroup
		 *
		 * then you have to set the this configuration option:
		 *
		 *  mshop/customer/manager/group/name = Mygroup
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

		/** mshop/customer/manager/group/decorators/excludes
		 * Excludes decorators added by the "common" option from the customer group manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "mshop/common/manager/group/decorators/default" before they are wrapped
		 * around the customer group manager.
		 *
		 *  mshop/customer/manager/group/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
		 * "mshop/common/manager/decorators/default" for the customer group manager.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/customer/manager/group/decorators/global
		 * @see mshop/customer/manager/group/decorators/local
		 */

		/** mshop/customer/manager/group/decorators/global
		 * Adds a list of globally available decorators only to the customer group manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the customer manager.
		 *
		 *  mshop/customer/manager/group/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the customer
		 * group manager.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/customer/manager/group/decorators/excludes
		 * @see mshop/customer/manager/group/decorators/local
		 */

		/** mshop/customer/manager/group/decorators/local
		 * Adds a list of local decorators only to the customer group manager
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the customer group manager.
		 *
		 *  mshop/customer/manager/group/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the customer
		 * group manager.
		 *
		 * @param array List of decorator names
		 * @since 2015.08
		 * @category Developer
		 * @see mshop/common/manager/decorators/default
		 * @see mshop/customer/manager/group/decorators/excludes
		 * @see mshop/customer/manager/group/decorators/global
		 */

		return $this->getSubManagerBase( 'customer/group', $manager, $name );
	}


	/**
	 * Creates a new customer group item
	 *
	 * @param array $values List of attributes for customer group item
	 * @return \Aimeos\MShop\Customer\Item\Group\Iface New customer group item
	 */
	protected function createItemBase( array $values = array() )
	{
		return new \Aimeos\MShop\Customer\Item\Group\Standard( $values );
	}
}
