<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Cache
 */


/**
 * Common class for all cache classes.
 *
 * @package MW
 * @subpackage Cache
 */
abstract class MW_Cache_Abstract
	extends MW_Common_Manager_Abstract
	implements MW_Cache_Interface
{
	/**
	 * Removes all expired cache entries.
	 *
	 * @inheritDoc
	 *
	 * @throws MW_Cache_Exception If the cache server doesn't respond
	 */
	public function cleanup()
	{
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
		$this->deleteList( array( $key ) );
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
		$list = $this->getList( array( $key ) );

		if( ( $value = reset( $list ) ) !== false ) {
			return $value;
		}

		return $default;
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
		if( !is_string( $key ) ) {
			throw new MW_Cache_Exception( 'Key is not a string' );
		}

		$expireList = ( $expires !== null ? array( $key => $expires ) : array() );
		$this->setList( array( $key => $value ), array( $key => $tags ), $expireList );
	}


	/**
	 * Tests if caching is available.
	 *
	 * @inheritDoc
	 *
	 * @return boolean True if available, false if not
	 * @deprecated
	 */
	public function isAvailable()
	{
		return true;
	}
}
