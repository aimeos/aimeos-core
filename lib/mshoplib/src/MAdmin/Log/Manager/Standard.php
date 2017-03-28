<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
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
	implements \Aimeos\MAdmin\Log\Manager\Iface
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
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'log.facility' => array(
			'code' => 'log.facility',
			'internalcode' => 'malog."facility"',
			'label' => 'Log facility',
			'type' => 'string',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'log.timestamp' => array(
			'code' => 'log.timestamp',
			'internalcode' => 'malog."timestamp"',
			'label' => 'Log create date/time',
			'type' => 'datetime',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
		),
		'log.priority' => array(
			'code' => 'log.priority',
			'internalcode' => 'malog."priority"',
			'label' => 'Log priority',
			'type' => 'integer',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
		),
		'log.message' => array(
			'code' => 'log.message',
			'internalcode' => 'malog."message"',
			'label' => 'Log message',
			'type' => 'string',
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

		/** madmin/log/manager/standard/loglevel
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
		 * @param integer Log level number
		 * @since 2014.03
		 * @category Developer
		 * @category User
		 */
		$this->loglevel = $config->get( 'madmin/log/manager/standard/loglevel', \Aimeos\MW\Logger\Base::WARN );
		$this->requestid = md5( php_uname( 'n' ) . getmypid() . date( 'Y-m-d H:i:s' ) );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'madmin/log/manager/submanagers';
		foreach( $this->getContext()->getConfig()->get( $path, [] ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'madmin/log/manager/standard/delete' );
	}


	/**
	 * Create new log item object.
	 *
	 * @return \Aimeos\MAdmin\Log\Item\Iface
	 */
	public function createItem()
	{
		try {
			$siteid = $this->getContext()->getLocale()->getSiteId();
		} catch( \Exception $e ) {
			$siteid = null;
		}

		$values = array( 'log.siteid' => $siteid );
		return $this->createItemBase( $values );
	}


	/**
	 * Adds a new log to the storage.
	 *
	 * @param \Aimeos\MAdmin\Log\Item\Iface $item Log item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( \Aimeos\MShop\Common\Item\Iface $item, $fetch = true )
	{
		$iface = '\\Aimeos\\MAdmin\\Log\\Item\\Iface';
		if( !( $item instanceof $iface ) ) {
			throw new \Aimeos\MAdmin\Log\Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) {
			return;
		}

		$context = $this->getContext();

		try {
			$siteid = $context->getLocale()->getSiteId();
		} catch( \Exception $e ) {
			$siteid = null;
		}

		$dbm = $context->getDatabaseManager();
		$dbname = $this->getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();

			if( $id === null )
			{
				/** madmin/log/manager/standard/insert/mysql
				 * Inserts a new log record into the database table
				 *
				 * @see madmin/log/manager/standard/insert/ansi
				 */

				/** madmin/log/manager/standard/insert/ansi
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
				 * order in the saveItems() method, so the correct values are
				 * bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for inserting records
				 * @since 2014.03
				 * @category Developer
				 * @see madmin/log/manager/standard/update/ansi
				 * @see madmin/log/manager/standard/newid/ansi
				 * @see madmin/log/manager/standard/delete/ansi
				 * @see madmin/log/manager/standard/search/ansi
				 * @see madmin/log/manager/standard/count/ansi
				 */
				$path = 'madmin/log/manager/standard/insert';
			}
			else
			{
				/** madmin/log/manager/standard/update/mysql
				 * Updates an existing log record in the database
				 *
				 * @see madmin/log/manager/standard/update/ansi
				 */

				/** madmin/log/manager/standard/update/ansi
				 * Updates an existing log record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the log item to the statement before they are
				 * sent to the database server. The order of the columns must
				 * correspond to the order in the saveItems() method, so the
				 * correct values are bound to the columns.
				 *
				 * The SQL statement should conform to the ANSI standard to be
				 * compatible with most relational database systems. This also
				 * includes using double quotes for table and column names.
				 *
				 * @param string SQL statement for updating records
				 * @since 2014.03
				 * @category Developer
				 * @see madmin/log/manager/standard/insert/ansi
				 * @see madmin/log/manager/standard/newid/ansi
				 * @see madmin/log/manager/standard/delete/ansi
				 * @see madmin/log/manager/standard/search/ansi
				 * @see madmin/log/manager/standard/count/ansi
				 */
				$path = 'madmin/log/manager/standard/update';
			}

			$stmt = $this->getCachedStatement( $conn, $path );
			$stmt->bind( 1, $siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $item->getFacility() );
			$stmt->bind( 3, date( 'Y-m-d H:i:s' ) );
			$stmt->bind( 4, $item->getPriority(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 5, $item->getMessage() );
			$stmt->bind( 6, $item->getRequest() );

			if( $item->getId() !== null ) {
				$stmt->bind( 7, $item->getId(), \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$item->setId( $id );
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** madmin/log/manager/standard/newid/mysql
				 * Retrieves the ID generated by the database when inserting a new record
				 *
				 * @see madmin/log/manager/standard/newid/ansi
				 */

				/** madmin/log/manager/standard/newid/ansi
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
				 * @see madmin/log/manager/standard/insert/ansi
				 * @see madmin/log/manager/standard/update/ansi
				 * @see madmin/log/manager/standard/delete/ansi
				 * @see madmin/log/manager/standard/search/ansi
				 * @see madmin/log/manager/standard/count/ansi
				 */
				$path = 'madmin/log/manager/standard/newid';
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
	 * Removes multiple items specified by ids in the array.
	 *
	 * @param array $ids List of IDs
	 */
	public function deleteItems( array $ids )
	{
		/** madmin/log/manager/standard/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see madmin/log/manager/standard/delete/ansi
		 */

		/** madmin/log/manager/standard/delete/ansi
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
		 * @see madmin/log/manager/standard/insert/ansi
		 * @see madmin/log/manager/standard/update/ansi
		 * @see madmin/log/manager/standard/newid/ansi
		 * @see madmin/log/manager/standard/search/ansi
		 * @see madmin/log/manager/standard/count/ansi
		 */
		$path = 'madmin/log/manager/standard/delete';
		$this->deleteItemsBase( $ids, $path );
	}


	/**
	 * Creates the log object for the given log id.
	 *
	 * @param integer $id Log ID to fetch log object for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param boolean $default Add default criteria
	 * @return \Aimeos\MAdmin\Log\Item\Iface Returns the log item of the given id
	 * @throws \Aimeos\MAdmin\Log\Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = [], $default = false )
	{
		$criteria = $this->createSearch( $default );
		$expr = [
			$criteria->compare( '==', 'log.id', $id ),
			$criteria->getConditions()
		];
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Aimeos\MAdmin\Log\Exception( sprintf( 'Log entry with ID "%1$s" not found', $id ) );
		}

		return $item;
	}


	/**
	 * Search for log entries based on the given criteria.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search object containing the conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of jobs implementing \Aimeos\MAdmin\Job\Item\Iface
	 */
	public function searchItems( \Aimeos\MW\Criteria\Iface $search, array $ref = [], &$total = null )
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

			/** madmin/log/manager/standard/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see madmin/log/manager/standard/search/ansi
			 */

			/** madmin/log/manager/standard/search/ansi
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
			 * @see madmin/log/manager/standard/insert/ansi
			 * @see madmin/log/manager/standard/update/ansi
			 * @see madmin/log/manager/standard/newid/ansi
			 * @see madmin/log/manager/standard/delete/ansi
			 * @see madmin/log/manager/standard/count/ansi
			 */
			$cfgPathSearch = 'madmin/log/manager/standard/search';

			/** madmin/log/manager/standard/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see madmin/log/manager/standard/count/ansi
			 */

			/** madmin/log/manager/standard/count/ansi
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
			 * @see madmin/log/manager/standard/insert/ansi
			 * @see madmin/log/manager/standard/update/ansi
			 * @see madmin/log/manager/standard/newid/ansi
			 * @see madmin/log/manager/standard/delete/ansi
			 * @see madmin/log/manager/standard/search/ansi
			 */
			$cfgPathCount = 'madmin/log/manager/standard/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$items[$row['log.id']] = $this->createItemBase( $row );
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
	 * Returns the available manager types
	 *
	 * @param boolean $withsub Return also the resource type of sub-managers if true
	 * @return array Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( $withsub = true )
	{
		$path = 'madmin/log/manager/submanagers';

		return $this->getResourceTypeBase( 'log', $path, [], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing \Aimeos\MW\Criteria\Attribute\Iface
	 */
	public function getSearchAttributes( $withsub = true )
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
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'log', $manager, $name );
	}


	/**
	 * Create new admin log item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs of a job
	 * @return \Aimeos\MAdmin\Log\Item\Iface
	 */
	protected function createItemBase( array $values = [] )
	{
		return new \Aimeos\MAdmin\Log\Item\Standard( $values );
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 */
	public function log( $message, $priority = \Aimeos\MW\Logger\Base::ERR, $facility = 'message' )
	{
		if( $priority <= $this->loglevel )
		{
			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$item = $this->createItem();

			$item->setFacility( $facility );
			$item->setPriority( $priority );
			$item->setMessage( $message );
			$item->setRequest( $this->requestid );

			$this->saveItem( $item );
		}
	}
}
