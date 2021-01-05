<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
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
	 * @inheritDoc
	 *
	 * @return bool True on success and false on failure
	 */
	public function cleanup() : bool
	{
		return $this->getObject()->cleanup();
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
		return $this->getObject()->clear();
	}


	/**
	 * Removes the cache entry identified by the given key.
	 *
	 * @inheritDoc
	 *
	 * @param string $key Key string that identifies the single cache entry
	 * @return bool True if the item was successfully removed. False if there was an error
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function delete( string $key ) : bool
	{
		return $this->getObject()->delete( $key );
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
		return $this->getObject()->deleteMultiple( $keys );
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
		return $this->getObject()->deleteByTags( $tags );
	}


	/**
	 * Returns the cached value for the given key.
	 *
	 * @inheritDoc
	 *
	 * @param string $key Path to the requested value like product/id/123
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key. If no value for the
	 *	key is found in the cache, the given default value is returned
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function get( string $key, $default = null )
	{
		return $this->getObject()->get( $key, $default );
	}


	/**
	 * Returns the cached values for the given cache keys if available.
	 *
	 * @inheritDoc
	 *
	 * @param iterable $keys List of key strings for the requested cache entries
	 * @param mixed $default Default value to return for keys that do not exist
	 * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function getMultiple( iterable $keys, $default = null ) : iterable
	{
		return $this->getObject()->getMultiple( $keys, $default );
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
		return $this->getObject()->has( $key );
	}


	/**
	 * Sets the value for the given key in the cache.
	 *
	 * @inheritDoc
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
		return $this->getObject()->set( $key, $value, $expires, $tags );
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
		return $this->getObject()->setMultiple( $pairs, $expires, $tags );
	}


	/**
	 * Returns the cache object or creates a new one if it doesn't exist yet.
	 *
	 * @return \Aimeos\MW\Cache\Iface Cache object
	 */
	protected function getObject()
	{
		if( !isset( $this->object ) ) {
			$this->object = \Aimeos\MAdmin::create( $this->context, 'cache' )->getCache();
		}

		return $this->object;
	}
}
