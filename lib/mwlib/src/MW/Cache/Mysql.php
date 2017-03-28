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
		$type = ( count( $pairs ) > 1 ? \Aimeos\MW\DB\Connection\Base::TYPE_PREP : \Aimeos\MW\DB\Connection\Base::TYPE_SIMPLE );
		$conn = $this->dbm->acquire( $this->dbname );

		try
		{
			$conn->begin();
			$stmt = $conn->create( $this->sql['set'], $type );

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
					$parts = [];
					$stmtTagPart = $conn->create( '( ?, ?, ? )' );

					foreach( (array) $tags[$key] as $name )
					{
						$stmtTagPart->bind( 1, $key );
						$stmtTagPart->bind( 2, $this->siteid, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
						$stmtTagPart->bind( 3, $name );

						$parts[] = (string) $stmtTagPart;
					}

					if( !empty ( $parts ) )
					{
						$stmtTag = $conn->create( str_replace( ':tuples', join( ',', $parts ), $this->sql['settag'] ) );
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
}
