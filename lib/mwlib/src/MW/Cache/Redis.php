<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Cache
 */


/**
 * Redis cache class.
 *
 * @package MW
 * @subpackage Cache
 */
class MW_Cache_Redis
	extends MW_Cache_Abstract
	implements MW_Cache_Interface
{
	private $_client;


	/**
	 * Initializes the object instace.
	 *
	 * @param array $options Associative list of Redis options
	 */
	public function __construct( array $options )
	{
		if( !isset( $options['connection_async'] ) ) {
			$options['connection_async'] = true;
		}

		if( !isset( $options['connection_persistent'] ) ) {
			$options['connection_persistent'] = true;
		}

		if( !isset( $options['timeout'] ) ) {
			$options['timeout'] = 0.05; // 50ms
		}

		if( !isset( $options['read_write_timeout'] ) ) {
			$options['read_write_timeout'] = 0.05; // 50ms
		}

		$this->_client = new Predis\Client( $options );
	}


	/**
	 * Removes the cache entry identified by the given key.
	 *
	 * @inheritDoc
	 *
	 * @param string $key Key string that identifies the single cache entry
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function delete( $key )
	{
		$this->_client->del( $key );
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
		$this->_client->del( $keys );
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
		$result = array();
		$pipe = $this->_client->pipeline();

		foreach( $tags as $tag ) {
			$pipe->smembers( '_tag:' . $tag );
		}

		foreach( $pipe->execute() as $keys )
		{
			foreach( $keys as $key ) {
				$result[$key] = null;
			}
		}

		$this->_client->del( array_keys( $result ) );
	}


	/**
	 * Removes all entries from the cache so it's completely empty.
	 *
	 * @inheritDoc
	 *
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function flush()
	{
		$this->_client->flushdb();
	}


	/**
	 * Returns the cached value for the given key.
	 *
	 * @inheritDoc
	 *
	 * @param string $key Path to the requested value like product/id/123
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key. If no value for the
	 * key is found in the cache, the given default value is returned
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function get( $key, $default = null )
	{
		if( ( $result = $this->_client->get( $key ) ) === null ) {
			return $default;
		}

		return $result;
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
		$result = array();
		$keys = array_values( $keys );

		foreach( $this->_client->mget( $keys ) as $idx => $value )
		{
			if( isset( $keys[$idx] ) ) {
				$result[ $keys[$idx] ] = $value;
			}
		}

		return $result;
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
		$result = array();
		$pipe = $this->_client->pipeline();

		foreach( $tags as $tag ) {
			$pipe->smembers( '_tag:' . $tag );
		}

		foreach( $pipe->execute() as $keys )
		{
			foreach( $keys as $key ) {
				$result[$key] = null;
			}
		}

		$keys = array_keys( $result );

		foreach( $this->_client->mget( $keys ) as $idx => $value )
		{
			if( isset( $keys[$idx] ) ) {
				$result[ $keys[$idx] ] = $value;
			}
		}

		return $result;
	}


	/**
	 * Sets the value for the given key in the cache.
	 *
	 * @inheritDoc
	 *
	 * @param string $key Key string for the given value like product/id/123
	 * @param mixed $value Value string that should be stored for the given key
	 * @param array $tags List of tag strings that should be assoicated to the
	 * 	given value in the cache
	 * @param string|null $expires Date/time string in "YYYY-MM-DD HH:mm:ss"
	 * 	format when the cache entry expires
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function set( $key, $value, array $tags = array(), $expires = null )
	{
		$pipe = $this->_client->pipeline();
		$pipe->set( $key, $value );

		foreach( $tags as $tag ) {
			$pipe->sadd( '_tag:' . $tag, $key );
		}

		if( $expires !== null && ( $timestamp = strtotime( $expires ) ) !== false ) {
			$pipe->expireat( $key, $timestamp );
		}

		$pipe->execute();
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
		$pipe = $this->_client->pipeline();
		$pipe->mset( $pairs );

		foreach( $tags as $key => $tag ) {
			$pipe->sadd( '_tag:' . $tag, $key );
		}

		foreach( $expires as $key => $datetime ) {
			$pipe->expireat( $key, strtotime( $datetime ) );
		}

		$pipe->execute();
	}
}
