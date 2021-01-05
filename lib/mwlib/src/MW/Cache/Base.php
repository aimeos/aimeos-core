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
	 * @return bool True on success and false on failure
	 */
	public function cleanup() : bool
	{
		return true;
	}


	/**
	 * Removes the cache entry identified by the given key.
	 *
	 * @param string $key Key string that identifies the single cache entry
	 * @return bool True if the item was successfully removed. False if there was an error
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function delete( string $key ) : bool
	{
		return $this->deleteMultiple( [$key] );
	}


	/**
	 * Returns the cached value for the given key.
	 *
	 * @param string $key Path to the requested value like product/id/123
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key. If no value for the
	 *	key is found in the cache, the given default value is returned
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function get( string $key, $default = null )
	{
		$list = $this->getMultiple( [$key] );

		if( $list instanceof \Iterator ) {
			return $list->current();
		} elseif( is_array( $list ) && ( $value = current( $list ) ) !== false ) {
			return $value;
		}

		return $default;
	}


	/**
	 * Sets the value for the given key in the cache.
	 *
	 * @param string $key Key string for the given value like product/id/123
	 * @param mixed $value Value string that should be stored for the given key
	 * @param \DateInterval|int|string|null $expires Date interval object,
	 *  date/time string in "YYYY-MM-DD HH:mm:ss" format or as integer TTL value
	 *  when the cache entry will expiry
	 * @param iterable $tags List of tag strings that should be assoicated to the cache entry
	 * @return bool True on success and false on failure.
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function set( string $key, $value, $expires = null, iterable $tags = [] ) : bool
	{
		return $this->setMultiple( [$key => $value], $expires, $tags );
	}
}
