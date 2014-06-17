<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Cache
 */


/**
 * Database cache class.
 *
 * @package MW
 * @subpackage Cache
 */
class MW_Cache_DB
	extends MW_Cache_Abstract
	implements MW_Cache_Interface
{
	private $_sql;
	private $_dbm;
	private $_dbname;
	private $_siteid;
	private $_searchConfig;


	/**
	 * Initializes the object instance.
	 *
	 * The config['search] array must contain these key/array pairs suitable for MW_Common_Criteria_Attribute_Default:
	 *	[cache.id] => Array containing the codes/types/labels for the unique ID
	 *	[cache.siteid] => Array containing the codes/types/labels for the site ID
	 *	[cache.value] => Array containing the codes/types/labels for the cached value
	 *	[cache.expire] => Array containing the codes/types/labels for the expiration date
	 *	[cache.tag.name] => Array containing the codes/types/labels for the tag name
	 *
	 * The config['sql] array must contain these statement:
	 *	[delete] =>
	 *		DELETE FROM cachetable WHERE siteid = ? AND :cond
	 *	[deletebytag] =>
	 *		DELETE FROM cachetable WHERE siteid = ? AND id IN (
	 *			SELECT tid FROM cachetagtable WHERE tsiteid = ? AND :cond
	 *		)
	 *	[get] =>
	 *		SELECT id, value, expire FROM cachetable WHERE siteid = ? AND :cond
	 *	[getbytag] =>
	 *		SELECT id, value, expire FROM cachetable
	 *		JOIN cachetagtable ON tid = id
	 *		WHERE siteid = ? AND tsiteid = ? AND :cond
	 *	[set] =>
	 *		INSERT INTO cachetable ( id, siteid, expire, value ) VALUES ( ?, ?, ?, ? )
	 *	[settag] =>
	 *		INSERT INTO cachetagtable ( tid, tsiteid, tname ) VALUES ( ?, ?, ? )
	 *
	 * For using a different database connection, the name of the database connection
	 * can be also given in the "config" parameter. In this case, use e.g.
	 *  config['dbname'] = 'db-cache'
	 *
	 * If a site ID is given, the cache is partitioned for different
	 * sites. This also includes access control so cached values can be only
	 * retrieved from the same site. Specify a site ID with
	 *  config['siteid'] = 123
	 *
	 * @param array $config Associative list with SQL statements, search attribute definitions and database name
	 * @param MW_DB_Manager_Interface $dbm Database manager
	 */
	public function __construct( array $config, MW_DB_Manager_Interface $dbm )
	{
		if( !isset( $config['search'] ) ) {
			throw new MW_Cache_Exception( 'Search config is missing' );
		}

		if( !isset( $config['sql'] ) ) {
			throw new MW_Cache_Exception( 'SQL config is missing' );
		}

		$this->_checkSearchConfig( $config['search'] );
		$this->_checkSqlConfig( $config['sql'] );

		$this->_dbname = ( isset( $config['dbname'] ) ? $config['dbname'] : 'db' );
		$this->_siteid = ( isset( $config['siteid'] ) ? $config['siteid'] : null );
		$this->_searchConfig = $config['search'];
		$this->_sql = $config['sql'];
		$this->_dbm = $dbm;
	}


	/**
	 * Removes all expired cache entries.
	 *
	 * @inheritDoc
	 *
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function cleanup()
	{
		$conn = $this->_dbm->acquire( $this->_dbname );

		try
		{
			$date = date( 'Y-m-d H:i:00' );
			$search = new MW_Common_Criteria_SQL( $conn );
			$search->setConditions( $search->compare( '<', $this->_searchConfig['cache.expire']['code'], $date ) );

			$types = $this->_getSearchTypes( $this->_searchConfig );
			$translations = $this->_getSearchTranslations( $this->_searchConfig );
			$conditions = $search->getConditionString( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->_sql['delete'] ) );
			$stmt->bind( 1, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$this->_dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Removes the cache entries identified by the given keys.
	 *
	 * @inheritDoc
	 *
	 * @param array $keys List of key strings that identify the cache entries
	 * 	that should be removed
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function deleteList( array $keys )
	{
		$conn = $this->_dbm->acquire( $this->_dbname );

		try
		{
			$search = new MW_Common_Criteria_SQL( $conn );
			$search->setConditions( $search->compare( '==', $this->_searchConfig['cache.id']['code'], $keys ) );

			$types = $this->_getSearchTypes( $this->_searchConfig );
			$translations = $this->_getSearchTranslations( $this->_searchConfig );
			$conditions = $search->getConditionString( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->_sql['delete'] ) );
			$stmt->bind( 1, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$this->_dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Removes the cache entries identified by the given tags.
	 *
	 * @inheritDoc
	 *
	 * @param array $tags List of tag strings that are associated to one or more
	 * 	cache entries that should be removed
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function deleteByTags( array $tags )
	{
		$conn = $this->_dbm->acquire( $this->_dbname );

		try
		{
			$search = new MW_Common_Criteria_SQL( $conn );
			$search->setConditions( $search->compare( '==', $this->_searchConfig['cache.tag.name']['code'], $tags ) );

			$types = $this->_getSearchTypes( $this->_searchConfig );
			$translations = $this->_getSearchTranslations( $this->_searchConfig );
			$conditions = $search->getConditionString( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->_sql['deletebytag'] ) );
			$stmt->bind( 1, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$this->_dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Removes all entries of the site from the cache.
	 *
	 * @inheritDoc
	 *
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function flush()
	{
		$conn = $this->_dbm->acquire( $this->_dbname );

		try
		{
			$stmt = $conn->create( str_replace( ':cond', '1', $this->_sql['delete'] ) );
			$stmt->bind( 1, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->execute()->finish();

			$this->_dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Returns the cached values for the given cache keys if available.
	 *
	 * @inheritDoc
	 *
	 * @param array $keys List of key strings for the requested cache entries
	 * @return array Associative list of key/value pairs for the requested cache
	 * 	entries. If a cache entry doesn't exist, neither its key nor a value
	 * 	will be in the result list
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function getList( array $keys )
	{
		$list = array();
		$conn = $this->_dbm->acquire( $this->_dbname );

		try
		{
			$search = new MW_Common_Criteria_SQL( $conn );
			$expires = array(
				$search->compare( '>', 'cache.expire', date( 'Y-m-d H:i:00' ) ),
				$search->compare( '==', 'cache.expire', null ),
			);
			$expr = array(
				$search->compare( '==', 'cache.id', $keys ),
				$search->combine( '||', $expires ),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$types = $this->_getSearchTypes( $this->_searchConfig );
			$translations = $this->_getSearchTranslations( $this->_searchConfig );
			$conditions = $search->getConditionString( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->_sql['get'] ) );
			$stmt->bind( 1, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$result = $stmt->execute();

			while( ( $row = $result->fetch() ) !== false ) {
				$list[ $row['id'] ] = $row['value'];
			}

			$this->_dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn, $this->_dbname );
			throw $e;
		}

		return $list;
	}


	/**
	 * Returns the cached keys and values associated to the given tags if available.
	 *
	 * @inheritDoc
	 *
	 * @param array $tags List of tag strings associated to the requested cache entries
	 * @return array Associative list of key/value pairs for the requested cache
	 * 	entries. If a tag isn't associated to any cache entry, nothing is returned
	 * 	for that tag
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function getListByTags( array $tags )
	{
		$list = array();
		$conn = $this->_dbm->acquire( $this->_dbname );

		try
		{
			$search = new MW_Common_Criteria_SQL( $conn );
			$expires = array(
				$search->compare( '>', 'cache.expire', date( 'Y-m-d H:i:00' ) ),
				$search->compare( '==', 'cache.expire', null ),
			);
			$expr = array(
				$search->compare( '==', 'cache.tag.name', $tags ),
				$search->combine( '||', $expires ),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$types = $this->_getSearchTypes( $this->_searchConfig );
			$translations = $this->_getSearchTranslations( $this->_searchConfig );
			$conditions = $search->getConditionString( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->_sql['getbytag'] ) );
			$stmt->bind( 1, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
			$result = $stmt->execute();

			while( ( $row = $result->fetch() ) !== false ) {
				$list[ $row['id'] ] = $row['value'];
			}

			$this->_dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn, $this->_dbname );
			throw $e;
		}

		return $list;
	}


	/**
	 * Adds or overwrites the given key/value pairs in the cache, which is much
	 * more efficient than setting them one by one using the set() method.
	 *
	 * @inheritDoc
	 *
	 * @param array $pairs Associative list of key/value pairs. Both must be
	 * 	a string
	 * @param array $tags Associative list of key/tag or key/tags pairs that should be
	 * 	associated to the values identified by their key. The value associated
	 * 	to the key can either be a tag string or an array of tag strings
	 * @param array $expires Associative list of key/datetime pairs.
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function setList( array $pairs, array $tags = array(), array $expires = array() )
	{
		$conn = $this->_dbm->acquire( $this->_dbname );

		try
		{
			$stmt = $conn->create( $this->_sql['set'], MW_DB_Connection_Abstract::TYPE_PREP );
			$stmtTag = $conn->create( $this->_sql['settag'], MW_DB_Connection_Abstract::TYPE_PREP );

			foreach( $pairs as $key => $value )
			{
				$date = ( isset( $expires[$key] ) ? $expires[$key] : null );

				$stmt->bind( 1, $key );
				$stmt->bind( 2, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
				$stmt->bind( 3, $date );
				$stmt->bind( 4, $value );
				$stmt->execute()->finish();

				if( isset( $tags[$key] ) )
				{
					foreach( $tags[$key] as $name )
					{
						$stmtTag->bind( 1, $key );
						$stmtTag->bind( 2, $this->_siteid, MW_DB_Statement_Abstract::PARAM_INT );
						$stmtTag->bind( 3, $name );
						$stmtTag->execute()->finish();
					}
				}
			}

			$this->_dbm->release( $conn, $this->_dbname );
		}
		catch( Exception $e )
		{
			$this->_dbm->release( $conn, $this->_dbname );
			throw $e;
		}
	}


	/**
	 * Checks if all required search configurations are available.
	 *
	 * @param array $config Associative list of search configurations
	 * @throws MW_Tree_Exception If one ore more search configurations are missing
	 */
	protected function _checkSearchConfig( array $config )
	{
		$required = array( 'cache.id', 'cache.siteid', 'cache.value', 'cache.expire', 'cache.tag.name' );

		foreach( $required as $key => $entry )
		{
			if( isset( $config[$entry] ) ) {
				unset( $required[$key] );
			}
		}

		if( count( $required ) > 0 )
		{
			$msg = 'Search config in given configuration are missing: "%1$s"';
			throw new MW_Cache_Exception( sprintf( $msg, implode( ', ', $required ) ) );
		}
	}


	/**
	 * Checks if all required SQL statements are available.
	 *
	 * @param array $config Associative list of SQL statements
	 * @throws MW_Tree_Exception If one ore more SQL statements are missing
	 */
	protected function _checkSqlConfig( array $config )
	{
		$required = array( 'delete', 'deletebytag', 'get', 'getbytag', 'set', 'settag' );

		foreach( $required as $key => $entry )
		{
			if( isset( $config[$entry] ) ) {
				unset( $required[$key] );
			}
		}

		if( count( $required ) > 0 )
		{
			$msg = 'SQL statements in given configuration are missing: "%1$s"';
			throw new MW_Cache_Exception( sprintf( $msg, implode( ', ', $required ) ) );
		}
	}
}
