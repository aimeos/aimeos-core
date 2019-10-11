<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @author Taylor Otwell, Aimeos.org developers
 * @package MW
 */


namespace Aimeos\MW;


/**
 * Handling and operating on an associative list of items easily
 * Inspired by Laravel Collection class, PHP map data structure and Javascript
 *
 * @package MW
 */
interface MapIface extends \ArrayAccess, \Countable, \IteratorAggregate
{
	/**
	 * Create a new map instance if the value isn't one already.
	 *
	 * @param iterable $items List of items
	 * @return MapIface New map
	 */
	public static function from( iterable $items = [] ) : MapIface;

	/**
	 * Register a custom method that has access to the class properties
	 *
	 * @param string $name Method name
	 * @param \Closure $function Anonymous method
	 */
	public static function method( string $name, \Closure $function );

	/**
	 * Removes all items from the map.
	 *
	 * @return MapIface Same map for fluid interface
	 */
	public function clear();

	/**
	 * Push all of the given items onto the map.
	 *
	 * @param iterable $items List of items
	 * @return MapIface Updated map for fluid interface
	 */
	public function concat( iterable $items ) : MapIface;

	/**
	 * Creates a new map with the same items.
	 *
	 * @return MapIface New map
	 */
	public function copy() : MapIface;

	/**
	 * Count the number of items in the map.
	 *
	 * @return int Number of items
	 */
	public function count() : int;

	/**
	 * Get the items in the map whose keys are not present in the given items.
	 *
	 * @param iterable $items List of items
	 * @param  callable|null $callback Function with (keyA, keyB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @return MapIface New map
	 */
	public function diff( iterable $items, callable $callback = null ) : MapIface;

	/**
	 * Get the items in the collection whose keys and values are not present in the given items.
	 *
	 * @param iterable $items List of items
	 * @return MapIface New map
	 */
	public function diffAssoc( iterable $items, callable $callback = null ) : MapIface;

	/**
	 * Get the items in the collection whose keys are not present in the given items.
	 *
	 * @param iterable $items List of items
	 * @return MapIface New map
	 */
	public function diffKeys( iterable $items, callable $callback = null ) : MapIface;

	/**
	 * Execute a callback over each item.
	 *
	 * @param callable $callback Function with (item, key) parameters and returns true/false
	 * @return MapIface Same map for fluid interface
	 */
	public function each( callable $callback ) : MapIface;

	/**
	 * Run a filter over each of the items.
	 *
	 * @param  callable|null $callback Function with (item) parameter and returns true/false
	 * @return MapIface New map
	 */
	public function filter( callable $callback = null ) : MapIface;

	/**
	 * Get the first item from the map passing the given truth test.
	 *
	 * @param callable|null $callback Function with (item, key) parameters and returns true/false
	 * @param mixed $default Default value if no item matches
	 * @return mixed First value of map or default value
	 */
	public function first( callable $callback = null, $default = null );

	/**
	 * Get an item from the map by key.
	 *
	 * @param mixed $key Key of the requested item
	 * @param mixed $default Default value if no item matches
	 * @return mixed Value from map or default value
	 */
	public function get( $key, $default = null );

	/**
	 * Get an iterator for the items.
	 *
	 * @return \Iterator Over map items
	 */
	public function getIterator() : \Iterator;

	/**
	 * Determine if an item exists in the map by key.
	 *
	 * @param mixed $key Key of the requested item
	 * @return bool True if key is available in map, false if not
	 */
	public function has( $key ) : bool;

	/**
	 * Intersect the map with the given items.
	 *
	 * @param iterable $items List of items
	 * @return MapIface New map
	 */
	public function intersect( iterable $items ) : MapIface;

	/**
	 * Intersect the map with the given items by key.
	 *
	 * @param iterable $items List of items
	 * @return MapIface New map
	 */
	public function intersectKeys( iterable $items ) : MapIface;

	/**
	 * Determine if the map is empty or not.
	 *
	 * @return bool True if map is empty, false if not
	 */
	public function isEmpty() : bool;

	/**
	 * Get the keys of the map items.
	 *
	 * @return MapIface New map
	 */
	public function keys() : MapIface;

	/**
	 * Sort the map keys.
	 *
	 * @param callable|null $callback Function with (keyA, keyB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @param int $options Sort options for ksort()
	 * @return MapIface Updated map for fluid interface
	 */
	public function ksort( callable $callback = null, int $options = SORT_REGULAR ) : MapIface;

	/**
	 * Get the last item from the map.
	 *
	 * @param callable|null $callback Function with (item, key) parameters and returns true/false
	 * @param mixed $default Default value if no item matches
	 * @return mixed Last value of map or default value
	 */
	public function last( callable $callback = null, $default = null );

	/**
	 * Calls the provided function once for each element and constructs a new array from the results
	 *
	 * @param callable $callback Function with (item, key) parameters and returns computed result
	 * @return MapIface New map with the original keys and the computed values
	 */
	public function map( callable $callback ) : MapIface;

	/**
	 * Merge the map with the given items.
	 * Items with the same keys will be overwritten
	 *
	 * @param iterable $items List of items
	 * @return MapIface Updated map for fluid interface
	 */
	public function merge( iterable $items ) : MapIface;

	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param mixed $key Key to check for
	 * @return bool True if key exists, false if not
	 */
	public function offsetExists( $key );

	/**
	 * Get an item at a given offset.
	 *
	 * @param mixed $key Key to return the item for
	 * @return mixed Value associated to the given key
	 */
	public function offsetGet( $key );

	/**
	 * Set the item at a given offset.
	 *
	 * @param mixed $key Key to set the item for
	 * @param mixed $value New value set for the key
	 */
	public function offsetSet( $key, $value );

	/**
	 * Unset the item at a given offset.
	 *
	 * @param string $key Key for unsetting the item
	 */
	public function offsetUnset( $key );

	/**
	 * Pass the map to the given callback and return the result.
	 *
	 * @param callable $callback Function with (map) parameter and returns arbitrary result
	 * @return mixed Result returned by the callback
	 */
	public function pipe( callable $callback );

	/**
	 * Get and remove the last item from the map.
	 *
	 * @return mixed Last element of the map or null if empty
	 */
	public function pop();

	/**
	 * Get and remove an item from the map.
	 *
	 * @param mixed $key Key to retrieve the value for
	 * @param mixed $default Default value if key isn't available
	 * @return mixed Value from map or default value
	 */
	public function pull( $key, $default = null );

	/**
	 * Push an item onto the end of the map.
	 *
	 * @param mixed $value Value to add to the end
	 * @return MapIface Same map for fluid interface
	 */
	public function push( $value ) : MapIface;

	/**
	 * Iteratively reduces the array to a single value using a callback function
	 *
	 * @param callable $callback Function with (result, item) parameters and returns result
	 * @param mixed $initial Initial value when computing the result
	 * @return mixed Value computed by the callback function
	 */
	public function reduce( callable $callback, $initial = null );

	/**
	 * Remove an item from the map by key.
	 *
	 * @param string|iterable $keys List of keys
	 * @return MapIface Updated map for fluid interface
	 */
	public function remove( $keys ) : MapIface;

	/**
	 * Recursively replaces items in the map with the given items
	 *
	 * @param iterable $items List of items
	 * @return MapIface Updated map for fluid interface
	 */
	public function replace( iterable $items ) : MapIface;

	/**
	 * Reverse items order.
	 *
	 * @return MapIface Updated map for fluid interface
	 */
	public function reverse() : MapIface;

	/**
	 * Search the map for a given value and return the corresponding key if successful.
	 *
	 * @param mixed $value Item to search for
	 * @param bool $strict True if type of the item should be checked too
	 * @return mixed Value from map or null if not found
	 */
	public function search( $value, $strict = true );

	/**
	 * Sets an item in the map by key.
	 *
	 * @param mixed $key Key to set the new value for
	 * @param mixed $value New item that should be set
	 * @return MapIface Same map for fluid interface
	 */
	public function set( $key, $value ) : MapIface;

	/**
	 * Get and remove the first item from the map.
	 *
	 * @return mixed Value from map or null if not found
	 */
	public function shift();

	/**
	 * Shuffle the items in the map.
	 *
	 * @return MapIface Updated map for fluid interface
	 */
	public function shuffle() : MapIface;

	/**
	 * Slice the underlying map array.
	 *
	 * @param int $offset Number of items to start from
	 * @param int $length Number of items to return
	 * @return MapIface New map
	 */
	public function slice( int $offset, int $length = null ) : MapIface;

	/**
	 * Sort through each item with a callback.
	 *
	 * @param callable|null $callback Function with (itemA, itemB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @param int $options Sort options for asort()
	 * @return MapIface Updated map for fluid interface
	 */
	public function sort( callable $callback = null, int $options = SORT_REGULAR ) : MapIface;

	/**
	 * Splice a portion of the underlying map array.
	 *
	 * @param int $offset Number of items to start from
	 * @param int|null $length Number of items to remove
	 * @param mixed $replacement List of items to insert
	 * @return MapIface New map
	 */
	public function splice( int $offset, int $length = null, $replacement = [] ) : MapIface;

	/**
	 * Get the map of items as a plain array.
	 *
	 * @return array Plain (multi dimensional) array
	 */
	public function toArray() : array;

	/**
	 * Union the map with the given items.
	 * Existing keys in the map will not be overwritten
	 *
	 * @param iterable $items List of items
	 * @return MapIface Updated map for fluid interface
	 */
	public function union( iterable $items ) : MapIface;

	/**
	 * Return only unique items from the map array.
	 *
	 * @return MapIface New map with duplicates removed
	 */
	public function unique() : MapIface;

	/**
	 * Push an item onto the beginning of the map.
	 *
	 * @param mixed $value Item to add at the beginning
	 * @param mixed $key Key for the item
	 * @return MapIface Same map for fluid interface
	 */
	public function unshift( $value, $key = null ) : MapIface;

	/**
	 * Reset the keys on the underlying array.
	 *
	 * @return MapIface New map of the values
	 */
	public function values() : MapIface;
}
