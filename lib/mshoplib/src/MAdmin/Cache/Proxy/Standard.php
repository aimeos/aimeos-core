<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage MAdmin
 */


namespace Aimeos\MAdmin\Cache\Proxy;


/**
 * Cache proxy for creating cache object on demand.
 *
 * @package MAdmin
 * @subpackage Cache
 */
class Standard
	implements \Aimeos\MW\Cache\Iface
{
	private $object;
	private $context;


	/**
	 * Initializes the cache controller.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		$this->context = $context;
	}


	/**
	 * Removes all expired cache entries.
	 *
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function cleanup()
	{
		$this->getObject()->cleanup();
	}


	/**
	 * Removes all entries of the site from the cache.
	 *
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function clear()
	{
		$this->getObject()->clear();
	}


	/**
	 * Removes the cache entry identified by the given key.
	 *
	 * @param string $key Key string that identifies the single cache entry
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function delete( $key )
	{
		$this->getObject()->delete( $key );
	}


	/**
	 * Removes the cache entries identified by the given keys.
	 *
	 * @param string[] $keys List of key strings that identify the cache entries
	 * 	that should be removed
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function deleteMultiple( $keys )
	{
		$this->getObject()->deleteMultiple( $keys );
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
		$this->getObject()->deleteByTags( $tags );
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
		return $this->getObject()->get( $key, $default );
	}


	/**
	 * Returns the cached values for the given cache keys if available.
	 *
	 * @param string[] $keys List of key strings for the requested cache entries
	 * @param mixed $default Default value to return for keys that do not exist
	 * @return array Associative list of key/value pairs for the requested cache
	 * 	entries. If a cache entry doesn't exist, neither its key nor a value
	 * 	will be in the result list
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function getMultiple( $keys, $default = null )
	{
		return $this->getObject()->getMultiple( $keys, $default );
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
		return $this->getObject()->getMultipleByTags( $tags );
	}


	/**
	 * Sets the value for the given key in the cache.
	 *
	 * @param string $key Key string for the given value like product/id/123
	 * @param string $value Value string that should be stored for the given key
	 * @param int|string|null $expires Date/time string in "YYYY-MM-DD HH:mm:ss"
	 * 	format when the cache entry expires
	 * @param string[] $tags List of tag strings that should be assoicated to the
	 * 	given value in the cache
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function set( $key, $value, $expires = null, array $tags = [] )
	{
		$this->getObject()->set( $key, $value, $expires, $tags );
	}


	/**
	 * Adds or overwrites the given key/value pairs in the cache, which is much
	 * more efficient than setting them one by one using the set() method.
	 *
	 * @param array $pairs Associative list of key/value pairs. Both must be
	 * 	a string
	 * @param array|int|string|null $expires Associative list of keys and datetime
	 *  string or integer TTL pairs.
	 * @param string[] $tags Associative list of key/tag or key/tags pairs that should be
	 * 	associated to the values identified by their key. The value associated
	 * 	to the key can either be a tag string or an array of tag strings
	 * @throws \Aimeos\MW\Cache\Exception If the cache server doesn't respond
	 */
	public function setMultiple( $pairs, $expires = null, array $tags = [] )
	{
		$this->getObject()->setMultiple( $pairs, $expires, $tags );
	}


	/**
	 * Returns the cache object or creates a new one if it doesn't exist yet.
	 *
	 * @return \Aimeos\MW\Cache\Iface Cache object
	 */
	protected function getObject()
	{
		if( !isset( $this->object ) ) {
			$this->object = \Aimeos\MAdmin\Cache\Manager\Factory::createManager( $this->context )->getCache();
		}

		return $this->object;
	}
}
