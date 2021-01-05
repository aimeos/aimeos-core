<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MW
 * @subpackage Cache
 */


namespace Aimeos\MW\Cache;


/**
 * Generic interface for cacheing classes.
 *
 * @package MW
 * @subpackage Cache
 */
interface Iface
{
	/**
	 * Removes all expired cache entries.
	 *
	 * This method should be called regularly to delete entries from the cache
	 * that are not valid any more because they have been already expired.
	 *
	 * The interval of calling this method depends on the expiration of the
	 * entries. If most of your cache entires expire after midnight, it's a good
	 * idea to clean up the cache a few minutes afterwards.
	 *
	 * When the expiration occurs randomly distributed over the whole day, then
	 * a clean up every half hour, hour, two hours, etc. is better. The amount
	 * of time between each cleanup() call depends on the size of your cache and
	 * the amount of expired entries. The more entries has been expired, the more
	 * often cleanup() should be called. But if your cache size is very big it
	 * might be a good idea to remove the cache entries at a time of less
	 * activity because cleaning up the cache can be a long running task.
	 *
	 * Implementations for cache servers that care about expiration themselves
	 * simply do nothing and return immediately.
	 *
	 * @return bool True on success and false on failure
	 */
	public function cleanup() : bool;


	/**
	 * Removes all entries of the site from the cache.
	 *
	 * This method deletes all cached entries of a site from the cache server
	 * the client has access to. This method is primarily usefull to provide a
	 * clean start before new entries are added to the cache and you don't know
	 * which entries are still in the cache.
	 *
	 * @return bool True on success and false on failure
	 */
	public function clear() : bool;


	/**
	 * Removes the cache entry identified by the given key.
	 *
	 * To remove a single entry from the cache, use
	 *
	 * <code>
	 * $cache->delete( 'product/id/100' );
	 * </code>
	 *
	 * If the key doesn't exist in the cache, nothing happens and the method
	 * returns in the same way as if the key was found.
	 *
	 * When multiple keys should be deleted, use deleteMultiple() instead as it can
	 * delete the keys much faster by combining them into one request.
	 *
	 * @param string $key Key string that identifies the single cache entry
	 * @return bool True if the item was successfully removed. False if there was an error
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function delete( string $key ) : bool;


	/**
	 * Removes the cache entries identified by the given keys.
	 *
	 * Several cache entries can be removed at once with deleteMultiple():
	 *
	 * <code>
	 * $keys = array(
	 * 	'product/id/100',
	 * 	'product/id/101',
	 * );
	 * $cache->deleteMultiple( $keys );
	 * </code>
	 *
	 * If one of the keys is not part of the cache, it's ignored and no error
	 * or warning occurs.
	 *
	 * This is much faster than deleting them one by one as they are combined
	 * into a single request.
	 *
	 * @param iterable $keys List of key strings that identify the cache entries that should be removed
	 * @return bool True if the items were successfully removed. False if there was an error.
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function deleteMultiple( iterable $keys ) : bool;


	/**
	 * Removes the cache entries identified by the given tags.
	 *
	 * If cache entries are tagged by one or more stings, these entries can also
	 * be deleted by their tags:
	 *
	 * <code>
	 * $tags = array(
	 * 	'product/code/abc',
	 * 	'product/code/def',
	 * );
	 * $cache->deleteByTags( $tags );
	 * </code>
	 *
	 * If no cache entry is tagged by one of the given tags, nothing will happen
	 * and no error or warning occurs.
	 *
	 * One tag can be associated to several cache entries, so tags are a fast
	 * way of deleting many entries at once that share the same tags. This is
	 * extremely handy if e.g. all cached entries that relates to one product
	 * should be deleted because the product has changed.
	 *
	 * @param iterable $tags List of tag strings that are associated to one or
	 *  more cache entries that should be removed
	 * @return bool True if the items were successfully removed. False if there was an error.
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function deleteByTags( iterable $tags ) : bool;


	/**
	 * Returns the cached value for the given key.
	 *
	 * To fetch an entry from the cache server, call the get() method with the
	 * key of the cached entry:
	 *
	 * <code>
	 * $result = $cache->get( 'product/id/100' );
	 * </code>
	 *
	 * In case you need to retrieve several cached entries, please use getMultiple()
	 * instead. It can combine fetching the entries into one request and saves
	 * the round trip time of a second or all further requests.
	 *
	 * If the key doesn't exist in the cache, null is returned by default. To
	 * return an alternative default value, please use the second parameter:
	 *
	 * <code>
	 * $result = $cache->get( 'product/id/100', '' );
	 * </code>
	 *
	 * This would return an empty string if the key isn't found in the cache.
	 * You can use any type for the default value, even objects or resources.
	 *
	 * The default value should not be something that requires a lot of time to
	 * be created. Neither should it be the same that is expected to be returned
	 * by the get() method from the cache. In both cases, it would render the
	 * caching useless as the required amount of time for generating the content
	 * and asking the server adds up. Instead, use a check, generate and store
	 * workflow:
	 *
	 * <code>
	 * if( ( $result = $cache->get( 'product/id/100' ) ) === null )
	 * {
	 * 	$result = generateContent();
	 * 	$cache->set( 'product/id/100', $result );
	 * }
	 * </code>
	 *
	 * @param string $key Path to the requested value like product/id/123
	 * @param mixed $default Value returned if requested key isn't found
	 * @return mixed Value associated to the requested key. If no value for the
	 *	key is found in the cache, the given default value is returned
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function get( string $key, $default = null );


	/**
	 * Returns the cached values for the given cache keys if available.
	 *
	 * Several cached entries can be fetched at once using getMultiple(), which is
	 * extremely useful to save round trip times:
	 *
	 * <code>
	 * $keys = array(
	 * 	'product/id/100',
	 * 	'product/id/101',
	 * );
	 * $result = $cache->getMultiple( $keys );
	 *
	 * // content of $result:
	 * array(
	 * 	'product/id/100' => '<cached string for product/id/100>',
	 * 	'product/id/101' => '<cached string for product/id/101>',
	 * );
	 * </code>
	 *
	 * The result is an associative array of the keys used in the first parameter
	 * and the strings stored in the cache for these keys. If one of the keys is
	 * not used in the cache, the key is ignored and won't be part of the result
	 * array. No error or warning is returned in this case. If none of the keys
	 * is found in the cache, an empty array is returned.
	 *
	 * @param iterable $keys List of key strings for the requested cache entries
	 * @param mixed $default Default value to return for keys that do not exist
	 * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function getMultiple( iterable $keys, $default = null ) : iterable;


	/**
	 * Determines whether an item is present in the cache.
	 *
	 * NOTE: It is recommended that has() is only to be used for cache warming type purposes
	 * and not to be used within your live applications operations for get/set, as this method
	 * is subject to a race condition where your has() will return true and immediately after,
	 * another script can remove it, making the state of your app out of date.
	 *
	 * @param string $key The cache item key
	 * @return bool True if cache entry is available, false if not
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function has( string $key ) : bool;


	/**
	 * Sets the value for the given key in the cache.
	 *
	 * Adding or overwriting a single cache entry can be done by calling the
	 * set() method with the key and the value parameters:
	 *
	 * <code>
	 * $cache->set( 'product/id/100', '<string with product details>' );
	 * </code>
	 *
	 * The keys must be strings and can contain any UTF-8 character. For the
	 * best compatibility ASCII characters and only a few special characters
	 * like ".", ":", "-" and "/" should be preferred and the first character
	 * should always be an char between a-z or A-Z. Try to use a consistent
	 * schema for naming, e.g. 'product/id/100/name' or 'user:1000:name'. The
	 * maximum allowed length for keys is 255 bytes. The best keys are short
	 * but descriptive.
	 *
	 * As only strings are allowed, list of values and objects must be serialized
	 * before they can be added to the cache. You can use json_encode() or
	 * the PHP serialize() method for this. Please remember to use json_decode()
	 * or unserialize() after retrieving the values for these keys to get back
	 * the list or object again. The maximum length of a value is 16 MB.
	 *
	 * Additionally, one or more tags can be associated to each key/value pair
	 * in the cache:
	 *
	 * <code>
	 * $tags = ['product/id/101', 'product/id/102'];
	 * $cache->set( 'product/id/100', '<string>', null, $tags );
	 * </code>
	 *
	 * In this case, the tag 'product/id/101' and 'product/id/102' would be
	 * associated to the key. This can be extremely handy if you have entries
	 * that relates to several other items and should be found with getListByTag()
	 * or deleted with deleteListByTag(). The maximum allowed length for tags
	 * is 255 bytes.
	 *
	 * You can also specify an expiration date for the key:
	 *
	 * <code>
	 * $cache->set( 'product/id/100', '<string>', '2100-01-01 12:00:00' );
	 * </code>
	 *
	 * In the example, 'product/id/100' would stay in the cache till Jan. 1, 2100
	 * at noon. The format of the date/time values is "YYYY-MM-DD HH:mm:ss" and
	 * the hour values are from 0-23. If no expiry date is given (a null value)
	 * then the cache entry will stay forever in the cache.
	 *
	 * You should keep the time on both servers in sync to get expected results.
	 * Also, the date/time values for expiration are sensitive to time zones.
	 * Make sure your web server and your cache server use the same time zone.
	 * Otherwise, cache entries will be dropped earlier or later than specified
	 * by the web server. The best option is to use UTC time zone on all servers
	 * especially if your visitors are accessing your content from all over the
	 * world. For example, you can use the date_default_timezone_set() function
	 * to set the timezone explicitely:
	 *
	 * <code>
	 * date_default_timezone_set( 'UTC' );
	 * </code>
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
	public function set( string $key, $value, $expires = null, iterable $tags = [] ) : bool;


	/**
	 * Adds or overwrites the given key/value pairs in the cache, which is much
	 * more efficient than setting them one by one using the set() method.
	 *
	 * In the simplest form, you can use this method to add or overwrite several
	 * key/value pairs at once in the cache:
	 *
	 * <code>
	 * $pairs = array(
	 * 	'product/id/100/name' => 'Product name for product with ID 100',
	 * 	'product/id/100/prices' => '{1:"10.00",5:"9.00",10:"7.50"}',
	 * 	'product/id/100/object' => '<output from serialize()>',
	 * );
	 * $cache->setMultiple( $pairs );
	 * </code>
	 *
	 * The keys must be strings and can contain any UTF-8 character. For the
	 * best compatibility ASCII characters and only a few special characters
	 * like ".", ":", "-" and "/" should be preferred and the first character
	 * should always be an char between a-z or A-Z. Try to use a consistent
	 * schema for naming, e.g. 'product/id/100/name' or 'user:1000:name'. The
	 * maximum allowed length for keys is 255 bytes. The best keys are short
	 * but descriptive.
	 *
	 * As only strings are allowed, list of values and objects must be serialized
	 * before they can be added to the cache. You can use json_encode() or
	 * the PHP serialize() method for this. Please remember to use json_decode()
	 * or unserialize() after retrieving the values for these keys to get back
	 * the list or object again. The maximum length of a value is 512 MB.
	 *
	 * You can also specify an expiration date by adding the date/time or TTL as
	 * value:
	 *
	 * <code>
	 * $cache->setMultiple( $pairs, '2100-01-01 00:00:00' );
	 * </code>
	 *
	 * In the example, all passed cache entries will expire at Jan. 1, 2100
	 * at noon.
	 *
	 * You should keep the time on both servers in sync to get expected results.
	 * Also, the date/time values for expiration are sensitive to time zones.
	 * Make sure your web server and your cache server use the same time zone.
	 * Otherwise, cache entries will be dropped earlier or later than specified
	 * by the web server. The best option is to use UTC time zone on all servers
	 * especially if your visitors are accessing your content from all over the
	 * world. For example, you can use the date_default_timezone_set() function
	 * to set the timezone explicitely:
	 *
	 * <code>
	 * date_default_timezone_set( 'UTC' );
	 * </code>
	 *
	 * Additionally, one or more tags can be associated to all given cache entries:
	 *
	 * <code>
	 * $cache->setMultiple( $pairs, null, ['product-100', 'price'], );
	 * </code>
	 *
	 * In this case, 'product-100' and 'price' would be associated to all cache
	 * entries. The maximum allowed length for tags is 255 bytes.
	 *
	 * @param iterable $pairs Associative list of key/value pairs. Both must be a string
	 * @param \DateInterval|int|string|null $expires Date interval object,
	 *  date/time string in "YYYY-MM-DD HH:mm:ss" format or as integer TTL value
	 *  when the cache entry will expiry
	 * @param iterable $tags List of tags that should be associated to the cache entries
	 * @return bool True on success and false on failure.
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function setMultiple( iterable $pairs, $expires = null, iterable $tags = [] ) : bool;
}
