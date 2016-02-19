<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Cache
 */


namespace Aimeos\MW\Cache;


/**
 * Common class for all cache classes.
 *
 * @package MW
 * @subpackage Cache
 */
abstract class Base
	extends \Aimeos\MW\Common\Manager\Base
	implements \Aimeos\MW\Cache\Iface
{
	/**
	 * Removes all expired cache entries.
	 *
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function cleanup()
	{
	}


	/**
	 * Removes the cache entry identified by the given key.
	 *
	 * @param string $key Key string that identifies the single cache entry
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function delete( $key )
	{
		$this->deleteList( array( $key ) );
	}


	/**
	 * Returns the cached value for the given key.
	 *
	 * @param string $key Path to the requested value like product/id/123
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key. If no value for the
	 * key is found in the cache, the given default value is returned
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function get( $key, $default = null )
	{
		$list = $this->getList( array( $key ) );

		if( ( $value = reset( $list ) ) !== false ) {
			return $value;
		}

		return $default;
	}


	/**
	 * Sets the value for the given key in the cache.
	 *
	 * @param string $key Key string for the given value like product/id/123
	 * @param string $value Value string that should be stored for the given key
	 * @param string[] $tags List of tag strings that should be assoicated to the
	 * 	given value in the cache
	 * @param string|null $expires Date/time string in "YYYY-MM-DD HH:mm:ss"
	 * 	format when the cache entry expires
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function set( $key, $value, array $tags = array(), $expires = null )
	{
		if( !is_string( $key ) ) {
			throw new \Aimeos\MW\Cache\Exception( 'Key is not a string' );
		}

		$expireList = ( $expires !== null ? array( $key => $expires ) : array() );
		$this->setList( array( $key => $value ), array( $key => $tags ), $expireList );
	}
}
