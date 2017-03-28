<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Cache
 */


namespace Aimeos\MW\Cache;


/**
 * Database cache class.
 *
 * @package MW
 * @subpackage Cache
 */
class DB
	extends \Aimeos\MW\Cache\Base
	implements \Aimeos\MW\Cache\Iface
{
	private $sql;
	private $dbm;
	private $dbname;
	private $siteid;
	private $searchConfig;


	/**
	 * Initializes the object instance.
	 *
	 * The config['search] array must contain these key/array pairs suitable for \Aimeos\MW\Criteria\Attribute\Standard:
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
	 * @param \Aimeos\MW\DB\Manager\Iface $dbm Database manager
	 */
	public function __construct( array $config, \Aimeos\MW\DB\Manager\Iface $dbm )
	{
		if( !isset( $config['search'] ) ) {
			throw new \Aimeos\MW\Cache\Exception( 'Search config is missing' );
		}

		if( !isset( $config['sql'] ) ) {
			throw new \Aimeos\MW\Cache\Exception( 'SQL config is missing' );
		}

		$this->checkSearchConfig( $config['search'] );
		$this->checkSqlConfig( $config['sql'] );

		$this->dbname = ( isset( $config['dbname'] ) ? $config['dbname'] : 'db' );
		$this->siteid = ( isset( $config['siteid'] ) ? $config['siteid'] : null );
		$this->searchConfig = $config['search'];
		$this->sql = $config['sql'];
		$this->dbm = $dbm;
	}


	/**
	 * Removes all expired cache entries.
	 *
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function cleanup()
	{
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$date = date( 'Y-m-d H:i:00' );
			$search = new \Aimeos\MW\Criteria\SQL( $conn );
			$search->setConditions( $search->compare( '<', $this->searchConfig['cache.expire']['code'], $date ) );

			$types = $this->getSearchTypes( $this->searchConfig );
			$translations = $this->getSearchTranslations( $this->searchConfig );
			$conditions = $search->getConditionString( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->sql['delete'] ) );
			$stmt->bind( 1, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->execute()->finish();

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );
			throw $e;
		}
	}


	/**
	 * Removes the cache entries identified by the given keys.
	 *
	 * @param \Traversable|array $keys List of key strings that identify the cache entries
	 * 	that should be removed
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function deleteMultiple( $keys )
	{
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$search = new \Aimeos\MW\Criteria\SQL( $conn );
			$search->setConditions( $search->compare( '==', $this->searchConfig['cache.id']['code'], $keys ) );

			$types = $this->getSearchTypes( $this->searchConfig );
			$translations = $this->getSearchTranslations( $this->searchConfig );
			$conditions = $search->getConditionString( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->sql['delete'] ) );
			$stmt->bind( 1, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->execute()->finish();

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );
			throw $e;
		}
	}


	/**
	 * Removes the cache entries identified by the given tags.
	 *
	 * @param string[] $tags List of tag strings that are associated to one or more
	 * 	cache entries that should be removed
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function deleteByTags( array $tags )
	{
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$search = new \Aimeos\MW\Criteria\SQL( $conn );
			$search->setConditions( $search->compare( '==', $this->searchConfig['cache.tag.name']['code'], $tags ) );

			$types = $this->getSearchTypes( $this->searchConfig );
			$translations = $this->getSearchTranslations( $this->searchConfig );
			$conditions = $search->getConditionString( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->sql['deletebytag'] ) );
			$stmt->bind( 1, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->execute()->finish();

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );
			throw $e;
		}
	}


	/**
	 * Removes all entries of the site from the cache.
	 *
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function clear()
	{
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$stmt = $conn->create( str_replace( ':cond', '1=1', $this->sql['delete'] ) );
			$stmt->bind( 1, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->execute()->finish();

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );
			throw $e;
		}
	}


	/**
	 * Returns the cached values for the given cache keys if available.
	 *
	 * @param \Traversable|array $keys List of key strings for the requested cache entries
	 * @param mixed $default Default value to return for keys that do not exist
	 * @return array Associative list of key/value pairs for the requested cache
	 * 	entries. If a cache entry doesn't exist, neither its key nor a value
	 * 	will be in the result list
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function getMultiple( $keys, $default = null )
	{
		$list = [];
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$search = new \Aimeos\MW\Criteria\SQL( $conn );
			$expires = array(
				$search->compare( '>', 'cache.expire', date( 'Y-m-d H:i:00' ) ),
				$search->compare( '==', 'cache.expire', null ),
			);
			$expr = array(
				$search->compare( '==', 'cache.id', $keys ),
				$search->combine( '||', $expires ),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$types = $this->getSearchTypes( $this->searchConfig );
			$translations = $this->getSearchTranslations( $this->searchConfig );
			$conditions = $search->getConditionString( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->sql['get'] ) );
			$stmt->bind( 1, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$result = $stmt->execute();

			while( ( $row = $result->fetch() ) !== false ) {
				$list[ $row['id'] ] = $row['value'];
			}

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );
			throw $e;
		}

		foreach( $keys as $key )
		{
			if( !isset( $list[$key] ) ) {
				$list[$key] = $default;
			}
		}

		return $list;
	}


	/**
	 * Returns the cached keys and values associated to the given tags if available.
	 *
	 * @param string[] $tags List of tag strings associated to the requested cache entries
	 * @return array Associative list of key/value pairs for the requested cache
	 * 	entries. If a tag isn't associated to any cache entry, nothing is returned
	 * 	for that tag
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function getMultipleByTags( array $tags )
	{
		$list = [];
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$search = new \Aimeos\MW\Criteria\SQL( $conn );
			$expires = array(
				$search->compare( '>', 'cache.expire', date( 'Y-m-d H:i:00' ) ),
				$search->compare( '==', 'cache.expire', null ),
			);
			$expr = array(
				$search->compare( '==', 'cache.tag.name', $tags ),
				$search->combine( '||', $expires ),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			$types = $this->getSearchTypes( $this->searchConfig );
			$translations = $this->getSearchTranslations( $this->searchConfig );
			$conditions = $search->getConditionString( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->sql['getbytag'] ) );
			$stmt->bind( 1, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$result = $stmt->execute();

			while( ( $row = $result->fetch() ) !== false ) {
				$list[ $row['id'] ] = $row['value'];
			}

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );
			throw $e;
		}

		return $list;
	}


	/**
	 * Adds or overwrites the given key/value pairs in the cache, which is much
	 * more efficient than setting them one by one using the set() method.
	 *
	 * @param \Traversable|array $pairs Associative list of key/value pairs. Both must be
	 * 	a string
	 * @param array|int|string|null $expires Associative list of keys and datetime
	 *  string or integer TTL pairs.
	 * @param array $tags Associative list of key/tag or key/tags pairs that
	 *  should be associated to the values identified by their key. The value
	 *  associated to the key can either be a tag string or an array of tag strings
	 * @return null
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function setMultiple( $pairs, $expires = null, array $tags = [] )
	{
		// Remove existing entries first to avoid duplicate key conflicts
		$this->deleteMultiple( array_keys( $pairs ) );

		$type = ( count( $pairs ) > 1 ? \Aimeos\MW\DB\Connection\Base::TYPE_PREP : \Aimeos\MW\DB\Connection\Base::TYPE_SIMPLE );
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$conn->begin();
			$stmt = $conn->create( $this->sql['set'], $type );
			$stmtTag = $conn->create( $this->sql['settag'], \Aimeos\MW\DB\Connection\Base::TYPE_PREP );

			foreach( $pairs as $key => $value )
			{
				$date = ( is_array( $expires ) && isset( $expires[$key] ) ? $expires[$key] : $expires );

				if( is_int( $date ) ) {
					$date = date( 'Y-m-d H:i:s', time() + $date );
				}

				$stmt->bind( 1, $key );
				$stmt->bind( 2, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 3, $date );
				$stmt->bind( 4, $value );
				$stmt->execute()->finish();

				if( isset( $tags[$key] ) )
				{
					foreach( (array) $tags[$key] as $name )
					{
						$stmtTag->bind( 1, $key );
						$stmtTag->bind( 2, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
						$stmtTag->bind( 3, $name );
						$stmtTag->execute()->finish();
					}
				}
			}

			$conn->commit();
			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$conn->rollback();
			$this->dbm->release( $conn, $this->dbname );
			throw $e;
		}
	}


	/**
	 * Checks if all required search configurations are available.
	 *
	 * @param array $config Associative list of search configurations
	 * @throws \Aimeos\MW\Tree\Exception If one ore more search configurations are missing
	 */
	protected function checkSearchConfig( array $config )
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
			throw new \Aimeos\MW\Cache\Exception( sprintf( $msg, implode( ', ', $required ) ) );
		}
	}


	/**
	 * Checks if all required SQL statements are available.
	 *
	 * @param array $config Associative list of SQL statements
	 * @throws \Aimeos\MW\Tree\Exception If one ore more SQL statements are missing
	 */
	protected function checkSqlConfig( array $config )
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
			throw new \Aimeos\MW\Cache\Exception( sprintf( $msg, implode( ', ', $required ) ) );
		}
	}
}
