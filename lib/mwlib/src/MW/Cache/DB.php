<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	private $searchConfig;


	/**
	 * Initializes the object instance.
	 *
	 * The config['search] array must contain these key/array pairs suitable for \Aimeos\MW\Criteria\Attribute\Standard:
	 *	[cache.id] => Array containing the codes/types/labels for the unique ID
	 *	[cache.value] => Array containing the codes/types/labels for the cached value
	 *	[cache.expire] => Array containing the codes/types/labels for the expiration date
	 *	[cache.tag.name] => Array containing the codes/types/labels for the tag name
	 *
	 * The config['sql] array must contain these statement:
	 *	[delete] =>
	 *		DELETE FROM cachetable WHERE :cond
	 *	[deletebytag] =>
	 *		DELETE FROM cachetable WHERE id IN (
	 *			SELECT tid FROM cachetagtable WHERE :cond
	 *		)
	 *	[get] =>
	 *		SELECT id, value, expire FROM cachetable WHERE :cond
	 *	[set] =>
	 *		INSERT INTO cachetable ( id, expire, value ) VALUES ( ?, ?, ? )
	 *	[settag] =>
	 *		INSERT INTO cachetagtable ( tid, tname ) VALUES ( ?, ? )
	 *
	 * For using a different database connection, the name of the database connection
	 * can be also given in the "config" parameter. In this case, use e.g.
	 *  config['dbname'] = 'db-cache'
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
		$this->searchConfig = $config['search'];
		$this->sql = $config['sql'];
		$this->dbm = $dbm;
	}


	/**
	 * Removes all expired cache entries.
	 *
	 * @inheritDoc
	 *
	 * @return bool True on success and false on failure
	 */
	public function cleanup() : bool
	{
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$date = date( 'Y-m-d H:i:00' );
			$search = new \Aimeos\MW\Criteria\SQL( $conn );
			$search->setConditions( $search->compare( '<', $this->searchConfig['cache.expire']['code'], $date ) );

			$types = $this->getSearchTypes( $this->searchConfig );
			$translations = $this->getSearchTranslations( $this->searchConfig );
			$conditions = $search->getConditionSource( $types, $translations );

			$conn->create( str_replace( ':cond', $conditions, $this->sql['delete'] ) )->execute()->finish();

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );

			error_log( __METHOD__ . ': ' . $e->getMessage() );
			return false;
		}

		return true;
	}


	/**
	 * Removes all entries of the site from the cache.
	 *
	 * @inheritDoc
	 *
	 * @return bool True on success and false on failure
	 */
	public function clear() : bool
	{
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$conn->create( str_replace( ':cond', '1=1', $this->sql['delete'] ) )->execute()->finish();
			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );

			error_log( __METHOD__ . ': ' . $e->getMessage() );
			return false;
		}

		return true;
	}


	/**
	 * Removes the cache entries identified by the given keys.
	 *
	 * @inheritDoc
	 *
	 * @param iterable $keys List of key strings that identify the cache entries that should be removed
	 * @return bool True if the items were successfully removed. False if there was an error.
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function deleteMultiple( iterable $keys ) : bool
	{
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$search = new \Aimeos\MW\Criteria\SQL( $conn );
			$search->setConditions( $search->compare( '==', $this->searchConfig['cache.id']['code'], $keys ) );

			$types = $this->getSearchTypes( $this->searchConfig );
			$translations = $this->getSearchTranslations( $this->searchConfig );
			$conditions = $search->getConditionSource( $types, $translations );

			$conn->create( str_replace( ':cond', $conditions, $this->sql['delete'] ) )->execute()->finish();

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );

			error_log( __METHOD__ . ': ' . $e->getMessage() );
			return false;
		}

		return true;
	}


	/**
	 * Removes the cache entries identified by the given tags.
	 *
	 * @inheritDoc
	 *
	 * @param iterable $tags List of tag strings that are associated to one or
	 *  more cache entries that should be removed
	 * @return bool True if the items were successfully removed. False if there was an error.
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function deleteByTags( iterable $tags ) : bool
	{
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$search = new \Aimeos\MW\Criteria\SQL( $conn );
			$search->setConditions( $search->compare( '==', $this->searchConfig['cache.tag.name']['code'], $tags ) );

			$types = $this->getSearchTypes( $this->searchConfig );
			$translations = $this->getSearchTranslations( $this->searchConfig );
			$conditions = $search->getConditionSource( $types, $translations );

			$stmt = $conn->create( str_replace( ':cond', $conditions, $this->sql['deletebytag'] ) )->execute()->finish();

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );

			error_log( __METHOD__ . ': ' . $e->getMessage() );
			return false;
		}

		return true;
	}


	/**
	 * Returns the cached values for the given cache keys if available.
	 *
	 * @inheritDoc
	 *
	 * @param string[] $keys List of key strings for the requested cache entries
	 * @param mixed $default Default value to return for keys that do not exist
	 * @return iterable Associative list of key/value pairs for the requested cache
	 * 	entries. If a cache entry doesn't exist, neither its key nor a value
	 * 	will be in the result list
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function getMultiple( iterable $keys, $default = null ) : iterable
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
				$search->or( $expires ),
			);
			$search->setConditions( $search->and( $expr ) );

			$types = $this->getSearchTypes( $this->searchConfig );
			$translations = $this->getSearchTranslations( $this->searchConfig );
			$conditions = $search->getConditionSource( $types, $translations );

			$result = $conn->create( str_replace( ':cond', $conditions, $this->sql['get'] ) )->execute();

			while( ( $row = $result->fetch() ) !== null ) {
				$list[$row['id']] = (string) $row['value'];
			}

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );
			error_log( __METHOD__ . ': ' . $e->getMessage() );
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
	 * Determines whether an item is present in the cache.
	 *
	 * @inheritDoc
	 *
	 * @param string $key The cache item key
	 * @return bool True if cache entry is available, false if not
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function has( string $key ) : bool
	{
		$return = false;
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$search = new \Aimeos\MW\Criteria\SQL( $conn );
			$expires = array(
				$search->compare( '>', 'cache.expire', date( 'Y-m-d H:i:00' ) ),
				$search->compare( '==', 'cache.expire', null ),
			);
			$expr = array(
				$search->compare( '==', 'cache.id', $key ),
				$search->or( $expires ),
			);
			$search->setConditions( $search->and( $expr ) );

			$types = $this->getSearchTypes( $this->searchConfig );
			$translations = $this->getSearchTranslations( $this->searchConfig );
			$conditions = $search->getConditionSource( $types, $translations );

			$result = $conn->create( str_replace( ':cond', $conditions, $this->sql['get'] ) )->execute();

			while( $result->fetch() !== null ) {
				$return = true;
			}

			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$this->dbm->release( $conn, $this->dbname );
			error_log( __METHOD__ . ': ' . $e->getMessage() );
		}

		return $return;
	}


	/**
	 * Adds or overwrites the given key/value pairs in the cache, which is much
	 * more efficient than setting them one by one using the set() method.
	 *
	 * @inheritDoc
	 *
	 * @param iterable $pairs Associative list of key/value pairs. Both must be a string
	 * @param \DateInterval|int|string|null $expires Date interval object,
	 *  date/time string in "YYYY-MM-DD HH:mm:ss" format or as integer TTL value
	 *  when the cache entry will expiry
	 * @param iterable $tags List of tags that should be associated to the cache entries
	 * @return bool True on success and false on failure.
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function setMultiple( iterable $pairs, $expires = null, iterable $tags = [] ) : bool
	{
		// Remove existing entries first to avoid duplicate key conflicts
		$this->deleteMultiple( array_keys( $pairs ) );

		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$conn->begin();
			$stmt = $conn->create( $this->sql['set'] );
			$stmtTag = $conn->create( $this->sql['settag'] );

			foreach( $pairs as $key => $value )
			{
				if( $expires instanceof \DateInterval ) {
					$expires = date_create()->add( $expires )->format( 'Y-m-d H:i:s' );
				} elseif( is_int( $expires ) ) {
					$expires = date( 'Y-m-d H:i:s', time() + $expires );
				}

				$stmt->bind( 1, (string) $key );
				$stmt->bind( 2, $expires );
				$stmt->bind( 3, (string) $value );
				$stmt->execute()->finish();

				foreach( $tags as $name )
				{
					$stmtTag->bind( 1, (string) $key );
					$stmtTag->bind( 2, (string) $name );
					$stmtTag->execute()->finish();
				}
			}

			$conn->commit();
			$this->dbm->release( $conn, $this->dbname );
		}
		catch( \Exception $e )
		{
			$conn->rollback();
			$this->dbm->release( $conn, $this->dbname );

			error_log( __METHOD__ . ': ' . $e->getMessage() );
			return false;
		}

		return true;
	}


	/**
	 * Checks if all required search configurations are available.
	 *
	 * @param array $config Associative list of search configurations
	 * @throws \Aimeos\MW\Tree\Exception If one ore more search configurations are missing
	 */
	protected function checkSearchConfig( array $config )
	{
		$required = array( 'cache.id', 'cache.value', 'cache.expire', 'cache.tag.name' );

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
		$required = array( 'delete', 'deletebytag', 'get', 'set', 'settag' );

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
