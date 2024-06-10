<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 * @package MAdmin
 * @subpackage Job
 */


namespace Aimeos\MAdmin\Job\Manager;


/**
 * Default job manager implementation.
 *
 * @package MAdmin
 * @subpackage Job
 */
class Standard
	extends \Aimeos\MAdmin\Common\Manager\Base
	implements \Aimeos\MAdmin\Job\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/** madmin/job/manager/name
	 * Class name of the used job manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Job\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Job\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  madmin/job/manager/name = Mymanager
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
	 * @since 2014.03
	 * @category Developer
	 */

	/** madmin/job/manager/decorators/excludes
	 * Excludes decorators added by the "common" option from the job manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "madmin/common/manager/decorators/default" before they are wrapped
	 * around the job manager.
	 *
	 *  madmin/job/manager/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "madmin/common/manager/decorators/default" for the job manager.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see madmin/common/manager/decorators/default
	 * @see madmin/job/manager/decorators/global
	 * @see madmin/job/manager/decorators/local
	 */

	/** madmin/job/manager/decorators/global
	 * Adds a list of globally available decorators only to the job manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the job manager.
	 *
	 *  madmin/job/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the job controller.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see madmin/common/manager/decorators/default
	 * @see madmin/job/manager/decorators/excludes
	 * @see madmin/job/manager/decorators/local
	 */

	/** madmin/job/manager/decorators/local
	 * Adds a list of local decorators only to the job manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the job manager.
	 *
	 *  madmin/job/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2" only to the job
	 * controller.
	 *
	 * @param array List of decorator names
	 * @since 2014.03
	 * @category Developer
	 * @see madmin/common/manager/decorators/default
	 * @see madmin/job/manager/decorators/excludes
	 * @see madmin/job/manager/decorators/global
	 */


	private array $searchConfig = array(
		'job.id' => array(
			'code' => 'job.id',
			'internalcode' => 'majob."id"',
			'label' => 'ID',
			'type' => 'int',
		),
		'job.siteid' => array(
			'code' => 'job.siteid',
			'internalcode' => 'majob."siteid"',
			'label' => 'Site ID',
			'public' => false,
		),
		'job.label' => array(
			'code' => 'job.label',
			'internalcode' => 'majob."label"',
			'label' => 'Label',
		),
		'job.status' => array(
			'code' => 'job.status',
			'internalcode' => 'majob."status"',
			'label' => 'Status',
			'type' => 'int',
		),
		'job.path' => array(
			'code' => 'job.path',
			'internalcode' => 'majob."path"',
			'label' => 'Generated file path',
		),
		'job.ctime' => array(
			'code' => 'job.ctime',
			'internalcode' => 'majob."ctime"',
			'label' => 'Create date/time',
			'type' => 'datetime',
		),
		'job.mtime' => array(
			'code' => 'job.mtime',
			'internalcode' => 'majob."mtime"',
			'label' => 'Modify date/time',
			'type' => 'datetime',
		),
		'job.editor' => array(
			'code' => 'job.editor',
			'internalcode' => 'majob."editor"',
			'label' => 'Editor',
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

		/** madmin/job/manager/resource
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
		$this->setResourceName( $context->config()->get( 'madmin/job/manager/resource', 'db-job' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MAdmin\Job\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'madmin/job/manager/submanagers';
		foreach( $this->context()->config()->get( $path, [] ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'madmin/job/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MAdmin\Job\Item\Iface New job item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['job.siteid'] = $values['job.siteid'] ?? $this->context()->locale()->getSiteId();
		return $this->createItemBase( $values );
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
		return $this->filterBase( 'job', $default );
	}


	/**
	 * Adds a new job to the storage.
	 *
	 * @param \Aimeos\MAdmin\Job\Item\Iface $item Job item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MAdmin\Job\Item\Iface Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MAdmin\Job\Item\Iface $item, bool $fetch = true ) : \Aimeos\MAdmin\Job\Item\Iface
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
			/** madmin/job/manager/insert/mysql
			 * Inserts a new job record into the database table
			 *
			 * @see madmin/job/manager/insert/ansi
			 */

			/** madmin/job/manager/insert/ansi
			 * Inserts a new job record into the database table
			 *
			 * Items with no ID yet (i.e. the ID is NULL) will be created in
			 * the database and the newly created ID retrieved afterwards
			 * using the "newid" SQL statement.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the job item to the statement before they are
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
			 * @category Developer
			 * @see madmin/job/manager/update/ansi
			 * @see madmin/job/manager/newid/ansi
			 * @see madmin/job/manager/delete/ansi
			 * @see madmin/job/manager/search/ansi
			 * @see madmin/job/manager/count/ansi
			 */
			$path = 'madmin/job/manager/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			/** madmin/job/manager/update/mysql
			 * Updates an existing job record in the database
			 *
			 * @see madmin/job/manager/update/ansi
			 */

			/** madmin/job/manager/update/ansi
			 * Updates an existing job record in the database
			 *
			 * Items which already have an ID (i.e. the ID is not NULL) will
			 * be updated in the database.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the job item to the statement before they are
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
			 * @category Developer
			 * @see madmin/job/manager/insert/ansi
			 * @see madmin/job/manager/newid/ansi
			 * @see madmin/job/manager/delete/ansi
			 * @see madmin/job/manager/search/ansi
			 * @see madmin/job/manager/count/ansi
			 */
			$path = 'madmin/job/manager/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		$stmt->bind( $idx++, $item->getLabel() );
		$stmt->bind( $idx++, $item->getPath() );
		$stmt->bind( $idx++, $item->getStatus(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $context->editor() );
		$stmt->bind( $idx++, $context->datetime() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $context->locale()->getSiteId() );
			$stmt->bind( $idx++, $context->datetime() );
		}

		$stmt->execute()->finish();

		if( $id === null && $fetch === true )
		{
			/** madmin/job/manager/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see madmin/job/manager/newid/ansi
			 */

			/** madmin/job/manager/newid/ansi
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
			 *  SELECT currval('seq_majob_id')
			 * For SQL Server:
			 *  SELECT SCOPE_IDENTITY()
			 * For Oracle:
			 *  SELECT "seq_majob_id".CURRVAL FROM DUAL
			 *
			 * There's no way to retrive the new ID by a SQL statements that
			 * fits for most database servers as they implement their own
			 * specific way.
			 *
			 * @param string SQL statement for retrieving the last inserted record ID
			 * @since 2014.03
			 * @category Developer
			 * @see madmin/job/manager/insert/ansi
			 * @see madmin/job/manager/update/ansi
			 * @see madmin/job/manager/delete/ansi
			 * @see madmin/job/manager/search/ansi
			 * @see madmin/job/manager/count/ansi
			 */
			$id = $this->newId( $conn, 'madmin/job/manager/newid' );
		}

		$item->setId( $id ); // so item is no longer modified

		return $item;
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MAdmin\Job\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** madmin/job/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see madmin/job/manager/delete/ansi
		 */

		/** madmin/job/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the job database.
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
		 * @see madmin/job/manager/insert/ansi
		 * @see madmin/job/manager/update/ansi
		 * @see madmin/job/manager/newid/ansi
		 * @see madmin/job/manager/search/ansi
		 * @see madmin/job/manager/count/ansi
		 */
		$path = 'madmin/job/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Creates the job object for the given job ID.
	 *
	 * @param string $id Job ID to fetch job object for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MAdmin\Job\Item\Iface Returns the job item of the given id
	 * @throws \Aimeos\MAdmin\Job\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$criteria = $this->object()->filter( $default );
		$expr = [
			$criteria->compare( '==', 'job.id', $id ),
			$criteria->getConditions()
		];
		$criteria->setConditions( $criteria->and( $expr ) );

		if( ( $item = $this->object()->search( $criteria, $ref )->first() ) ) {
			return $item;
		}

		$msg = $this->context()->translate( 'mshop', 'Job with ID "%1$s" not found' );
		throw new \Aimeos\MAdmin\Job\Exception( sprintf( $msg, $id ) );
	}


	/**
	 * Search for jobs based on the given criteria.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search object containing the conditions
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MAdmin\Job\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$context = $this->context();
		$logger = $context->logger();
		$conn = $context->db( $this->getResourceName() );

		$required = array( 'job' );
		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ONE;

		/** madmin/job/manager/search/mysql
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * @see madmin/job/manager/search/ansi
		 */

		/** madmin/job/manager/search/ansi
		 * Retrieves the records matched by the given criteria in the database
		 *
		 * Fetches the records matched by the given criteria from the job
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
		 * @category Developer
		 * @see madmin/job/manager/insert/ansi
		 * @see madmin/job/manager/update/ansi
		 * @see madmin/job/manager/newid/ansi
		 * @see madmin/job/manager/delete/ansi
		 * @see madmin/job/manager/count/ansi
		 */
		$cfgPathSearch = 'madmin/job/manager/search';

		/** madmin/job/manager/count/mysql
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * @see madmin/job/manager/count/ansi
		 */

		/** madmin/job/manager/count/ansi
		 * Counts the number of records matched by the given criteria in the database
		 *
		 * Counts all records matched by the given criteria from the job
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
		 * @see madmin/job/manager/insert/ansi
		 * @see madmin/job/manager/update/ansi
		 * @see madmin/job/manager/newid/ansi
		 * @see madmin/job/manager/delete/ansi
		 * @see madmin/job/manager/search/ansi
		 */
		$cfgPathCount = 'madmin/job/manager/count';

		$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

		while( $row = $results->fetch() )
		{
			if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
				$items[$row['job.id']] = $item;
			}
		}

		return map( $items );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'madmin/job/manager/submanagers';
		return $this->getResourceTypeBase( 'job', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] Returns a list of attributes
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** madmin/job/manager/submanagers
		 * List of manager names that can be instantiated by the job manager
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
		$path = 'madmin/job/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for job extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'job', $manager, $name );
	}


	/**
	 * Create new admin job item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs of a job
	 * @return \Aimeos\MAdmin\Job\Item\Iface New job item
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MAdmin\Job\Item\Iface
	{
		return new \Aimeos\MAdmin\Job\Item\Standard( $values );
	}
}
