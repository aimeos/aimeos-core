<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MW
 * @subpackage Cache
 * @version $$
 */


namespace Aimeos\MW\Cache;


/**
 * Void caching implementation.
 *
 * @package MW
 * @subpackage Cache
 */
class None
	extends \Aimeos\MW\Cache\Base
	implements \Aimeos\MW\Cache\Iface
{
	/**
	 * Removes the cache entry identified by the given key.
	 *
	 * @param string $key Key string that identifies the single cache entry
	 */
	public function delete( $key )
	{

	}


	/**
	 * Removes the cache entries identified by the given keys.
	 *
	 * @param array $keys List of key strings that identify the cache entries
	 * 	that should be removed
	 */
	public function deleteList( array $keys )
	{

	}


	/**
	 * Removes the cache entries identified by the given tags.
	 *
	 * @param array $tags List of tag strings that are associated to one or more
	 * 	cache entries that should be removed
	 */
	public function deleteByTags( array $tags )
	{

	}


	/**
	 * Removes all entries from the cache so it's completely empty.
	 *
	 * This method deletes all cached entries from the cache server the client
	 * has access to. This method is primarily usefull to provide a clean start
	 * before new entries are added to the cache and you don't know which
	 * entries are still in the cache.
	 *
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function flush()
	{
	}


	/**
	 * Returns the value of the requested cache key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key
	 */
	public function get( $name, $default = null )
	{
		return $default;
	}


	/**
	 * Returns the cached values for the given cache keys.
	 *
	 * @param array $keys List of key strings for the requested cache entries
	 * @return array Associative list of key/value pairs for the requested cache
	 * 	entries. If a cache entry doesn't exist, neither its key nor a value
	 * 	will be in the result list
	 */
	public function getList( array $keys )
	{
		return array();
	}


	/**
	 * Returns the cached keys and values associated to the given tags.
	 *
	 * @param array $tags List of tag strings associated to the requested cache entries
	 * @return array Associative list of key/value pairs for the requested cache
	 * 	entries. If a tag isn't associated to any cache entry, nothing is returned
	 * 	for that tag
	 */
	public function getListByTags( array $tags )
	{
		return array();
	}


	/**
	 * Sets the value for the specified key.
	 *
	 * @param string $name Path to the requested value like tree/node/classname
	 * @param mixed $value Value that should be associated with the given path
	 * @param array $tags List of tag strings that should be assoicated to the
	 * 	given value in the cache
	 * @param string|null $expires Date/time string in "YYYY-MM-DD HH:mm:ss"
	 * 	format when the cache entry expires
	 */
	public function set( $name, $value, array $tags = array(), $expire = null )
	{
	}


	/**
	 * Adds the given key/value pairs to the cache.
	 *
	 * @param array $pairs Associative list of key/value pairs. Both must be
	 * 	a string
	 * @param array $expires Associative list of key/datetime pairs.
	 * @param array $tags Associative list of key/tag or key/tags pairs that should be
	 * 	associated to the values identified by their key. The value associated
	 * 	to the key can either be a tag string or an array of tag strings
	 */
	public function setList( array $pairs, array $tags = array(), array $expires = array() )
	{

	}
}
