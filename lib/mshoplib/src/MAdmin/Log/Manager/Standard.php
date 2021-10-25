<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MAdmin
 * @subpackage Log
 */


namespace Aimeos\MAdmin\Log\Manager;


/**
 * Default log manager implementation.
 *
 * @package MAdmin
 * @subpackage Log
 */
class Standard
	extends \Aimeos\MAdmin\Common\Manager\Base
	implements \Aimeos\MAdmin\Log\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	private $loglevel;
	private $requestid;

	private $searchConfig = array(
		'log.id' => array(
			'code' => 'log.id',
			'internalcode' => 'malog."id"',
			'label' => 'Log ID',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'log.siteid' => array(
			'code' => 'log.siteid',
			'internalcode' => 'malog."siteid"',
			'label' => 'Log site ID',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
			'public' => false,
		),
		'log.message' => array(
			'code' => 'log.message',
			'internalcode' => 'malog."message"',
			'label' => 'Log message',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'log.facility' => array(
			'code' => 'log.facility',
			'internalcode' => 'malog."facility"',
			'label' => 'Log facility',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'log.priority' => array(
			'code' => 'log.priority',
			'internalcode' => 'malog."priority"',
			'label' => 'Log priority',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'log.timestamp' => array(
			'code' => 'log.timestamp',
			'internalcode' => 'malog."timestamp"',
			'label' => 'Log create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'log.request' => array(
			'code' => 'log.request',
			'internalcode' => 'malog."request"',
			'label' => 'Log request',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		)
	);


	/**
	 * Creates the log manager that will use the given context object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );
		$this->setResourceName( 'db-log' );

		$config = $context->getConfig();

		/** madmin/log/manager/loglevel
		 * Sets the severity level for messages to be written to the log
		 *
		 * Manager, provider and other active components write messages about
		 * problems, informational and debug output to the logs. The messages
		 * that are actually written to the logs can be limited with the
		 * "loglevel" configuration.
		 *
		 * Available log levels are:
		 * * Emergency (0): system is unusable
		 * * Alert (1): action must be taken immediately
		 * * Critical (2): critical conditions
		 * * Error (3): error conditions
		 * * Warning (4): warning conditions
		 * * Notice (5): normal but significant condition
		 * * Informational (6): informational messages
		 * * Debug (7): debug messages
		 *
		 * The "loglevel" configuration option defines the severity of messages
		 * that will be written to the logs, e.g. a log level of "3" (error)
		 * will allow all messages with an associated level of three and below
		 * (error, critical, alert and emergency) to be written to the storage.
		 * Messages with other log levels (warning, notice, informational and
		 * debug) would be discarded and won't be written to the storage.
		 *
		 * The higher the log level, the more messages will be written to the
		 * storage. Keep in mind that a higher volume of messages will slow
		 * down the system and the debug log level shouldn't be used in
		 * production environments with a high number of visitors!
		 *
		 * @param int Log level number
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */
		$this->loglevel = $config->get( 'madmin/log/manager/loglevel', \Aimeos\MW\Logger\Base::NOTICE );
		$this->requestid = md5( php_uname( 'n' ) . getmypid() . date( 'Y-m-d H:i:s' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MAdmin\Log\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'madmin/log/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
			$this->getObject()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'madmin/log/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MAdmin\Log\Item\Iface New log item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		try {
			$values['log.siteid'] = $this->getContext()->getLocale()->getSiteId();
		} catch( \Exception $e ) {
			$values['log.siteid'] = null;
		}

		return $this->createItemBase( $values );
	}


	/**
	 * Adds a new log to the storage.
	 *
	 * @param \Aimeos\MAdmin\Log\Item\Iface $item Log item that should be saved to the storage
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MAdmin\Log\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MAdmin\Log\Item\Iface $item, bool $fetch = true ) : \Aimeos\MAdmin\Log\Item\Iface
	{
		if( !$item->isModified() ) {
			return $item;
		}

		$context = $this->getContext();

		try {
			$siteid = $context->getLocale()->getSiteId();
		} catch( \Exception $e ) {
			$siteid = '';
		}

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$columns = $this->getObject()->getSaveAttributes();

			if( $id === null )
			{
				/** madmin/log/manager/insert/mysql
				 * Inserts a new log record into the database table
				 *
				 * @see madmin/log/manager/insert/ansi
				 */

				/** madmin/log/manager/insert/ansi
				 * Inserts a new log record into the database table
				 *
				 * Items with no ID yet (i.e. the ID is NULL) will be created in
				 * the database and the newly created ID retrieved afterwards
				 * using the "newid" SQL statement.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the log item to the statement before they are
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
				 * @see madmin/log/manager/update/ansi
				 * @see madmin/log/manager/newid/ansi
				 * @see madmin/log/manager/delete/ansi
				 * @see madmin/log/manager/search/ansi
				 * @see madmin/log/manager/count/ansi
				 */
				$path = 'madmin/log/manager/insert';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
			}
			else
			{
				/** madmin/log/manager/update/mysql
				 * Updates an existing log record in the database
				 *
				 * @see madmin/log/manager/update/ansi
				 */

				/** madmin/log/manager/update/ansi
				 * Updates an existing log record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the log item to the statement before they are
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
				 * @see madmin/log/manager/insert/ansi
				 * @see madmin/log/manager/newid/ansi
				 * @see madmin/log/manager/delete/ansi
				 * @see madmin/log/manager/search/ansi
				 * @see madmin/log/manager/count/ansi
				 */
				$path = 'madmin/log/manager/update';
				$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
			}

			$idx = 1;
			$stmt = $this->getCachedStatement( $conn, $path, $sql );

			foreach( $columns as $name => $entry ) {
				$stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
			}


			$stmt->bind( $idx++, $item->getFacility() );
			$stmt->bind( $idx++, date( 'Y-m-d H:i:s' ) );
			$stmt->bind( $idx++, $item->getPriority(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( $idx++, $item->getMessage() );
			$stmt->bind( $idx++, $item->getRequest() );
			$stmt->bind( $idx++, $siteid );

			if( $item->getId() !== null ) {
				$stmt->bind( $idx++, $item->getId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** madmin/log/manager/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see madmin/log/manager/newid/ansi
				 */

				/** madmin/log/manager/newid/ansi
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
				 *  SELECT currval('seq_malog_id')
				 * For SQL Server:
				 *  SELECT SCOPE_IDENTITY()
				 * For Oracle:
				 *  SELECT "seq_malog_id".CURRVAL FROM DUAL
				 *
				 * There's no way to retrive the new ID by a SQL statements that
				 * fits for most database servers as they implement their own
				 * specific way.
				 *
				 * @param string SQL statement for retrieving the last inserted record ID
				 * @since 2014.03
				 * @category Developer
				 * @see madmin/log/manager/insert/ansi
				 * @see madmin/log/manager/update/ansi
				 * @see madmin/log/manager/delete/ansi
				 * @see madmin/log/manager/search/ansi
				 * @see madmin/log/manager/count/ansi
				 */
				$id = $this->newId( $conn, 'madmin/log/manager/newid' );
			}

			$item->setId( $id );

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
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MAdmin\Log\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** madmin/log/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see madmin/log/manager/delete/ansi
		 */

		/** madmin/log/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the log database.
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
		 * @see madmin/log/manager/insert/ansi
		 * @see madmin/log/manager/update/ansi
		 * @see madmin/log/manager/newid/ansi
		 * @see madmin/log/manager/search/ansi
		 * @see madmin/log/manager/count/ansi
		 */
		$path = 'madmin/log/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path );
	}


	/**
	 * Creates the log object for the given log id.
	 *
	 * @param string $id Log ID to fetch log object for
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MAdmin\Log\Item\Iface Returns the log item of the given id
	 * @throws \Aimeos\MAdmin\Log\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$criteria = $this->getObject()->filter( $default );
		$expr = [
			$criteria->compare( '==', 'log.id', $id ),
			$criteria->getConditions()
		];
		$criteria->setConditions( $criteria->and( $expr ) );

		if( ( $item = $this->getObject()->search( $criteria, $ref )->first() ) ) {
			return $item;
		}

		$msg = $this->getContext()->translate( 'mshop', 'Log entry with ID "%1$s" not found' );
		throw new \Aimeos\MAdmin\Log\Exception( sprintf( $msg, $id ) );
	}


	/**
	 * Search for log entries based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search object containing the conditions
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing Aimeos\MAdmin\Log\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$context = $this->getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'log' );
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE;

			/** madmin/log/manager/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see madmin/log/manager/search/ansi
			 */

			/** madmin/log/manager/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the log
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
			 * @see madmin/log/manager/insert/ansi
			 * @see madmin/log/manager/update/ansi
			 * @see madmin/log/manager/newid/ansi
			 * @see madmin/log/manager/delete/ansi
			 * @see madmin/log/manager/count/ansi
			 */
			$cfgPathSearch = 'madmin/log/manager/search';

			/** madmin/log/manager/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see madmin/log/manager/count/ansi
			 */

			/** madmin/log/manager/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the log
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
			 * @see madmin/log/manager/insert/ansi
			 * @see madmin/log/manager/update/ansi
			 * @see madmin/log/manager/newid/ansi
			 * @see madmin/log/manager/delete/ansi
			 * @see madmin/log/manager/search/ansi
			 */
			$cfgPathCount = 'madmin/log/manager/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null )
			{
				if( $item = $this->applyFilter( $this->createItemBase( $row ) ) ) {
					$items[$row['log.id']] = $item;
				}
			}

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
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
		$path = 'madmin/log/manager/submanagers';
		return $this->getResourceTypeBase( 'log', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] Returns a list of search attributes
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** madmin/log/manager/submanagers
		 * List of manager names that can be instantiated by the log manager
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
		$path = 'madmin/log/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Returns a new manager for log extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'log', $manager, $name );
	}


	/**
	 * Create new admin log item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs of a job
	 * @return \Aimeos\MAdmin\Log\Item\Iface New log item
	 */
	protected function createItemBase( array $values = [] ) : \Aimeos\MAdmin\Log\Item\Iface
	{
		return new \Aimeos\MAdmin\Log\Item\Standard( $values );
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string|array|object $message Message text that should be written to the log facility
	 * @param int $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 * @return \Aimeos\MW\Logger\Iface Logger object for method chaining
	 */
	public function log( $message, int $priority = \Aimeos\MW\Logger\Base::ERR, string $facility = 'message' ) : \Aimeos\MW\Logger\Iface
	{
		if( $priority <= $this->loglevel )
		{
			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$item = $this->getObject()->create();

			$item->setFacility( $facility );
			$item->setPriority( $priority );
			$item->setMessage( $message );
			$item->setRequest( $this->requestid );

			$this->getObject()->save( $item );
		}

		return $this;
	}
}
