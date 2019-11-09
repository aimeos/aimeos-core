<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MW
 * @subpackage Cache
 */


namespace Aimeos\MW\Cache;


/**
 * MySQL database cache class.
 *
 * @package MW
 * @subpackage Cache
 */
class Mysql
	extends \Aimeos\MW\Cache\DB
	implements \Aimeos\MW\Cache\Iface
{
	private $sql;
	private $dbm;
	private $dbname;
	private $siteid;


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
	 *		INSERT INTO cachetable ( id, siteid, expire, value ) VALUES ( ?, ?, ?, ? ) ON DUPLICATE KEY UPDATE
	 *	[settag] =>
	 *		INSERT INTO cachetagtable ( tid, tsiteid, tname ) VALUES :tuples ON DUPLICATE KEY UPDATE
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
		parent::__construct( $config, $dbm );

		$this->dbname = ( isset( $config['dbname'] ) ? $config['dbname'] : 'db' );
		$this->siteid = ( isset( $config['siteid'] ) ? $config['siteid'] : null );
		$this->sql = $config['sql'];
		$this->dbm = $dbm;
	}


	/**
	 * Adds or overwrites the given key/value pairs in the cache, which is much
	 * more efficient than setting them one by one using the set() method.
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
		$type = ( count( $pairs ) > 1 ? \Aimeos\MW\DB\Connection\Base::TYPE_PREP : \Aimeos\MW\DB\Connection\Base::TYPE_SIMPLE );
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$conn->begin();
			$stmt = $conn->create( $this->sql['set'], $type );

			foreach( $pairs as $key => $value )
			{
				if( $expires instanceof \DateInterval ) {
					$expires = date_create()->add( $expires )->format( 'Y-m-d H:i:s' );
				} elseif( is_int( $expires ) ) {
					$expires = date( 'Y-m-d H:i:s', time() + $expires );
				}

				$stmt->bind( 1, (string) $key );
				$stmt->bind( 2, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
				$stmt->bind( 3, $expires );
				$stmt->bind( 4, (string) $value );
				$stmt->execute()->finish();

				if( !empty( $tags ) )
				{
					$parts = [];
					$stmtTagPart = $conn->create( '( ?, ?, ? )' );

					foreach( $tags as $name )
					{
						$stmtTagPart->bind( 1, (string) $key );
						$stmtTagPart->bind( 2, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
						$stmtTagPart->bind( 3, (string) $name );

						$parts[] = (string) $stmtTagPart;
					}

					$stmtTag = $conn->create( str_replace( ':tuples', join( ',', $parts ), $this->sql['settag'] ) );
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
}
