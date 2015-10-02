<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MAdmin
 * @subpackage Job
 */


/**
 * Default job manager implementation.
 *
 * @package MAdmin
 * @subpackage Job
 */
class MAdmin_Job_Manager_Default
	extends MAdmin_Common_Manager_Abstract
	implements MAdmin_Job_Manager_Interface
{
	private $_searchConfig = array(
		'job.id'=> array(
			'code'=>'job.id',
			'internalcode'=>'majob."id"',
			'label'=>'Job ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'job.siteid'=> array(
			'code'=>'job.siteid',
			'internalcode'=>'majob."siteid"',
			'label'=>'Job site ID',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'job.label'=> array(
			'code'=>'job.label',
			'internalcode'=>'majob."label"',
			'label'=>'Job label',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.method'=> array(
			'code'=>'job.method',
			'internalcode'=>'majob."method"',
			'label'=>'Job method',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.parameter'=> array(
			'code'=>'job.parameter',
			'internalcode'=>'majob."parameter"',
			'label'=>'Job parameter',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.result'=> array(
			'code'=>'job.result',
			'internalcode'=>'majob."result"',
			'label'=>'Job result',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.status'=> array(
			'code'=>'job.status',
			'internalcode'=>'majob."status"',
			'label'=>'Job status',
			'type'=> 'integer',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_INT,
		),
		'job.ctime'=> array(
			'code'=>'job.ctime',
			'internalcode'=>'majob."ctime"',
			'label'=>'Job create date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.mtime'=> array(
			'code'=>'job.mtime',
			'internalcode'=>'majob."mtime"',
			'label'=>'Job modification date/time',
			'type'=> 'datetime',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
		'job.editor'=> array(
			'code'=>'job.editor',
			'internalcode'=>'majob."editor"',
			'label'=>'Job editor',
			'type'=> 'string',
			'internaltype'=> MW_DB_Statement_Abstract::PARAM_STR,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );
		$this->_setResourceName( 'db-job' );
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param integer[] $siteids List of IDs for sites whose entries should be deleted
	 */
	public function cleanup( array $siteids )
	{
		$path = 'classes/job/manager/submanagers';
		foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
			$this->getSubManager( $domain )->cleanup( $siteids );
		}

		$this->cleanupBase( $siteids, 'madmin/job/manager/default/delete' );
	}


	/**
	 * Create new job item object.
	 *
	 * @return MAdmin_Job_Item_Interface
	 */
	public function createItem()
	{
		$values = array( 'siteid' => $this->_getContext()->getLocale()->getSiteId() );
		return $this->_createItem( $values );
	}


	/**
	 * Creates a search object and optionally sets base criteria.
	 *
	 * @param boolean $default Add default criteria
	 * @return MW_Common_Criteria_Interface Criteria object
	 */
	public function createSearch( $default = false )
	{
		if( $default === true ) {
			return $this->createSearchBase( 'job' );
		}

		return parent::createSearch();
	}


	/**
	 * Adds a new job to the storage.
	 *
	 * @param MAdmin_Job_Item_Interface $item Job item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MAdmin_Job_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MAdmin_Job_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( !$item->isModified() ) {
			return;
		}

		$context = $this->_getContext();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$id = $item->getId();
			$date = date( 'Y-m-d H:i:s' );

			if( $id === null )
			{
				/** madmin/job/manager/default/insert
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
				 * @see madmin/job/manager/default/update
				 * @see madmin/job/manager/default/newid
				 * @see madmin/job/manager/default/delete
				 * @see madmin/job/manager/default/search
				 * @see madmin/job/manager/default/count
				 */
				$path = 'madmin/job/manager/default/insert';
			}
			else
			{
				/** madmin/job/manager/default/update
				 * Updates an existing job record in the database
				 *
				 * Items which already have an ID (i.e. the ID is not NULL) will
				 * be updated in the database.
				 *
				 * The SQL statement must be a string suitable for being used as
				 * prepared statement. It must include question marks for binding
				 * the values from the job item to the statement before they are
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
				 * @see madmin/job/manager/default/insert
				 * @see madmin/job/manager/default/newid
				 * @see madmin/job/manager/default/delete
				 * @see madmin/job/manager/default/search
				 * @see madmin/job/manager/default/count
				 */
				$path = 'madmin/job/manager/default/update';
			}

			$stmt = $this->_getCachedStatement( $conn, $path );
			$stmt->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getLabel() );
			$stmt->bind( 3, $item->getMethod() );
			$stmt->bind( 4, json_encode( $item->getParameter() ) );
			$stmt->bind( 5, json_encode( $item->getResult() ) );
			$stmt->bind( 6, $item->getStatus(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 7, $context->getEditor() );
			$stmt->bind( 8, $date );

			if( $id !== null ) {
				$stmt->bind( 9, $id, MW_DB_Statement_Abstract::PARAM_INT );
				$item->setId( $id ); // so item is no longer modified
			} else {
				$stmt->bind( 9, $date );
			}

			$stmt->execute()->finish();

			if( $id === null && $fetch === true )
			{
				/** madmin/job/manager/default/newid
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
				 * @see madmin/job/manager/default/insert
				 * @see madmin/job/manager/default/update
				 * @see madmin/job/manager/default/delete
				 * @see madmin/job/manager/default/search
				 * @see madmin/job/manager/default/count
				 */
				$path = 'madmin/job/manager/default/newid';
				$item->setId( $this->_newId( $conn, $context->getConfig()->get( $path, $path ) ) );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
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
		/** madmin/job/manager/default/delete
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
		 * @see madmin/job/manager/default/insert
		 * @see madmin/job/manager/default/update
		 * @see madmin/job/manager/default/newid
		 * @see madmin/job/manager/default/search
		 * @see madmin/job/manager/default/count
		 */
		$path = 'madmin/job/manager/default/delete';
		$this->_deleteItems( $ids, $this->_getContext()->getConfig()->get( $path, $path ) );
	}


	/**
	 * Creates the job object for the given job ID.
	 *
	 * @param integer $id Job ID to fetch job object for
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @return MAdmin_Job_Item_Interface Returns the job item of the given id
	 * @throws MAdmin_Job_Exception If item couldn't be found
	 */
	public function getItem( $id, array $ref = array() )
	{
		$criteria = $this->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'job.id', $id ) );
		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new MAdmin_Job_Exception( sprintf( 'Job with ID "%1$s" not found', $id ) );
		}

		return $item;
	}


	/**
	 * Search for jobs based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param array $ref List of domains to fetch list items and referenced items for
	 * @param integer &$total Number of items that are available in total
	 * @return array List of jobs implementing MAdmin_Job_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$items = array();
		$context = $this->_getContext();
		$logger = $context->getLogger();

		$dbm = $context->getDatabaseManager();
		$dbname = $this->_getResourceName();
		$conn = $dbm->acquire( $dbname );

		try
		{
			$required = array( 'job' );
			$level = MShop_Locale_Manager_Abstract::SITE_SUBTREE;

			/** madmin/job/manager/default/search
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
			 * @see madmin/job/manager/default/insert
			 * @see madmin/job/manager/default/update
			 * @see madmin/job/manager/default/newid
			 * @see madmin/job/manager/default/delete
			 * @see madmin/job/manager/default/count
			 */
			$cfgPathSearch = 'madmin/job/manager/default/search';

			/** madmin/job/manager/default/count
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
			 * @see madmin/job/manager/default/insert
			 * @see madmin/job/manager/default/update
			 * @see madmin/job/manager/default/newid
			 * @see madmin/job/manager/default/delete
			 * @see madmin/job/manager/default/search
			 */
			$cfgPathCount = 'madmin/job/manager/default/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false )
			{
				$config = $row['parameter'];
				if( ( $row['parameter'] = json_decode( $row['parameter'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'madmin_job.parameter', $row['id'], $config );
					$logger->log( $msg, MW_Logger_Abstract::WARN );
				}

				$config = $row['result'];
				if( ( $row['result'] = json_decode( $row['result'], true ) ) === null )
				{
					$msg = sprintf( 'Invalid JSON as result of search for ID "%2$s" in "%1$s": %3$s', 'madmin_job.result', $row['id'], $config );
					$logger->log( $msg, MW_Logger_Abstract::WARN );
				}

				$items[$row['id']] = $this->_createItem( $row );
			}

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withsub Return also attributes of sub-managers if true
	 * @return array Returns a list of attributes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withsub = true )
	{
		/** classes/job/manager/submanagers
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
		$path = 'classes/job/manager/submanagers';

		return $this->getSearchAttributesBase( $this->_searchConfig, $path, array(), $withsub );
	}


	/**
	 * Returns a new manager for job extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return MShop_Common_Manager_Interface Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->getSubManagerBase( 'job', $manager, $name );
	}


	/**
	 * Create new admin job item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs of a job
	 * @return MAdmin_Job_Item_Interface
	 */
	protected function _createItem( array $values = array() )
	{
		return new MAdmin_Job_Item_Default( $values );
	}


	/**
	 * Returns the search result object for the given SQL statement.
	 *
	 * @param MW_DB_Connection_Interface $conn Database connection
	 * @param string $sql SQL-statement to execute
	 * @return MW_DB_Result_Interface Returns db result set from given sql statment
	 */
	protected function _getSearchResults( MW_DB_Connection_Interface $conn, $sql )
	{
		$context = $this->_getContext();
		$siteId = $context->getLocale()->getSiteId();

		$statement = $conn->create( $sql );
		$statement->bind( 1, $siteId, MW_DB_Statement_Abstract::PARAM_INT );

		$context->getLogger()->log( __METHOD__ . ': SQL statement: ' . $statement, MW_Logger_Abstract::DEBUG );

		return $statement->execute();
	}
}
