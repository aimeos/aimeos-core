<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MAdmin
 * @subpackage Log
 * @version $Id: Default.php 14720 2012-01-05 17:09:29Z nsendetzky $
 */


/**
 * Default log manager implementation.
 *
 * @package MAdmin
 * @subpackage Log
 */
class MAdmin_Log_Manager_Default
	extends MAdmin_Common_Manager_Abstract
	implements MAdmin_Log_Manager_Interface, MW_Logger_Interface
{
	private $_requestid;
	private $_dbname = 'db';

	private $_searchConfig = array(
		'log.id' => array(
			'code' => 'log.id',
			'internalcode' => 'malog."id"',
			'label' => 'Log ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'log.siteid' => array(
			'code' => 'log.siteid',
			'internalcode' => 'malog."siteid"',
			'label' => 'Log site ID',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'log.facility' => array(
			'code' => 'log.facility',
			'internalcode' => 'malog."facility"',
			'label' => 'Log facility',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'log.timestamp' => array(
			'code' => 'log.timestamp',
			'internalcode' => 'malog."timestamp"',
			'label' => 'Log create date/time',
			'type' => 'datetime',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'log.priority' => array(
			'code' => 'log.priority',
			'internalcode' => 'malog."priority"',
			'label' => 'Log priority',
			'type' => 'integer',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_INT,
		),
		'log.message' => array(
			'code' => 'log.message',
			'internalcode' => 'malog."message"',
			'label' => 'Log message',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		),
		'log.request' => array(
			'code' => 'log.request',
			'internalcode' => 'malog."request"',
			'label' => 'Log request',
			'type' => 'string',
			'internaltype' => MW_DB_Statement_Abstract::PARAM_STR,
		)
	);


	/**
	 * Creates the log manager that will use the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$config = $context->getConfig();

		$this->_loglevel = $config->get( 'madmin/log/manager/default/loglevel', MW_Logger_Abstract::WARN );
		$this->_requestid = md5( php_uname('n') . getmypid() . date( 'Y-m-d H:i:s' ) );

		if( $config->get( 'resource/db-log/adapter', null ) !== null ) {
			$this->_dbname = 'db-log';
		}
	}


	/**
	 * Create new log item object.
	 *
	 * @return MAdmin_Log_Item_Interface
	 */
	public function createItem()
	{
		try {
			$siteid = $this->_getContext()->getLocale()->getSiteId();
		} catch( Exception $e ) {
			$siteid = null;
		}

		$values = array( 'siteid' => $siteid );
		return $this->_createItem( $values );
	}


	/**
	 * Adds a new log to the storage.
	 *
	 * @param MAdmin_Log_Item_Interface $item Log item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 */
	public function saveItem( MShop_Common_Item_Interface $item, $fetch = true )
	{
		$iface = 'MAdmin_Log_Item_Interface';
		if( !( $item instanceof $iface ) ) {
			throw new MAdmin_Log_Exception( sprintf( 'Object is not of required type "%1$s"', $iface ) );
		}

		if( ! $item->isModified() ) {
			return;
		}

		$context = $this->_getContext();
		$config = $context->getConfig();

		try {
			$siteid = $context->getLocale()->getSiteId();
		} catch( Exception $e ) {
			$siteid = null;
		}

		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$id = $item->getId();

			$path = 'madmin/log/manager/default/';
			$path .= ( $id === null ) ? 'insert' : 'update';

			$stmt = $conn->create( $config->get( $path, $path ) );
			$stmt->bind( 1, $siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $item->getFacility() );
			$stmt->bind( 3, date('Y-m-d H:i:s', time() ) );
			$stmt->bind( 4, $item->getPriority(), MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 5, $item->getMessage() );
			$stmt->bind( 6, $item->getRequest() );

			if( ! is_null( $item->getId() ) ) {
				$stmt->bind( 7, $item->getId(), MW_DB_Statement_Abstract::PARAM_INT );
			}

			$stmt->execute()->finish();

			if ( $fetch === true )
			{
				if( $id === null )
				{
					$path = 'madmin/log/manager/default/newid';
					$item->setId( $this->_newId( $conn, $config->get( $path, $path ) ) );
				} else {
					$item->setId( $id );
				}
			}

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Deletes an existing log from the storage.
	 *
	 * @param integer $itemId Log id of an existing Log in the storage that should be deleted
	 */
	public function deleteItem( $itemId )
	{
		$context = $this->_getContext();
		$config = $context->getConfig();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );

		try
		{
			$path = 'madmin/log/manager/default/delete';
			$sql = $config->get( $path, $path );

			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $itemId );
			$stmt->execute()->finish();

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Creates the log object for the given log id.
	 *
	 * @param integer $id Log ID to fetch log object for
	 * @return MAdmin_Log_Item_Interface
	 */
	public function getItem( $id, array $ref = array() )
	{
		$criteria = $this->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'log.id', $id ) );
		$items = $this->searchItems( $criteria, $ref );

		if( ( $item = reset( $items ) ) === false ) {
			throw new MAdmin_Log_Exception( sprintf( 'Log entry with ID "%1$s" not found', $id ) );
		}

		return $item;
	}


	/**
	 * Search for log entries based on the given criteria.
	 *
	 * @param MW_Common_Criteria_Interface $search Search object containing the conditions
	 * @param integer &$total Number of items that are available in total
	 *
	 * @return array List of jobs implementing MAdmin_Job_Item_Interface
	 */
	public function searchItems( MW_Common_Criteria_Interface $search, array $ref = array(), &$total = null )
	{
		$context = $this->_getContext();
		$dbm = $context->getDatabaseManager();
		$conn = $dbm->acquire( $this->_dbname );
		$items = array();

		try
		{
			$level = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
			$cfgPathSearch = 'madmin/log/manager/default/search';
			$cfgPathCount =  'madmin/log/manager/default/count';
			$required = array( 'log' );

			$results = $this->_searchItems( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== false ) {
				$items[ $row['id'] ] = $this->_createItem( $row );
			}

			$dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $this->_dbname );
			throw $e;
		}

		return $items;
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param boolean $withSub Return also attributes of sub-managers if true
	 * @return array Returns a list of attribtes implementing MW_Common_Criteria_Attribute_Interface
	 */
	public function getSearchAttributes( $withSub = true )
	{
		$list = array();

		foreach( $this->_searchConfig as $key => $fields ) {
			$list[ $key ] = new MW_Common_Criteria_Attribute_Default( $fields );
		}

		if( $withSub === true )
		{
			$path = 'classes/log/manager/submanagers';
			foreach( $this->_getContext()->getConfig()->get( $path, array() ) as $domain ) {
				$list = array_merge( $list, $this->getSubManager( $domain )->getSearchAttributes() );
			}
		}

		return $list;
	}


	/**
	 * Returns a new manager for log extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return mixed Manager for different extensions, e.g stock, tags, locations, etc.
	 */
	public function getSubManager( $manager, $name = null )
	{
		return $this->_getSubManager( 'log', $manager, $name );
	}


	/**
	 * Create new admin log item object initialized with given parameters.
	 *
	 * @param array $values Associative list of key/value pairs of a job
	 * @return MAdmin_Log_Item_Interface
	 */
	protected function _createItem( array $values = array() )
	{
		return new MAdmin_Log_Item_Default( $values );
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
		$statement = $conn->create( $sql );

		try {
			$statement->bind( 1, $context->getLocale()->getSiteId(), MW_DB_Statement_Abstract::PARAM_INT );
		} catch( Exception $e ) {
			$statement->bind( 1, null, MW_DB_Statement_Abstract::PARAM_INT );
		}

		return $statement->execute();
	}


	/**
	 * Writes a message to the configured log facility.
	 *
	 * @param string $message Message text that should be written to the log facility
	 * @param integer $priority Priority of the message for filtering
	 * @param string $facility Facility for logging different types of messages (e.g. message, auth, user, changelog)
	 */
	public function log( $message, $priority = MW_Logger_Abstract::ERR, $facility = 'message' )
	{
		if( $priority <= $this->_loglevel )
		{
			if( !is_scalar( $message ) ) {
				$message = json_encode( $message );
			}

			$item = $this->createItem();

			$item->setFacility( $facility );
			$item->setPriority( $priority );
			$item->setMessage( $message );
			$item->setRequest( $this->_requestid );

			$this->saveItem( $item );
		}
	}
}
