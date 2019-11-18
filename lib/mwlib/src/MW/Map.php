<?php

/**
 * @license MIT, http://opensource.org/licenses/MIT
 * @author Taylor Otwell, Aimeos.org developers
 * @package MW
 */


namespace Aimeos\MW;


/**
 * Handling and operating on a list of items easily
 * Inspired by Laravel Collection class, PHP map data structure and Javascript
 *
 * @package MW
 */
class Map implements MapIface
{
	protected static $methods = [];
	protected $items = [];


	/**
	 * Create a new map.
	 *
	 * @param iterable $items List of items
	 */
	public function __construct( iterable $items = [] )
	{
		$this->items = $this->getArray( $items );
	}


	/**
	 * Dynamically handle calls to the class.
	 *
	 * @param string $name Method name
	 * @param array $params List of parameters
	 * @return mixed Result from called function
	 *
	 * @throws \BadMethodCallException
	 */
	public static function __callStatic( string $name, array $params )
	{
		if( !isset( static::$methods[$name] ) ) {
			throw new \BadMethodCallException( sprintf( 'Method %s::%s does not exist.', static::class, $name ) );
		}

		return call_user_func_array( \Closure::bind( static::$methods[$name], null, static::class ), $params );
	}


	/**
	 * Dynamically handle calls to the class.
	 *
	 * @param string $name Method name
	 * @param array $params List of parameters
	 * @return mixed Result from called function
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call( string $name, array $params )
	{
		if( !isset( static::$methods[$name] ) ) {
			throw new \BadMethodCallException( sprintf( 'Method %s::%s does not exist.', static::class, $name ) );
		}

		return call_user_func_array( static::$methods[$name]->bindTo( $this, static::class ), $params );
	}


	/**
	 * Create a new map instance if the value isn't one already.
	 *
	 * @param iterable $items List of items
	 * @return MapIface New map
	 */
	public static function from( iterable $items = [] ) : MapIface
	{
		return new static( $items );
	}


	/**
	 * Register a custom method that has access to the class properties
	 *
	 * @param string $name Method name
	 * @param \Closure $function Anonymous method
	 */
	public static function method( string $name, \Closure $function )
	{
		static::$methods[$name] = $function;
	}


	/**
	 * Removes all items from the map.
	 *
	 * @return MapIface Same map for fluid interface
	 */
	public function clear() : MapIface
	{
		$this->items = [];
		return $this;
	}


	/**
	 * Return the values of a single column/property from an array of arrays or list of items.
	 *
	 * @inheritDoc
	 *
	 * @param string $valuecol Name of the value property
	 * @param string|null $indexcol Name of the index property
	 * @return MapIface New instance with mapped entries
	 */
	public function col( string $valuecol, $indexcol = null ) : MapIface
	{
		return new static( array_column( $this->items, $valuecol, $indexcol ) );
	}


	/**
	 * Push all of the given items onto the map.
	 *
	 * @param iterable $items List of items
	 * @return MapIface Updated map for fluid interface
	 */
	public function concat( iterable $items ) : MapIface
	{
		foreach( $items as $item ) {
			$this->items[] = $item;
		}

		return $this;
	}


	/**
	 * Creates a new map with the same items.
	 *
	 * @return MapIface New map
	 */
	public function copy() : MapIface
	{
		return new static( $this->items );
	}


	/**
	 * Count the number of items in the map.
	 *
	 * @return int Number of items
	 */
	public function count() : int
	{
		return count( $this->items );
	}


	/**
	 * Returns the keys/values in the map whose values are not present in the given items.
	 *
	 * @param iterable $items List of items
	 * @param  callable|null $callback Function with (valueA, valueB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @return MapIface New map
	 */
	public function diff( iterable $items, callable $callback = null ) : MapIface
	{
		if( $callback ) {
			return new static( array_udiff( $this->items, $this->getArray( $items ), $callback ) );
		}

		return new static( array_diff( $this->items, $this->getArray( $items ) ) );
	}


	/**
	 * Returns the keys/values in the map whose keys and values are not present in the given items.
	 *
	 * @param iterable $items List of items
	 * @param  callable|null $callback Function with (valueA, valueB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @return MapIface New map
	 */
	public function diffAssoc( iterable $items, callable $callback = null ) : MapIface
	{
		if( $callback ) {
			return new static( array_diff_uassoc( $this->items, $this->getArray( $items ), $callback ) );
		}

		return new static( array_diff_assoc( $this->items, $this->getArray( $items ) ) );
	}


	/**
	 * Returns the keys/values in the map whose keys are not present in the given items.
	 *
	 * @param iterable $items List of items
	 * @param  callable|null $callback Function with (keyA, keyB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @return MapIface New map
	 */
	public function diffKeys( iterable $items, callable $callback = null ) : MapIface
	{
		if( $callback ) {
			return new static( array_diff_ukey( $this->items, $this->getArray( $items ), $callback ) );
		}

		return new static( array_diff_key( $this->items, $this->getArray( $items ) ) );
	}


	/**
	 * Execute a callback over each item.
	 *
	 * @param callable $callback Function with (item, key) parameters and returns true/false
	 * @return MapIface Same map for fluid interface
	 */
	public function each( callable $callback ) : MapIface
	{
		foreach( $this->items as $key => $item )
		{
			if( $callback( $item, $key ) === false ) {
				break;
			}
		}

		return $this;
	}


	/**
	 * Run a filter over each of the items.
	 *
	 * @param  callable|null $callback Function with (item) parameter and returns true/false
	 * @return MapIface New map
	 */
	public function filter( callable $callback = null ) : MapIface
	{
		if( $callback ) {
			return new static( array_filter( $this->items, $callback, ARRAY_FILTER_USE_BOTH ) );
		}

		return new static( array_filter( $this->items ) );
	}



	/**
	 * Get the first item from the map passing the given truth test.
	 *
	 * @param callable|null $callback Function with (item, key) parameters and returns true/false
	 * @param mixed $default Default value if no item matches
	 * @return mixed First value of map or default value
	 */
	public function first( callable $callback = null, $default = null )
	{
		if( $callback )
		{
			foreach( $this->items as $key => $value )
			{
				if( $callback( $value, $key ) ) {
					return $value;
				}
			}

			return $default;
		}

		return reset( $this->items ) ?: $default;
	}


	/**
	 * Get an item from the map by key.
	 *
	 * @param mixed $key Key of the requested item
	 * @param mixed $default Default value if no item matches
	 * @return mixed Value from map or default value
	 */
	public function get( $key, $default = null )
	{
		return array_key_exists( $key, $this->items ) ? $this->items[$key] : $default;
	}


	/**
	 * Get an iterator for the items.
	 *
	 * @return \Iterator Over map items
	 */
	public function getIterator() : \Iterator
	{
		return new \ArrayIterator( $this->items );
	}


	/**
	 * Determine if an item exists in the map by key.
	 *
	 * @param mixed $key Key of the requested item
	 * @return bool True if key is available in map, false if not
	 */
	public function has( $key ) : bool
	{
		return array_key_exists( $key, $this->items );
	}


	/**
	 * Intersect the map with the given items.
	 *
	 * @param iterable $items List of items
	 * @param  callable|null $callback Function with (keyA, keyB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @return MapIface New map
	 */
	public function intersect( iterable $items, callable $callback = null ) : MapIface
	{
		if( $callback ) {
			return new static( array_uintersect( $this->items, $this->getArray( $items ), $callback ) );
		}

		return new static( array_intersect( $this->items, $this->getArray( $items ) ) );
	}


	/**
	 * Intersect the map with the given items by key.
	 *
	 * @param iterable $items List of items
	 * @param  callable|null $callback Function with (keyA, keyB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @return MapIface New map
	 */
	public function intersectAssoc( iterable $items, callable $callback = null ) : MapIface
	{
		if( $callback ) {
			return new static( array_uintersect_assoc( $this->items, $this->getArray( $items ), $callback ) );
		}

		return new static( array_intersect_assoc( $this->items, $this->getArray( $items ) ) );
	}


	/**
	 * Intersect the map with the given items by key.
	 *
	 * @param iterable $items List of items
	 * @param  callable|null $callback Function with (keyA, keyB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @return MapIface New map
	 */
	public function intersectKeys( iterable $items, callable $callback = null ) : MapIface
	{
		if( $callback ) {
			return new static( array_intersect_ukey( $this->items, $this->getArray( $items ), $callback ) );
		}

		return new static( array_intersect_key( $this->items, $this->getArray( $items ) ) );
	}


	/**
	 * Determine if the map is empty or not.
	 *
	 * @return bool True if map is empty, false if not
	 */
	public function isEmpty() : bool
	{
		return empty( $this->items );
	}


	/**
	 * Get the keys of the map items.
	 *
	 * @return MapIface New map
	 */
	public function keys() : MapIface
	{
		return new static( array_keys( $this->items ) );
	}


	/**
	 * Sort the map keys.
	 *
	 * @param callable|null $callback Function with (keyA, keyB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @param int $options Sort options for ksort()
	 * @return MapIface Updated map for fluid interface
	 */
	public function ksort( callable $callback = null, int $options = SORT_REGULAR ) : MapIface
	{
		$callback ? uksort( $this->items, $callback ) : ksort( $this->items, $options );
		return $this;
	}


	/**
	 * Get the last item from the map.
	 *
	 * @param callable|null $callback Function with (item, key) parameters and returns true/false
	 * @param mixed $default Default value if no item matches
	 * @return mixed Last value of map or default value
	 */
	public function last( callable $callback = null, $default = null )
	{
		if( $callback )
		{
			foreach( array_reverse( $this->items, true ) as $key => $value )
			{
				if( $callback( $value, $key ) ) {
					return $value;
				}
			}

			return $default;
		}

		return end( $this->items ) ?: $default;
	}


	/**
	 * Calls the provided function once for each element and constructs a new array from the results
	 *
	 * @param callable $callback Function with (item, key) parameters and returns computed result
	 * @return MapIface New map with the original keys and the computed values
	 */
	public function map( callable $callback ) : MapIface
	{
		$keys = array_keys( $this->items );
		$items = array_map( $callback, $this->items, $keys );

		return new static( array_combine( $keys, $items ) ?: [] );
	}


	/**
	 * Merge the map with the given items.
	 * Items with the same keys will be overwritten
	 *
	 * @param iterable $items List of items
	 * @return MapIface Updated map for fluid interface
	 */
	public function merge( iterable $items ) : MapIface
	{
		$this->items = array_merge( $this->items, $this->getArray( $items ) );
		return $this;
	}


	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param mixed $key Key to check for
	 * @return bool True if key exists, false if not
	 */
	public function offsetExists( $key )
	{
		return array_key_exists( $key, $this->items );
	}


	/**
	 * Get an item at a given offset.
	 *
	 * @param mixed $key Key to return the item for
	 * @return mixed Value associated to the given key
	 */
	public function offsetGet( $key )
	{
		return $this->items[$key];
	}


	/**
	 * Set the item at a given offset.
	 *
	 * @param mixed $key Key to set the item for
	 * @param mixed $value New value set for the key
	 */
	public function offsetSet( $key, $value )
	{
		if( $key !== null ) {
			$this->items[$key] = $value;
		} else {
			$this->items[] = $value;
		}
	}


	/**
	 * Unset the item at a given offset.
	 *
	 * @param string $key Key for unsetting the item
	 */
	public function offsetUnset( $key )
	{
		unset( $this->items[$key] );
	}


	/**
	 * Pass the map to the given callback and return the result.
	 *
	 * @param callable $callback Function with (map) parameter and returns arbitrary result
	 * @return mixed Result returned by the callback
	 */
	public function pipe( callable $callback )
	{
		return $callback( $this );
	}


	/**
	 * Get and remove the last item from the map.
	 *
	 * @return mixed Last element of the map or null if empty
	 */
	public function pop()
	{
		return array_pop( $this->items );
	}


	/**
	 * Get and remove an item from the map.
	 *
	 * @param mixed $key Key to retrieve the value for
	 * @param mixed $default Default value if key isn't available
	 * @return mixed Value from map or default value
	 */
	public function pull( $key, $default = null )
	{
		$value = $this->get( $key, $default );
		unset( $this->items[$key] );

		return $value;
	}


	/**
	 * Push an item onto the end of the map.
	 *
	 * @param mixed $value Value to add to the end
	 * @return MapIface Same map for fluid interface
	 */
	public function push( $value ) : MapIface
	{
		$this->items[] = $value;
		return $this;
	}


	/**
	 * Iteratively reduces the array to a single value using a callback function
	 *
	 * @param callable $callback Function with (result, item) parameters and returns result
	 * @param mixed $initial Initial value when computing the result
	 * @return mixed Value computed by the callback function
	 */
	public function reduce( callable $callback, $initial = null )
	{
		return array_reduce( $this->items, $callback, $initial );
	}


	/**
	 * Remove an item from the map by key.
	 *
	 * @param string|iterable $keys List of keys
	 * @return MapIface Same map for fluid interface
	 */
	public function remove( $keys ) : MapIface
	{
		foreach( (array) $keys as $key ) {
			unset( $this->items[$key] );
		}

		return $this;
	}


	/**
	 * Recursively replaces items in the map with the given items
	 *
	 * @param iterable $items List of items
	 * @return MapIface Updated map for fluid interface
	 */
	public function replace( iterable $items ) : MapIface
	{
		$this->items = array_replace_recursive( $this->items, $this->getArray( $items ) );
		return $this;
	}


	/**
	 * Reverse items order.
	 *
	 * @return MapIface Updated map for fluid interface
	 */
	public function reverse() : MapIface
	{
		$this->items = array_reverse( $this->items, true );
		return $this;
	}


	/**
	 * Search the map for a given value and return the corresponding key if successful.
	 *
	 * @param mixed $value Item to search for
	 * @param bool $strict True if type of the item should be checked too
	 * @return mixed|null Value from map or null if not found
	 */
	public function search( $value, $strict = true )
	{
		if( ( $result = array_search( $value, $this->items, $strict ) ) !== false ) {
			return $result;
		}

		return null;
	}


	/**
	 * Sets an item in the map by key.
	 *
	 * @param mixed $key Key to set the new value for
	 * @param mixed $value New item that should be set
	 * @return MapIface Same map for fluid interface
	 */
	public function set( $key, $value ) : MapIface
	{
		$this->items[$key] = $value;
		return $this;
	}


	/**
	 * Get and remove the first item from the map.
	 *
	 * @return mixed|null Value from map or null if not found
	 */
	public function shift()
	{
		return array_shift( $this->items );
	}


	/**
	 * Shuffle the items in the map.
	 *
	 * @return MapIface Updated map for fluid interface
	 */
	public function shuffle() : MapIface
	{
		shuffle( $this->items );
		return $this;
	}


	/**
	 * Slice the underlying map array.
	 *
	 * @param int $offset Number of items to start from
	 * @param int $length Number of items to return
	 * @return MapIface New map
	 */
	public function slice( int $offset, int $length = null ) : MapIface
	{
		return new static( array_slice( $this->items, $offset, $length, true ) );
	}


	/**
	 * Sort through each item with a callback.
	 *
	 * @param callable|null $callback Function with (itemA, itemB) parameters and returns -1 (<), 0 (=) and 1 (>)
	 * @param int $options Sort options for asort()
	 * @return MapIface Updated map for fluid interface
	 */
	public function sort( callable $callback = null, int $options = SORT_REGULAR ) : MapIface
	{
		$callback ? uasort( $this->items, $callback ) : asort( $this->items, $options );
		return $this;
	}


	/**
	 * Splice a portion of the underlying map array.
	 *
	 * @param int $offset Number of items to start from
	 * @param int|null $length Number of items to remove
	 * @param mixed $replacement List of items to insert
	 * @return MapIface New map
	 */
	public function splice( int $offset, int $length = null, $replacement = [] ) : MapIface
	{
		if( $length === null ) {
			return new static( array_splice( $this->items, $offset ) );
		}

		return new static( array_splice( $this->items, $offset, $length, $replacement ) );
	}


	/**
	 * Get the map of items as a plain array.
	 *
	 * @return array Plain array
	 */
	public function toArray() : array
	{
		return $this->items;
	}


	/**
	 * Union the map with the given items.
	 * Existing keys in the map will not be overwritten
	 *
	 * @param iterable $items List of items
	 * @return MapIface Updated map for fluid interface
	 */
	public function union( iterable $items ) : MapIface
	{
		$this->items += $this->getArray( $items );
		return $this;
	}


	/**
	 * Return only unique items from the map.
	 *
	 * @return MapIface New map
	 */
	public function unique() : MapIface
	{
		return new static( array_unique( $this->items ) );
	}


	/**
	 * Push an item onto the beginning of the map.
	 *
	 * @param mixed $value Item to add at the beginning
	 * @param mixed $key Key for the item
	 * @return MapIface Same map for fluid interface
	 */
	public function unshift( $value, $key = null ) : MapIface
	{
		if( $key === null ) {
			array_unshift( $this->items, $value );
		} else {
			$this->items = [$key => $value] + $this->items;
		}

		return $this;
	}


	/**
	 * Reset the keys on the underlying array.
	 *
	 * @return MapIface New map of the values
	 */
	public function values() : MapIface
	{
		return new static( array_values( $this->items ) );
	}


	/**
	 * Returns an array of the given items
	 *
	 * @param iterable $items List of items
	 * @return array Plain array
	 */
	protected function getArray( iterable $items ) : array
	{
		if( is_array( $items ) ) {
			return $items;
		} elseif( $items instanceof self ) {
			return $items->toArray();
		} elseif( $items instanceof \Traversable ) {
			return iterator_to_array( $items );
		}

		return (array) $items;
	}
}
